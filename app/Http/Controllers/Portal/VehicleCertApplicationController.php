<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CertificateApplication;
use App\Models\MemberCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleCertApplicationController extends Controller
{
    public function index()
    {
        CertificateApplication::ensureTableExists();
        $user = Auth::user();

        // Riwayat pengajuan sertifikat kendaraan oleh user
        $applications = CertificateApplication::with('verifiedBy:id,name', 'certification')
            ->where('user_id', $user->id)
            ->where('division', 'ga')
            ->latest()
            ->get();

        // Sertifikat kendaraan resmi yang sudah dimiliki user
        $myCertifications = MemberCertification::with('issuedBy:id,name')
            ->where('user_id', $user->id)
            ->whereIn('type', ['vehicle_land', 'vehicle_heli'])
            ->latest()
            ->get();

        $certificates = $myCertifications;

        return view('portal.member-certs.vehicle', compact('applications', 'myCertifications', 'certificates', 'user'));
    }

    public function store(Request $request)
    {
        CertificateApplication::ensureTableExists();
        $user = Auth::user();

        $validated = $request->validate([
            'type'   => 'nullable|string|in:vehicle_land,vehicle_heli',
            'title'  => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:1000',
            'notes'  => 'nullable|string|max:500',
        ]);

        $type = in_array($request->input('type'), ['vehicle_land', 'vehicle_heli'])
            ? $request->input('type')
            : 'vehicle_land';

        $title = trim((string)$request->input('title'));
        if (empty($title)) {
            $title = $type === 'vehicle_heli'
                ? 'Sertifikasi Penerbang Helikopter Medis & Air Ambulance'
                : 'Sertifikasi Izin Mengemudi Ambulans Medis';
        }

        // Cek apakah ada pengajuan sejenis dengan judul yang sama yang masih pending
        $existing = CertificateApplication::where('user_id', $user->id)
            ->where('title', $title)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki permohonan pengajuan ' . $title . ' yang sedang menunggu peninjauan GA.');
        }

        CertificateApplication::create([
            'user_id'  => $user->id,
            'type'     => $type,
            'division' => 'ga',
            'title'    => $title,
            'reason'   => $validated['notes'] ?? $validated['reason'] ?? null,
            'notes'    => $validated['notes'] ?? null,
            'status'   => CertificateApplication::STATUS_PENDING,
        ]);

        return redirect()->route('portal.vehicle-cert.index')
            ->with('success', 'Permohonan ' . $title . ' berhasil dikirim ke Divisi GA.');
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
        return back()->with('success', 'Pengajuan sertifikat kendaraan berhasil dibatalkan.');
    }
}
