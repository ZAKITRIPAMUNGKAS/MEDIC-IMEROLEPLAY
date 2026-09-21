<?php

namespace App\Http\Controllers;

use App\Models\CreditScore;
use App\Models\CreditScoreLog;
use App\Models\User;
use Illuminate\Http\Request;
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
        return $user && ($user->isAdmin() || $user->isInDivision('comdis'));
    }

    private function canViewAll(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isInDivision('comdis', 'pnd', 'ie'));
    }

    private function ensureTablesExist(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('credit_scores')) {
            \Illuminate\Support\Facades\Schema::create('credit_scores', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->integer('balance')->default(100);
                $table->timestamps();
                $table->unique('user_id');
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasTable('credit_score_logs')) {
            \Illuminate\Support\Facades\Schema::create('credit_score_logs', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
                $table->integer('amount');
                $table->integer('balance_after');
                $table->string('reason');
                $table->enum('type', ['add', 'deduct'])->default('add');
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    /**
     * GET /credit-score
     * Halaman utama — Comdis/PND/IE lihat semua, anggota lain lihat skor sendiri.
     */
    public function index(Request $request)
    {
        $this->ensureTablesExist();
        $user = auth()->user();

        // Sistem Credit Score khusus Alta Hospital
        $userHospital = strtolower(trim($user->hospital ?? 'alta'));
        if ($userHospital === 'roxwood' && !$user->isAdmin()) {
            abort(403, 'Sistem Credit Score saat ini khusus untuk anggota Alta Hospital.');
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
    }

    /**
     * GET /credit-score/{user}
     * Detail skor + log satu anggota Alta (Comdis/PND/IE/Admin).
     */
    public function show(User $user)
    {
        $this->ensureTablesExist();

        // Validasi: hanya untuk staf Alta Hospital
        if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta' && !auth()->user()->isAdmin()) {
            abort(404, 'Data Credit Score hanya tersedia untuk staf Alta Hospital.');
        }

        if (!$this->canViewAll()) {
            // Anggota biasa hanya boleh lihat milik sendiri
            if ($user->id !== auth()->id()) {
                abort(403, 'Credit Score bersifat pribadi dan hanya dapat dilihat oleh pemilik akun, Divisi PND, IE, atau Comdis.');
            }
        }

        $creditScore = CreditScore::getOrCreate($user->id);
        $logs = CreditScoreLog::where('user_id', $user->id)
            ->with('issuedBy:id,name,staff_id')
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('credit-score.show', compact('user', 'creditScore', 'logs'));
    }

    /**
     * GET /credit-score/{user}/input
     * Form input poin — khusus Comdis (Khusus Staf Alta).
     */
    public function inputForm(User $user)
    {
        $this->ensureTablesExist();
        if (!$this->canManage()) {
            abort(403, 'Hanya Divisi Comdis yang dapat menginput Credit Score.');
        }

        if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta') {
            return redirect()->route('credit-score.index')->with('error', 'Credit Score hanya berlaku untuk anggota Alta Hospital.');
        }

        $creditScore = CreditScore::getOrCreate($user->id);
        return view('credit-score.input', compact('user', 'creditScore'));
    }

    /**
     * POST /credit-score/{user}/input
     * Simpan penambahan/pengurangan poin (Khusus Staf Alta).
     */
    public function inputStore(Request $request, User $user)
    {
        $this->ensureTablesExist();
        if (!$this->canManage()) {
            abort(403, 'Hanya Divisi Comdis yang dapat menginput Credit Score.');
        }

        if (strtolower(trim($user->hospital ?? 'alta')) !== 'alta') {
            return back()->with('error', 'Credit Score hanya berlaku untuk anggota Alta Hospital.');
        }

        $validator = Validator::make($request->all(), [
            'type'   => 'required|in:add,deduct',
            'amount' => 'required|integer|min:1|max:100',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $creditScore = CreditScore::getOrCreate($user->id);

        if ($request->type === 'add') {
            $newBalance = $creditScore->add(
                (int) $request->amount,
                auth()->id(),
                $request->reason
            );
            $action = "ditambah {$request->amount} poin";
        } else {
            $newBalance = $creditScore->deduct(
                (int) $request->amount,
                auth()->id(),
                $request->reason
            );
            $action = "dikurangi {$request->amount} poin";
        }

        return redirect()
            ->route('credit-score.show', $user)
            ->with('success', "Credit Score {$user->name} berhasil {$action}. Saldo sekarang: {$newBalance}.");
    }
}
