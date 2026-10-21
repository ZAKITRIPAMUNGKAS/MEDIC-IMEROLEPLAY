<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MemberCertification;
use App\Models\StaseApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MslCertificationController extends Controller
{
    private function checkIsMsl(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('msl')) {
            abort(403, 'Hanya divisi MSL yang dapat mengelola fitur ini.');
        }
    }

    // ─── Sertifikasi Visum ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->checkIsMsl();
        $user = Auth::user();

        $query = MemberCertification::with(['user:id,name,staff_id', 'issuedBy:id,name'])
            ->whereIn('type', ['visum_alive', 'visum_dead'])
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest();

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->get('q')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('staff_id', 'like', "%{$search}%"));
        }

        $certifications = $query->paginate(30)->withQueryString();

        $staffList = User::where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->whereNotNull('role_id')
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id']);

        return view('portal.msl.index', compact('certifications', 'staffList'));
    }

    public function storeVisum(Request $request)
    {
        $this->checkIsMsl();

        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'type'               => 'required|in:visum_alive,visum_dead',
            'title'              => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:100',
            'issue_date'         => 'required|date',
            'notes'              => 'nullable|string|max:500',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('certifications/msl/visum', 'public');
        }

        $typeMap = ['visum_alive' => 'Sertifikat Visum Hidup', 'visum_dead' => 'Sertifikat Visum Mati'];

        $cert = MemberCertification::create([
            'user_id'            => $validated['user_id'],
            'type'               => $validated['type'],
            'division'           => 'msl',
            'title'              => $validated['title'] ?: ($typeMap[$validated['type']] ?? $validated['type']),
            'certificate_number' => $validated['certificate_number'] ?? null,
            'issued_by_user_id'  => Auth::id(),
            'issue_date'         => $validated['issue_date'],
            'file_path'          => $filePath,
            'notes'              => $validated['notes'] ?? null,
            'status'             => 'active',
        ]);

        if (empty($filePath)) {
            $filePath = \App\Services\CertificateGeneratorService::generate($cert);
            $cert->update(['file_path' => $filePath]);
        }

        return redirect()->route('portal.msl.index')
            ->with('success', 'Sertifikat visum berhasil diterbitkan dan otomatis masuk ke profil anggota.');
    }

    /**
     * POST /portal/msl/{certification}/revoke
     * Cabut (revoke) atau aktifkan kembali sertifikat visum / stase.
     */
    public function revoke(Request $request, MemberCertification $certification)
    {
        $this->checkIsMsl();
        abort_unless(in_array($certification->type, ['visum_alive', 'visum_dead', 'stase']), 404);

        $newStatus = ($certification->status === 'active') ? 'revoked' : 'active';
        $certification->update(['status' => $newStatus]);

        $actionText = ($newStatus === 'revoked') ? 'dicabut (revoked)' : 'diaktifkan kembali';
        return back()->with('success', "Sertifikat {$certification->title} milik {$certification->user?->name} berhasil {$actionText}.");
    }

    /**
     * DELETE /portal/msl/{certification}
     * Hapus permanen sertifikat dari sistem.
     */
    public function destroy(MemberCertification $certification)
    {
        $this->checkIsMsl();
        abort_unless(in_array($certification->type, ['visum_alive', 'visum_dead', 'stase']), 404);

        if ($certification->file_path && Storage::disk('public')->exists($certification->file_path)) {
            Storage::disk('public')->delete($certification->file_path);
        }

        $certification->delete();

        return back()->with('success', 'Sertifikat berhasil dihapus dari sistem.');
    }

    // ─── Manajemen Konsulen Stase ─────────────────────────────────────────────

    /**
     * POST /portal/msl/konsulen/assign
     * Daftarkan dokter/staf sebagai Konsulen Stase resmi Alta Hospital.
     */
    public function assignKonsulen(Request $request)
    {
        $this->checkIsMsl();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Pilih dokter atau anggota staf yang akan didaftarkan sebagai konsulen stase.',
        ]);

        $target = User::findOrFail($validated['user_id']);
        $target->update(['is_konsulen' => true]);

        return back()->with('success', "Staf {$target->name} ({$target->staff_id}) berhasil didaftarkan sebagai Konsulen Stase resmi.");
    }

    /**
     * POST /portal/msl/konsulen/{user}/revoke
     * Cabut status penugasan Konsulen Stase.
     */
    public function revokeKonsulen(User $user)
    {
        $this->checkIsMsl();

        $user->update(['is_konsulen' => false]);

        return back()->with('success', "Status Konsulen Stase untuk {$user->name} ({$user->staff_id}) berhasil dicabut.");
    }

    // ─── Pengajuan Stase (MSL Verifikasi & Penerbitan Sertifikat) ──────────────

    public function staseIndex(Request $request)
    {
        $this->checkIsMsl();
        $user = Auth::user();

        $query = StaseApplication::with(['user:id,name,staff_id', 'konsulen:id,name', 'certification'])
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $applications = $query->paginate(25)->withQueryString();

        // Ambil daftar Konsulen Stase aktif
        $activeKonsulen = User::with('role:id,name,display_name')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_konsulen', true)
            ->orderBy('name')
            ->get();

        // Ambil staf medis yang belum jadi konsulen untuk form penugasan
        $staffCandidates = User::with('role:id,name,display_name')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_konsulen', false)
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id', 'role_id']);

        return view('portal.msl.stase', compact('applications', 'activeKonsulen', 'staffCandidates'));
    }

    /**
     * Approve Stase & Langsung Terbitkan Sertifikat Stase Resmi ke Profil
     */
    public function staseApprove(Request $request, StaseApplication $stase)
    {
        $this->checkIsMsl();
        $request->validate([
            'msl_notes' => 'nullable|string|max:500',
            'grade'     => 'nullable|string|max:10',
        ]);

        $allowed = [
            StaseApplication::STATUS_PENDING_KONSULEN,
            StaseApplication::STATUS_PENDING_MSL,
            StaseApplication::STATUS_APPROVED_KONSULEN,
            StaseApplication::STATUS_APPROVED,
        ];

        if (!in_array($stase->status, $allowed)) {
            return back()->with('error', 'Status stase tidak sesuai untuk disetujui MSL.');
        }

        $finalGrade = $request->grade ?: ($stase->grade ?: 'Lulus');
        $konsulenName = $stase->konsulen?->name ?? 'Dokter Konsulen Pembimbing';

        // Buat sertifikat kelulusan stase resmi
        $certNotes = 'Kelulusan ' . $stase->stase_name . ' di bawah bimbingan ' . $konsulenName . '.';
        if (!empty($stase->konsulen_notes)) {
            $certNotes .= ' Laporan Konsulen: ' . $stase->konsulen_notes;
        }

        $cert = MemberCertification::create([
            'user_id'           => $stase->user_id,
            'type'              => 'stase',
            'division'          => 'msl',
            'title'             => 'Sertifikat Kelulusan: ' . $stase->stase_name,
            'issued_by_user_id' => Auth::id(),
            'issue_date'        => now()->toDateString(),
            'notes'             => $certNotes,
            'status'            => 'active',
        ]);

        $filePath = \App\Services\CertificateGeneratorService::generate($cert);
        $cert->update(['file_path' => $filePath]);

        $updateData = [
            'status'               => StaseApplication::STATUS_COMPLETED,
            'passed'               => true,
            'grade'                => $finalGrade,
            'certification_id'     => $cert->id,
            'msl_approved_by'      => Auth::id(),
            'msl_approved_at'      => now(),
            'msl_notes'            => $request->msl_notes ?? 'Disetujui oleh MSL dan sertifikat stase resmi telah diterbitkan ke profil anggota.',
        ];

        // Jika konsulen belum sempat menyetujui di sistem, lengkapi otomatis
        if (!$stase->konsulen_approved_at) {
            $updateData['konsulen_approved_by'] = $stase->konsulen_id ?? Auth::id();
            $updateData['konsulen_approved_at'] = now();
            if (empty($stase->konsulen_notes)) {
                $updateData['konsulen_notes'] = 'Disetujui langsung oleh Divisi MSL.';
            }
        }

        $stase->update($updateData);

        return back()->with('success', 'Stase ' . $stase->stase_name . ' berhasil disetujui MSL! Sertifikat resmi otomatis terbit dan tampil di profil ' . $stase->user?->name);
    }

    public function staseReject(Request $request, StaseApplication $stase)
    {
        $this->checkIsMsl();
        $request->validate(['msl_notes' => 'nullable|string|max:500']);

        $stase->update([
            'status'          => StaseApplication::STATUS_REJECTED,
            'msl_approved_by' => Auth::id(),
            'msl_approved_at' => now(),
            'msl_notes'       => $request->msl_notes ?? 'Ditolak oleh Divisi MSL.',
        ]);

        return back()->with('success', 'Pengajuan stase ' . $stase->stase_name . ' ditolak oleh MSL.');
    }

    public function staseComplete(Request $request, StaseApplication $stase)
    {
        return $this->staseApprove($request, $stase);
    }
}
