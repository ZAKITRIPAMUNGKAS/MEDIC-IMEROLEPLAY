<?php

namespace App\Http\Controllers;

use App\Models\CreditScore;
use App\Models\CreditScoreLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CreditScoreController extends Controller
{
    /**
     * Cek akses berdasarkan divisi:
     * - Comdis  : input poin (add/deduct)
     * - PND/IE  : lihat semua anggota
     * - Lainnya : hanya lihat skor sendiri
     */
    private function canManage(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isExecutiveOrAbove() || $user->isInDivision('comdis'));
    }

    private function canViewAll(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isExecutiveOrAbove() || $user->isInDivision('comdis', 'pnd', 'ie'));
    }

    /**
     * Pastikan tabel credit_scores dan credit_score_logs ada dan schema-nya kompatibel.
     */
    private function ensureTablesExist(): void
    {
        try {
            if (!Schema::hasTable('credit_scores')) {
                Schema::create('credit_scores', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->unique();
                    $table->integer('balance')->default(100);
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::warning('ensureTablesExist credit_scores notice: ' . $e->getMessage());
        }

        try {
            if (!Schema::hasTable('credit_score_logs')) {
                Schema::create('credit_score_logs', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->index();
                    $table->unsignedBigInteger('issued_by')->nullable()->index();
                    $table->integer('amount');
                    $table->integer('balance_after');
                    $table->text('reason')->nullable();
                    $table->string('type', 20)->default('add');
                    $table->timestamps();
                    $table->index(['user_id', 'created_at']);
                });
            } else {
                // Pastikan kolom reason dan type tidak truncated jika tabel lama masih varchar(255)/enum
                try {
                    if (Schema::hasColumn('credit_score_logs', 'reason')) {
                        DB::statement("ALTER TABLE credit_score_logs MODIFY COLUMN reason TEXT NULL");
                    }
                    if (Schema::hasColumn('credit_score_logs', 'type')) {
                        DB::statement("ALTER TABLE credit_score_logs MODIFY COLUMN type VARCHAR(20) DEFAULT 'add'");
                    }
                } catch (\Throwable $ignored) {
                    // Abaikan jika database tidak support / privilege terbatas
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ensureTablesExist credit_score_logs notice: ' . $e->getMessage());
        }
    }

    /**
     * Resolusi parameter user baik dari model binding maupun ID numerik.
     */
    private function resolveUser($user): User
    {
        if ($user instanceof User) {
            return $user;
        }
        return User::findOrFail($user);
    }

    /**
     * GET /credit-score
     * Halaman utama — Comdis/PND/IE lihat semua, anggota lain lihat skor sendiri.
     */
    public function index(Request $request)
    {
        try {
            $this->ensureTablesExist();
            $user = auth()->user();

            if (!$user) {
                return redirect()->route('staff.login');
            }

            // Sistem Credit Score khusus Alta Hospital
            $userHospital = strtolower(trim($user->hospital ?? 'alta'));
            if ($userHospital === 'roxwood' && !$user->isAdmin()) {
                return redirect()->route('dashboard')->with('error', 'Sistem Credit Score saat ini khusus untuk anggota Alta Hospital.');
            }

            if ($this->canViewAll()) {
                // Tampilkan semua anggota aktif Alta Hospital + skor mereka
                $search = trim($request->get('q', ''));
                $hospital = 'alta';

                $query = User::with(['role:id,name,display_name,level', 'subRole:id,name,short_name,color', 'creditScore'])
                    ->where('users.is_active', true)
                    ->whereNotNull('users.role_id')
                    ->where('users.hospital', 'alta')
                    ->whereHas('role', fn($q) => $q->where('name', '!=', 'admin'));

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', "%{$search}%")
                          ->orWhere('users.staff_id', 'like', "%{$search}%")
                          ->orWhere('users.email', 'like', "%{$search}%")
                          ->orWhereHas('subRole', fn($sr) => $sr->where('name', 'like', "%{$search}%")->orWhere('short_name', 'like', "%{$search}%"))
                          ->orWhereHas('role', fn($r) => $r->where('display_name', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
                    });
                }

                $members = $query->orderByRoleLevel()->paginate(30)->withQueryString();

                return view('credit-score.index-all', compact('members', 'search', 'hospital'));
            }

            // Anggota biasa — hanya lihat skor & log pribadi
            $creditScore = CreditScore::getOrCreate($user->id);
            $logs = CreditScoreLog::where('user_id', $user->id)
                ->with('issuedBy:id,name,staff_id')
                ->orderByDesc('created_at')
                ->paginate(20);

            return view('credit-score.index-self', compact('creditScore', 'logs'));
        } catch (\Throwable $e) {
            Log::error('CreditScore index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('dashboard')->with('error', 'Gagal memuat halaman Credit Score: ' . $e->getMessage());
        }
    }

    /**
     * GET /credit-score/{user}
     * Detail skor + log satu anggota Alta (Comdis/PND/IE/Admin).
     */
    public function show($user)
    {
        try {
            $this->ensureTablesExist();
            $user = $this->resolveUser($user);

            // Validasi: hanya untuk staf Alta Hospital
            if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta' && !auth()->user()->isAdmin()) {
                return redirect()->route('credit-score.index')->with('error', 'Data Credit Score hanya tersedia untuk staf Alta Hospital.');
            }

            if (!$this->canViewAll()) {
                // Anggota biasa hanya boleh lihat milik sendiri
                if ($user->id !== auth()->id()) {
                    return redirect()->route('credit-score.index')->with('error', 'Credit Score bersifat pribadi dan hanya dapat dilihat oleh pemilik akun, Divisi PND, IE, atau Comdis.');
                }
            }

            $creditScore = CreditScore::getOrCreate($user->id);
            $logs = CreditScoreLog::where('user_id', $user->id)
                ->with('issuedBy:id,name,staff_id')
                ->orderByDesc('created_at')
                ->paginate(30);

            return view('credit-score.show', compact('user', 'creditScore', 'logs'));
        } catch (\Throwable $e) {
            Log::error('CreditScore show error: ' . $e->getMessage(), [
                'user' => is_object($user) ? $user->id : $user,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('credit-score.index')->with('error', 'Gagal memuat detail Credit Score: ' . $e->getMessage());
        }
    }

    /**
     * GET /credit-score/{user}/input
     * Form input poin — khusus Comdis (Khusus Staf Alta).
     */
    public function inputForm($user)
    {
        try {
            $this->ensureTablesExist();
            $user = $this->resolveUser($user);

            if (!$this->canManage()) {
                return redirect()->route('credit-score.index')->with('error', 'Hanya Divisi Comdis atau Admin yang memiliki akses untuk menginput Credit Score.');
            }

            if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta') {
                return redirect()->route('credit-score.index')->with('error', 'Credit Score hanya berlaku untuk anggota Alta Hospital.');
            }

            $creditScore = CreditScore::getOrCreate($user->id);
            return view('credit-score.input', compact('user', 'creditScore'));
        } catch (\Throwable $e) {
            Log::error('CreditScore inputForm error: ' . $e->getMessage(), [
                'user' => is_object($user) ? $user->id : $user,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('credit-score.index')->with('error', 'Gagal memuat formulir input Credit Score: ' . $e->getMessage());
        }
    }

    /**
     * POST /credit-score/{user}/input
     * Simpan penambahan/pengurangan poin (Khusus Staf Alta).
     */
    public function inputStore(Request $request, $user)
    {
        try {
            $this->ensureTablesExist();
            $user = $this->resolveUser($user);

            if (!$this->canManage()) {
                return back()->with('error', 'Hanya Divisi Comdis atau Admin yang memiliki hak akses untuk menginput Credit Score.');
            }

            if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta') {
                return back()->with('error', 'Credit Score hanya berlaku untuk anggota Alta Hospital.');
            }

            $validator = Validator::make($request->all(), [
                'type'   => 'required|in:add,deduct,set',
                'amount' => 'required|integer|min:0|max:100',
                'reason' => 'required|string|max:1000',
            ], [
                'type.required'   => 'Jenis penyesuaian harus dipilih.',
                'amount.required' => 'Jumlah poin harus diisi.',
                'amount.integer'  => 'Jumlah poin harus berupa bilangan bulat.',
                'amount.min'      => 'Jumlah poin minimal 0.',
                'amount.max'      => 'Jumlah poin maksimal 100.',
                'reason.required' => 'Alasan / dasar tindakan harus diisi.',
                'reason.max'      => 'Alasan maksimal 1000 karakter.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $creditScore = CreditScore::getOrCreate($user->id);
            $issuedBy = auth()->id() ?? $user->id;
            $amount = (int) $request->amount;
            $reason = trim($request->reason);

            if ($request->type === 'set') {
                $newBalance = $creditScore->setScore(
                    $amount,
                    $issuedBy,
                    $reason
                );
                $action = "diatur menjadi {$newBalance} poin";
            } elseif ($request->type === 'add') {
                $newBalance = $creditScore->add(
                    $amount,
                    $issuedBy,
                    $reason
                );
                $action = "ditambah {$amount} poin";
            } else {
                $newBalance = $creditScore->deduct(
                    $amount,
                    $issuedBy,
                    $reason
                );
                $action = "dikurangi {$amount} poin";
            }

            return redirect()
                ->route('credit-score.show', $user->id)
                ->with('success', "Credit Score {$user->name} berhasil {$action}. Saldo sekarang: {$newBalance}.");
        } catch (\Throwable $e) {
            Log::error('CreditScore inputStore error: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
                'user'    => is_object($user) ? $user->id : $user,
            ]);

            return back()->withInput()->with('error', 'Gagal memproses Credit Score: ' . $e->getMessage());
        }
    }

    /**
     * GET /credit-score/{user}/edit
     * Form edit / atur langsung saldo Credit Score — khusus Comdis.
     */
    public function editForm($user)
    {
        try {
            $this->ensureTablesExist();
            $user = $this->resolveUser($user);

            if (!$this->canManage()) {
                return redirect()->route('credit-score.index')->with('error', 'Hanya Divisi Comdis atau Admin yang memiliki akses untuk mengedit Credit Score.');
            }

            if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta') {
                return redirect()->route('credit-score.index')->with('error', 'Credit Score hanya berlaku untuk anggota Alta Hospital.');
            }

            $creditScore = CreditScore::getOrCreate($user->id);
            return view('credit-score.edit', compact('user', 'creditScore'));
        } catch (\Throwable $e) {
            Log::error('CreditScore editForm error: ' . $e->getMessage(), [
                'user' => is_object($user) ? $user->id : $user,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('credit-score.index')->with('error', 'Gagal memuat form edit Credit Score: ' . $e->getMessage());
        }
    }

    /**
     * POST /credit-score/{user}/edit
     * Simpan perubahan saldo langsung oleh Comdis.
     */
    public function updateScore(Request $request, $user)
    {
        try {
            $this->ensureTablesExist();
            $user = $this->resolveUser($user);

            if (!$this->canManage()) {
                return back()->with('error', 'Hanya Divisi Comdis atau Admin yang memiliki akses untuk mengedit Credit Score.');
            }

            if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta') {
                return back()->with('error', 'Credit Score hanya berlaku untuk anggota Alta Hospital.');
            }

            $validator = Validator::make($request->all(), [
                'score'  => 'required|integer|min:0|max:100',
                'reason' => 'required|string|max:1000',
            ], [
                'score.required'  => 'Nilai skor harus diisi.',
                'score.integer'   => 'Nilai skor harus berupa angka.',
                'score.min'       => 'Nilai skor minimal 0.',
                'score.max'       => 'Nilai skor maksimal 100.',
                'reason.required' => 'Alasan perbaikan/edit skor harus diisi.',
                'reason.max'      => 'Alasan maksimal 1000 karakter.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $creditScore = CreditScore::getOrCreate($user->id);
            $newBalance = $creditScore->setScore(
                (int) $request->score,
                auth()->id() ?? $user->id,
                trim($request->reason)
            );

            return redirect()
                ->route('credit-score.show', $user->id)
                ->with('success', "Credit Score {$user->name} berhasil diubah menjadi {$newBalance}.");
        } catch (\Throwable $e) {
            Log::error('CreditScore updateScore error: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
                'user'    => is_object($user) ? $user->id : $user,
            ]);

            return back()->withInput()->with('error', 'Gagal mengubah Credit Score: ' . $e->getMessage());
        }
    }

    /**
     * PUT /credit-score/log/{log}
     * Update catatan alasan riwayat Credit Score — khusus Comdis.
     */
    public function updateLog(Request $request, $log)
    {
        try {
            $this->ensureTablesExist();
            if (!$this->canManage()) {
                return back()->with('error', 'Hanya Divisi Comdis atau Admin yang dapat mengedit riwayat Credit Score.');
            }

            $log = ($log instanceof CreditScoreLog) ? $log : CreditScoreLog::findOrFail($log);

            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:1000',
            ], [
                'reason.required' => 'Alasan / catatan riwayat harus diisi.',
                'reason.max'      => 'Alasan maksimal 1000 karakter.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $log->update(['reason' => trim($request->reason)]);

            return back()->with('success', 'Catatan alasan pelanggaran/penghargaan berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('CreditScore updateLog error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui catatan: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /credit-score/log/{log}
     * Batalkan / hapus catatan log transaksi yang keliru & kembalikan saldo skornya — khusus Comdis.
     */
    public function destroyLog($log)
    {
        try {
            $this->ensureTablesExist();
            if (!$this->canManage()) {
                return back()->with('error', 'Hanya Divisi Comdis atau Admin yang dapat menghapus catatan Credit Score.');
            }

            $log = ($log instanceof CreditScoreLog) ? $log : CreditScoreLog::findOrFail($log);

            $creditScore = CreditScore::where('user_id', $log->user_id)->first();
            if ($creditScore) {
                if ($log->type === 'add') {
                    $creditScore->balance -= $log->amount;
                } elseif ($log->type === 'deduct') {
                    $creditScore->balance += abs($log->amount);
                }
                $creditScore->balance = min(100, max(0, (int) $creditScore->balance));
                $creditScore->save();
            }

            $log->delete();

            return back()->with('success', 'Catatan riwayat transaksi berhasil dihapus dan saldo Credit Score telah dikembalikan.');
        } catch (\Throwable $e) {
            Log::error('CreditScore destroyLog error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus catatan riwayat: ' . $e->getMessage());
        }
    }
}
