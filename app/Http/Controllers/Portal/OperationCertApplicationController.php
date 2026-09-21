<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CertificateApplication;
use App\Models\MemberCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationCertApplicationController extends Controller
{
    public function index()
    {
        CertificateApplication::ensureTableExists();
        $user = Auth::user();

        // Riwayat pengajuan sertifikat operasi oleh user
        $applications = CertificateApplication::with('verifiedBy:id,name', 'certification')
            ->where('user_id', $user->id)
            ->where('division', 'pnd')
            ->latest()
            ->get();

        // Sertifikat operasi resmi yang sudah dimiliki user
        $myCertifications = MemberCertification::with('issuedBy:id,name')
            ->where('user_id', $user->id)
            ->where('type', 'operation_cert')
            ->latest()
            ->get();

        $certificates = $myCertifications;

        return view('portal.member-certs.operation', compact('applications', 'myCertifications', 'certificates', 'user'));
    }

    public function store(Request $request)
    {
        CertificateApplication::ensureTableExists();
        $user = Auth::user();

        $validated = $request->validate([
            'title'  => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:1000',
            'notes'  => 'nullable|string|max:500',
        ]);

        $title = trim((string)$request->input('title'));
        if (empty($title)) {
            $title = 'Sertifikat Pelatihan Operasi';
        }

        // Cek apakah ada pengajuan yang masih pending untuk judul yang sama
        $existing = CertificateApplication::where('user_id', $user->id)
            ->where('division', 'pnd')
            ->where('title', $title)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki permohonan ' . $title . ' yang sedang menunggu verifikasi PND.');
        }

        CertificateApplication::create([
            'user_id'  => $user->id,
            'type'     => 'operation_cert',
            'division' => 'pnd',
            'title'    => $title,
            'reason'   => $validated['notes'] ?? $validated['reason'] ?? null,
            'notes'    => $validated['notes'] ?? null,
            'status'   => CertificateApplication::STATUS_PENDING,
        ]);

        return redirect()->route('portal.operation-cert.index')
            ->with('success', 'Permohonan ' . $title . ' berhasil dikirim ke Divisi PND.');
    }

    public function cancel(CertificateApplication $application)
    {
        $user = Auth::user();
        if ($application->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses dan tidak dapat dibatalkan.');
        }

        $application->delete();
        return back()->with('success', 'Pengajuan sertifikat operasi berhasil dibatalkan.');
    }
}
