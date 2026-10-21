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

        // 1. Ambil daftar Konsulen Stase yang terdaftar resmi di sistem Alta
        $registeredKonsulen = User::with('role:id,name,display_name')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_konsulen', true)
            ->orderBy('name')
            ->get(['id', 'name', 'staff_id', 'role_id']);

        // Jika ada konsulen resmi yang terdaftar, gunakan itu. Jika belum ada yang didaftarkan, fallback ke Dokter Spesialis/Pimpinan
        if ($registeredKonsulen->isNotEmpty()) {
            $konsulenList = $registeredKonsulen;
        } else {
            $konsulenList = User::with('role:id,name,display_name')
                ->where('is_active', true)
                ->where('hospital', $user->hospital ?? 'alta')
                ->whereHas('role', fn($q) => $q->whereIn('name', ['dokter_spesialis', 'supervisor', 'staff_manager', 'manajer', 'executive', 'admin']))
                ->orderByRoleLevel()
                ->get(['id', 'name', 'staff_id', 'role_id']);
        }

        // 2. Daftar stase yang tersedia
        $availableStases = StaseApplication::AVAILABLE_STASES;

        return view('portal.stase.create', compact('user', 'konsulenList', 'availableStases'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'konsulen_id' => 'required|exists:users,id',
            'stase_name'  => 'required|string|max:255',
            'notes'       => 'nullable|string|max:500',
        ], [
            'konsulen_id.required' => 'Pilih dokter konsulen pembimbing stase.',
            'stase_name.required'  => 'Pilih bidang stase yang tersedia.',
        ]);

        StaseApplication::create([
            'user_id'     => $user->id,
            'konsulen_id' => $validated['konsulen_id'],
            'stase_name'  => $validated['stase_name'],
            'department'  => null,
            'start_date'  => null,
            'end_date'    => null,
            'notes'       => $validated['notes'] ?? null,
            'status'      => StaseApplication::STATUS_PENDING_KONSULEN,
        ]);

        return redirect()->route('portal.stase.index')
            ->with('success', 'Pengajuan stase berhasil dikirim ke Dokter Konsulen untuk evaluasi dan bimbingan.');
    }

    // ─── Konsulen: Setujui / Input Laporan Pengajuan Stase ────────────────────

    public function myApprovals(Request $request)
    {
        $user = Auth::user();

        // Konsulen melihat pengajuan yang ditujukan ke dirinya (atau admin/executive dapat melihat semua)
        $query = StaseApplication::with(['user:id,name,staff_id', 'certification'])
            ->latest();

        if (!$user->isAdmin() && !$user->isExecutiveOrAbove()) {
            $query->where('konsulen_id', $user->id);
        }

        $applications = $query->paginate(20);

        return view('portal.stase.konsulen', compact('applications', 'user'));
    }

    public function konsulenApprove(Request $request, StaseApplication $stase)
    {
        $user = Auth::user();

        // Izinkan: konsulen yang ditunjuk, konsulen terdaftar, manager ke atas, executive, atau admin
        $isAuthorized = $stase->konsulen_id === $user->id
            || $user->isAdmin()
            || $user->isExecutiveOrAbove()
            || $user->isManagerOrAbove()
            || $user->isInDivision('msl');

        if (!$isAuthorized) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengevaluasi pengajuan stase ini.');
        }

        $request->validate([
            'konsulen_notes' => 'required|string|max:1000',
            'grade'          => 'nullable|string|max:20',
        ], [
            'konsulen_notes.required' => 'Hasil evaluasi / laporan stase wajib diisi oleh dokter konsulen pembimbing.',
        ]);

        $stase->update([
            'status'               => StaseApplication::STATUS_APPROVED_KONSULEN,
            'konsulen_approved_by' => $user->id,
            'konsulen_approved_at' => now(),
            'konsulen_notes'       => $request->konsulen_notes,
            'grade'                => $request->grade ?: 'Lulus',
        ]);

        return back()->with('success', 'Laporan hasil stase ' . $stase->stase_name . ' berhasil disimpan dan disetujui. Berkas diteruskan ke Divisi MSL untuk verifikasi dan penerbitan sertifikat resmi.');
    }

    public function konsulenReject(Request $request, StaseApplication $stase)
    {
        $user = Auth::user();

        // Izinkan: konsulen yang ditunjuk, manager ke atas, executive, atau admin
        $isAuthorized = $stase->konsulen_id === $user->id
            || $user->isAdmin()
            || $user->isExecutiveOrAbove()
            || $user->isManagerOrAbove()
            || $user->isInDivision('msl');

        if (!$isAuthorized) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menolak pengajuan stase ini.');
        }

        $request->validate([
            'konsulen_notes' => 'required|string|max:500',
        ], [
            'konsulen_notes.required' => 'Alasan penolakan stase wajib diisi.',
        ]);

        $stase->update([
            'status'               => StaseApplication::STATUS_REJECTED,
            'konsulen_approved_by' => $user->id,
            'konsulen_approved_at' => now(),
            'konsulen_notes'       => $request->konsulen_notes,
        ]);

        return back()->with('success', 'Pengajuan stase berhasil ditolak.');
    }
}
