<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MemberCertification;
use App\Models\StaseApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MslCertificationController extends Controller
{
    private function checkIsMsl(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('msl')) {
            abort(403, 'Hanya divisi MSL yang dapat mengelola sertifikasi ini.');
        }
    }

    // ─── Sertifikasi Visum ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->checkIsMsl();
        $user = Auth::user();

        $certifications = MemberCertification::with(['user:id,name,staff_id', 'issuedBy:id,name'])
            ->whereIn('type', ['visum_alive', 'visum_dead'])
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest()
            ->paginate(30);

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

        MemberCertification::create([
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

        return redirect()->route('portal.msl.index')
            ->with('success', 'Sertifikat visum berhasil diterbitkan dan otomatis masuk ke profil anggota.');
    }

    // ─── Pengajuan Stase (MSL Approval Tahap 2) ───────────────────────────────

    public function staseIndex(Request $request)
    {
        $this->checkIsMsl();
        $user = Auth::user();

        $query = StaseApplication::with(['user:id,name,staff_id', 'konsulen:id,name'])
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $applications = $query->paginate(30)->withQueryString();

        return view('portal.msl.stase', compact('applications'));
    }

    public function staseApprove(Request $request, StaseApplication $stase)
    {
        $this->checkIsMsl();
        $request->validate(['msl_notes' => 'nullable|string|max:500']);

        if ($stase->status !== StaseApplication::STATUS_PENDING_MSL && $stase->status !== StaseApplication::STATUS_APPROVED_KONSULEN) {
            return back()->with('error', 'Status stase tidak sesuai untuk disetujui MSL.');
        }

        $stase->update([
            'status'          => StaseApplication::STATUS_APPROVED,
            'msl_approved_by' => Auth::id(),
            'msl_approved_at' => now(),
            'msl_notes'       => $request->msl_notes,
        ]);

        return back()->with('success', 'Stase ' . $stase->stase_name . ' disetujui oleh MSL.');
    }

    public function staseComplete(Request $request, StaseApplication $stase)
    {
        $this->checkIsMsl();
        $request->validate([
            'passed' => 'required|in:1,0',
            'grade'  => 'nullable|string|max:10',
        ]);

        $passed = (bool) $request->passed;

        // Jika lulus, terbitkan sertifikat stase otomatis ke profil
        $certId = null;
        if ($passed) {
            $cert = MemberCertification::create([
                'user_id'           => $stase->user_id,
                'type'              => 'visum_alive', // placeholder stase type
                'division'          => 'msl',
                'title'             => 'Sertifikat Kelulusan Stase: ' . $stase->stase_name,
                'issued_by_user_id' => Auth::id(),
                'issue_date'        => now()->toDateString(),
                'notes'             => 'Kelulusan stase ' . $stase->stase_name . '. Grade: ' . $request->grade,
                'status'            => 'active',
            ]);
            $certId = $cert->id;
        }

        $stase->update([
            'status'           => StaseApplication::STATUS_COMPLETED,
            'passed'           => $passed,
            'grade'            => $request->grade,
            'certification_id' => $certId,
            'msl_approved_by'  => Auth::id(),
        ]);

        $msg = $passed
            ? 'Stase ' . $stase->stase_name . ' dinyatakan LULUS. Sertifikat otomatis masuk ke profil anggota.'
            : 'Stase ' . $stase->stase_name . ' dinyatakan TIDAK LULUS.';

        return back()->with('success', $msg);
    }
}
