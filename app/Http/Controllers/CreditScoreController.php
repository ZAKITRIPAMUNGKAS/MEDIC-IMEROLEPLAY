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
        return $user->isAdmin()
            || $user->isExecutiveOrAbove()
            || $user->isInDivision('comdis');
    }

    private function canViewAll(): bool
    {
        $user = auth()->user();
        return $user->isAdmin()
            || $user->isExecutiveOrAbove()
            || $user->isInDivision('comdis', 'pnd', 'ie');
    }

    /**
     * GET /credit-score
     * Halaman utama — Comdis/PND/IE lihat semua, anggota lain lihat skor sendiri.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($this->canViewAll()) {
            // Tampilkan semua anggota aktif + skor mereka
            $search = trim($request->get('q', ''));
            $hospital = $request->get('hospital', $user->hospital ?? 'alta');

            $query = User::with(['role:id,name,display_name,level', 'subRole:id,name,short_name,color', 'creditScore'])
                ->where('is_active', true)
                ->whereNotNull('role_id')
                ->where('hospital', $hospital)
                ->whereHas('role', fn($q) => $q->where('name', '!=', 'admin'));

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('staff_id', 'like', "%{$search}%");
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
     * Detail skor + log satu anggota (Comdis/PND/IE/Admin).
     */
    public function show(User $user)
    {
        if (!$this->canViewAll()) {
            // Anggota biasa hanya boleh lihat milik sendiri
            if ($user->id !== auth()->id()) {
                abort(403, 'Anda hanya dapat melihat Credit Score milik sendiri.');
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
     * Form input poin — khusus Comdis.
     */
    public function inputForm(User $user)
    {
        if (!$this->canManage()) {
            abort(403, 'Hanya Divisi Comdis yang dapat menginput Credit Score.');
        }

        $creditScore = CreditScore::getOrCreate($user->id);
        return view('credit-score.input', compact('user', 'creditScore'));
    }

    /**
     * POST /credit-score/{user}/input
     * Simpan penambahan/pengurangan poin.
     */
    public function inputStore(Request $request, User $user)
    {
        if (!$this->canManage()) {
            abort(403, 'Hanya Divisi Comdis yang dapat menginput Credit Score.');
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
