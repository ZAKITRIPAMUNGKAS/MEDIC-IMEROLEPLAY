<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MemberCertification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GaCertificationController extends Controller
{
    private function checkIsGa(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('ga')) {
            abort(403, 'Hanya divisi GA yang dapat mengelola sertifikasi kendaraan.');
        }
    }

    /**
     * GET /portal/ga/certifications
     * Daftar seluruh sertifikat kendaraan (darat & heli) yang pernah diterbitkan.
     */
    public function index(Request $request)
    {
        $this->checkIsGa();
        $user = Auth::user();

        $query = MemberCertification::with(['user:id,name,staff_id', 'issuedBy:id,name'])
            ->whereIn('type', ['vehicle_land', 'vehicle_heli'])
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
        $staffList = User::with('role:id,name,display_name,level')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->whereNotNull('role_id')
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id', 'role_id']);

        $pendingApplications = \App\Models\CertificateApplication::with('user')
            ->where('division', 'ga')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('portal.ga.index', compact('certifications', 'staffList', 'pendingApplications'));
    }

    /**
     * GET /portal/ga/certifications/create
     */
    public function create()
    {
        $this->checkIsGa();
        $user = Auth::user();

        $staffList = User::with('role:id,name,display_name,level')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->whereNotNull('role_id')
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id', 'role_id']);

        return view('portal.ga.create', compact('staffList'));
    }

    /**
     * POST /portal/ga/certifications
     */
    public function store(Request $request)
    {
        $this->checkIsGa();

        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'type'               => 'required|in:vehicle_land,vehicle_heli',
            'title'              => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:100',
            'issue_date'         => 'required|date',
            'expiry_date'        => 'nullable|date|after_or_equal:issue_date',
            'notes'              => 'nullable|string|max:500',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('certifications/ga', 'public');
        }

        $cert = MemberCertification::create([
            'user_id'            => $validated['user_id'],
            'type'               => $validated['type'],
            'division'           => 'ga',
            'title'              => $validated['title'],
            'certificate_number' => $validated['certificate_number'] ?? null,
            'issued_by_user_id'  => Auth::id(),
            'issue_date'         => $validated['issue_date'],
            'expiry_date'        => $validated['expiry_date'] ?? null,
            'file_path'          => $filePath,
            'notes'              => $validated['notes'] ?? null,
            'status'             => 'active',
        ]);

        if (empty($filePath)) {
            $filePath = \App\Services\CertificateGeneratorService::generate($cert);
            $cert->update(['file_path' => $filePath]);
        }

        return redirect()->route('portal.ga.index')
            ->with('success', 'Sertifikat kendaraan berhasil diterbitkan dan otomatis sinkron ke profil anggota.');
    }

    /**
     * POST /portal/ga/certifications/{certification}/revoke
     */
    public function revoke(Request $request, MemberCertification $certification)
    {
        $this->checkIsGa();
        abort_unless(in_array($certification->type, ['vehicle_land', 'vehicle_heli']), 404);

        $certification->update(['status' => 'revoked']);
        return back()->with('success', 'Sertifikat kendaraan ' . $certification->user->name . ' dicabut.');
    }

    /**
     * DELETE /portal/ga/certifications/{certification}
     */
    public function destroy(MemberCertification $certification)
    {
        $this->checkIsGa();
        abort_unless(in_array($certification->type, ['vehicle_land', 'vehicle_heli']), 404);

        if ($certification->file_path && Storage::disk('public')->exists($certification->file_path)) {
            Storage::disk('public')->delete($certification->file_path);
        }

        $certification->delete();

        return back()->with('success', 'Sertifikat kendaraan berhasil dihapus dari sistem.');
    }

    /**
     * POST /portal/ga/applications/{application}/approve
     */
    public function approveApplication(Request $request, \App\Models\CertificateApplication $application)
    {
        $this->checkIsGa();
        abort_unless($application->division === 'ga', 403);

        $cert = MemberCertification::create([
            'user_id'           => $application->user_id,
            'type'              => $application->type,
            'division'          => 'ga',
            'title'             => $application->title,
            'issued_by_user_id' => Auth::id(),
            'issue_date'        => now(),
            'notes'             => $application->notes ?? 'Diterbitkan melalui pengajuan sertifikat kendaraan.',
            'status'            => 'active',
        ]);

        $filePath = \App\Services\CertificateGeneratorService::generate($cert);
        $cert->update(['file_path' => $filePath]);

        $application->update([
            'status'           => \App\Models\CertificateApplication::STATUS_APPROVED,
            'verified_by'      => Auth::id(),
            'verified_at'      => now(),
            'certification_id' => $cert->id,
            'admin_notes'      => $request->admin_notes ?? 'Disetujui oleh GA.',
        ]);

        return back()->with('success', 'Pengajuan disetujui dan sertifikat otomatis diterbitkan untuk ' . $application->user->name);
    }

    /**
     * POST /portal/ga/applications/{application}/reject
     */
    public function rejectApplication(Request $request, \App\Models\CertificateApplication $application)
    {
        $this->checkIsGa();
        abort_unless($application->division === 'ga', 403);

        $application->update([
            'status'      => \App\Models\CertificateApplication::STATUS_REJECTED,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes ?? 'Ditolak oleh GA.',
        ]);

        return back()->with('success', 'Pengajuan sertifikat ditolak.');
    }
}
