<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MemberCertification;
use App\Models\OperationRequest;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PndOperationController extends Controller
{
    private function checkIsPnd(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('pnd')) {
            abort(403, 'Hanya divisi PND yang dapat mengelola ini.');
        }
    }

    // ─── Pengajuan Operasi (Anggota) ──────────────────────────────────────────

    public function myOperations()
    {
        $user  = Auth::user();
        $items = OperationRequest::with(['dpjp:id,name', 'verifiedByPnd:id,name'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        return view('portal.pnd.my-operations', compact('items'));
    }

    public function createOperation()
    {
        $user = Auth::user();
        // Sertakan semua staf aktif medis & manajemen (kecuali Trainee) agar Manajer, Staff Manager, dll tetap dapat ditargetkan sebagai DPJP/Asisten
        $doctors = User::with('role:id,name,display_name,level')
            ->where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->where(function ($q) {
                $q->whereDoesntHave('role')
                  ->orWhereHas('role', fn($rq) => $rq->whereNotIn('name', ['trainee']));
            })
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id', 'role_id']);

        return view('portal.pnd.create-operation', compact('doctors', 'user'));
    }

    public function storeOperation(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'jenis_operasi'     => 'required|string|max:100',
            'patient_name'      => 'required|string|max:255',
            'diagnosis'         => 'required|string|max:2000',
            'planned_procedure' => 'required|string|max:2000',
            'scheduled_at'      => 'nullable|date',
            'dpjp_id'           => 'nullable|exists:users,id',
            'assistant_ids'     => 'nullable|array',
            'assistant_ids.*'   => 'exists:users,id',
            'notes'             => 'nullable|string|max:500',
        ]);

        OperationRequest::create([
            'user_id'           => $user->id,
            'jenis_operasi'     => $validated['jenis_operasi'],
            'patient_name'      => $validated['patient_name'],
            'diagnosis'         => $validated['diagnosis'],
            'planned_procedure' => $validated['planned_procedure'],
            'scheduled_at'      => $validated['scheduled_at'] ?? null,
            'dpjp_id'           => $validated['dpjp_id'] ?? null,
            'assistant_ids'     => $validated['assistant_ids'] ?? [],
            'notes'             => $validated['notes'] ?? null,
            'status'            => OperationRequest::STATUS_PENDING,
        ]);

        return redirect()->route('portal.pnd.my-operations')
            ->with('success', 'Pengajuan operasi berhasil dikirim ke PND untuk verifikasi.');
    }

    // ─── PND: Kelola & Verifikasi Pengajuan Operasi ───────────────────────────

    public function index(Request $request)
    {
        $this->checkIsPnd();
        $user = Auth::user();

        $query = OperationRequest::with(['user:id,name,staff_id', 'dpjp:id,name', 'verifiedByPnd:id,name'])
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest();

        if ($status = $request->get('status')) $query->where('status', $status);

        $items = $query->paginate(30)->withQueryString();
        return view('portal.pnd.operations', compact('items'));
    }

    public function approve(Request $request, OperationRequest $opRequest)
    {
        $this->checkIsPnd();
        $request->validate(['pnd_notes' => 'nullable|string|max:500']);

        $opRequest->update([
            'status'          => OperationRequest::STATUS_APPROVED,
            'verified_by_pnd' => Auth::id(),
            'pnd_verified_at' => now(),
            'pnd_notes'       => $request->pnd_notes,
        ]);

        return back()->with('success', 'Pengajuan operasi ' . $opRequest->patient_name . ' disetujui.');
    }

    public function reject(Request $request, OperationRequest $opRequest)
    {
        $this->checkIsPnd();
        $request->validate(['pnd_notes' => 'required|string|max:500']);

        $opRequest->update([
            'status'          => OperationRequest::STATUS_REJECTED,
            'verified_by_pnd' => Auth::id(),
            'pnd_verified_at' => now(),
            'pnd_notes'       => $request->pnd_notes,
        ]);

        return back()->with('success', 'Pengajuan operasi ditolak.');
    }

    // ─── PND: Kelola Sertifikat Operasi ───────────────────────────────────────

    public function certIndex(Request $request)
    {
        $this->checkIsPnd();
        $user = Auth::user();

        $certifications = MemberCertification::with(['user:id,name,staff_id', 'issuedBy:id,name'])
            ->where('type', 'operation_cert')
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest()
            ->paginate(30);

        \App\Models\CertificateApplication::ensureTableExists();
        $pendingApplications = \App\Models\CertificateApplication::with('user:id,name,staff_id')
            ->where('division', 'pnd')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $staffList = User::where('is_active', true)
            ->where('hospital', $user->hospital ?? 'alta')
            ->whereNotNull('role_id')
            ->orderByRoleLevel()
            ->get(['id', 'name', 'staff_id']);

        return view('portal.pnd.certs', compact('certifications', 'staffList', 'pendingApplications'));
    }

    public function certStore(Request $request)
    {
        $this->checkIsPnd();

        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'title'              => 'required|string|max:255',
            'certificate_number' => 'nullable|string|max:100',
            'issue_date'         => 'required|date',
            'notes'              => 'nullable|string|max:500',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('certifications/pnd', 'public');
        }

        $cert = MemberCertification::create([
            'user_id'            => $validated['user_id'],
            'type'               => 'operation_cert',
            'division'           => 'pnd',
            'title'              => $validated['title'],
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

        return redirect()->route('portal.pnd.cert-index')
            ->with('success', 'Sertifikat operasi berhasil diterbitkan dan otomatis sinkron ke profil anggota.');
    }

    public function certDestroy(MemberCertification $certification)
    {
        $this->checkIsPnd();
        abort_unless($certification->type === 'operation_cert', 404);

        if ($certification->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($certification->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($certification->file_path);
        }

        $certification->delete();

        return back()->with('success', 'Sertifikat operasi berhasil dihapus dari sistem.');
    }

    public function approveApplication(Request $request, \App\Models\CertificateApplication $application)
    {
        $this->checkIsPnd();
        abort_unless($application->division === 'pnd', 403);

        $cert = MemberCertification::create([
            'user_id'           => $application->user_id,
            'type'              => 'operation_cert',
            'division'          => 'pnd',
            'title'             => $application->title,
            'issued_by_user_id' => Auth::id(),
            'issue_date'        => now(),
            'notes'             => $application->notes ?? 'Diterbitkan melalui pengajuan sertifikat operasi.',
            'status'            => 'active',
        ]);

        $filePath = \App\Services\CertificateGeneratorService::generate($cert);
        $cert->update(['file_path' => $filePath]);

        $application->update([
            'status'           => \App\Models\CertificateApplication::STATUS_APPROVED,
            'verified_by'      => Auth::id(),
            'verified_at'      => now(),
            'certification_id' => $cert->id,
            'admin_notes'      => $request->admin_notes ?? 'Disetujui oleh PND.',
        ]);

        return back()->with('success', 'Pengajuan sertifikat operasi disetujui dan sertifikat resmi telah diterbitkan.');
    }

    public function rejectApplication(Request $request, \App\Models\CertificateApplication $application)
    {
        $this->checkIsPnd();
        abort_unless($application->division === 'pnd', 403);

        $application->update([
            'status'      => \App\Models\CertificateApplication::STATUS_REJECTED,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes ?? 'Pengajuan ditolak oleh PND.',
        ]);

        return back()->with('success', 'Pengajuan sertifikat operasi telah ditolak.');
    }
}
