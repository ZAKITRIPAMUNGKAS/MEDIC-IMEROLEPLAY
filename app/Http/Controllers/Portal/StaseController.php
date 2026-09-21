<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\StaseApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaseController extends Controller
{
    // ─── Anggota: Pengajuan Stase ──────────────────────────────────────────────

    public function index()
    {
        $user         = Auth::user();
        $applications = StaseApplication::with(['konsulen:id,name', 'mslApprovedBy:id,name'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('portal.stase.index', compact('applications', 'user'));
    }

    public function create()
    {
        $user = Auth::user();

        // Daftar konsulen / dokter spesialis aktif di hospital yang sama
        $konsulenList = User::with('role:id,name,display_name')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->whereHas('role', fn($q) => $q->whereIn('name', ['dokter_spesialis', 'supervisor']))
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id', 'role_id']);

        return view('portal.stase.create', compact('user', 'konsulenList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'konsulen_id' => 'required|exists:users,id',
            'stase_name'  => 'required|string|max:255',
            'department'  => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'notes'       => 'nullable|string|max:500',
        ]);

        StaseApplication::create([
            'user_id'     => $user->id,
            'konsulen_id' => $validated['konsulen_id'],
            'stase_name'  => $validated['stase_name'],
            'department'  => $validated['department'] ?? null,
            'start_date'  => $validated['start_date'] ?? null,
            'end_date'    => $validated['end_date'] ?? null,
            'notes'       => $validated['notes'] ?? null,
            'status'      => StaseApplication::STATUS_PENDING_KONSULEN,
        ]);

        return redirect()->route('portal.stase.index')
            ->with('success', 'Pengajuan stase berhasil dikirim ke Konsulen untuk disetujui.');
    }

    // ─── Konsulen: Setujui / Tolak Pengajuan Stase ────────────────────────────

    public function myApprovals(Request $request)
    {
        $user = Auth::user();

        // Konsulen hanya melihat pengajuan yang ditujukan ke dirinya
        $applications = StaseApplication::with(['user:id,name,staff_id'])
            ->where('konsulen_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('portal.stase.konsulen', compact('applications', 'user'));
    }

    public function konsulenApprove(Request $request, StaseApplication $stase)
    {
        $user = Auth::user();
        if ($stase->konsulen_id !== $user->id && !$user->isAdmin() && !$user->isExecutiveOrAbove()) {
            abort(403, 'Anda bukan konsulen yang ditunjuk untuk stase ini.');
        }
        $request->validate(['konsulen_notes' => 'nullable|string|max:500']);

        $stase->update([
            'status'               => StaseApplication::STATUS_PENDING_MSL,
            'konsulen_approved_by' => $user->id,
            'konsulen_approved_at' => now(),
            'konsulen_notes'       => $request->konsulen_notes,
        ]);

        return back()->with('success', 'Pengajuan stase ' . $stase->stase_name . ' disetujui. Diteruskan ke MSL.');
    }

    public function konsulenReject(Request $request, StaseApplication $stase)
    {
        $user = Auth::user();
        if ($stase->konsulen_id !== $user->id && !$user->isAdmin() && !$user->isExecutiveOrAbove()) {
            abort(403);
        }
        $request->validate(['konsulen_notes' => 'required|string|max:500']);

        $stase->update([
            'status'               => StaseApplication::STATUS_REJECTED,
            'konsulen_approved_by' => $user->id,
            'konsulen_approved_at' => now(),
            'konsulen_notes'       => $request->konsulen_notes,
        ]);

        return back()->with('success', 'Pengajuan stase ditolak.');
    }
}
