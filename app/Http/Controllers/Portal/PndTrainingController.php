<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TrainingApplication;
use App\Models\MemberCertification;
use App\Services\CertificateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PndTrainingController extends Controller
{
    private function checkIsPnd(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('pnd')) {
            abort(403, 'Akses ditolak. Hanya staf Divisi PND (People & Development) yang dapat mengelola formulir pelatihan.');
        }
    }

    /**
     * Dashboard PND: Kelola Formulir Pendaftaran Pelatihan
     */
    public function index(Request $request)
    {
        $this->checkIsPnd();

        $type   = $request->get('type', 'all');
        $status = $request->get('status', 'all');
        $batch  = $request->get('batch', 'all');
        $q      = $request->get('q', '');

        $query = TrainingApplication::with(['user:id,name,staff_id,citizen_id', 'reviewer:id,name']);

        $validTypes = [
            TrainingApplication::TYPE_OPERASI,
            TrainingApplication::TYPE_SURAT_MENYURAT,
            TrainingApplication::TYPE_VISUM_HIDUP,
            TrainingApplication::TYPE_REKAM_MEDIS,
            TrainingApplication::TYPE_PEMULSARAN_JENAZAH,
        ];

        if ($type !== 'all' && in_array($type, $validTypes)) {
            $query->where('training_type', $type);
        }

        if ($status !== 'all' && in_array($status, [TrainingApplication::STATUS_PENDING, TrainingApplication::STATUS_APPROVED, TrainingApplication::STATUS_REJECTED])) {
            $query->where('status', $status);
        }

        if ($batch !== 'all' && !empty($batch)) {
            $query->where('batch', $batch);
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_ic', 'like', "%{$q}%")
                    ->orWhere('phone_ic', 'like', "%{$q}%")
                    ->orWhere('jabatan', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                           ->orWhere('staff_id', 'like', "%{$q}%");
                    });
            });
        }

        $applications = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total'              => TrainingApplication::count(),
            'pending'            => TrainingApplication::where('status', TrainingApplication::STATUS_PENDING)->count(),
            'approved'           => TrainingApplication::where('status', TrainingApplication::STATUS_APPROVED)->count(),
            'rejected'           => TrainingApplication::where('status', TrainingApplication::STATUS_REJECTED)->count(),
            'operasi'            => TrainingApplication::where('training_type', TrainingApplication::TYPE_OPERASI)->count(),
            'surat_menyurat'     => TrainingApplication::where('training_type', TrainingApplication::TYPE_SURAT_MENYURAT)->count(),
            'visum_hidup'        => TrainingApplication::where('training_type', TrainingApplication::TYPE_VISUM_HIDUP)->count(),
            'rekam_medis'        => TrainingApplication::where('training_type', TrainingApplication::TYPE_REKAM_MEDIS)->count(),
            'pemulsaran_jenazah' => TrainingApplication::where('training_type', TrainingApplication::TYPE_PEMULSARAN_JENAZAH)->count(),
        ];

        // Available batches for filter
        $availableBatches = TrainingApplication::select('batch')
            ->distinct()
            ->orderBy('batch')
            ->pluck('batch');

        return view('portal.pnd.training.index', compact(
            'applications',
            'stats',
            'type',
            'status',
            'batch',
            'q',
            'availableBatches'
        ));
    }

    /**
     * Otomatis terbitkan Sertifikat Resmi di profil anggota ketika pengajuan pelatihan disetujui (5 jenis pelatihan)
     */
    private function grantTrainingCertificate(TrainingApplication $application, ?int $reviewerId = null): ?MemberCertification
    {
        $userId = $application->user_id;
        if (!$userId) {
            return null;
        }

        $config = match ($application->training_type) {
            TrainingApplication::TYPE_OPERASI => [
                'type'     => 'operation_cert',
                'title'    => 'Sertifikat Pelatihan Operasi Medis',
                'division' => 'pnd',
            ],
            TrainingApplication::TYPE_SURAT_MENYURAT => [
                'type'     => 'training_surat_menyurat',
                'title'    => 'Sertifikat Pelatihan Surat Menyurat',
                'division' => 'pnd',
            ],
            TrainingApplication::TYPE_VISUM_HIDUP => [
                'type'     => 'visum_alive',
                'title'    => 'Sertifikat Pelatihan Visum Hidup Medis',
                'division' => 'pnd',
            ],
            TrainingApplication::TYPE_REKAM_MEDIS => [
                'type'     => 'training_rekam_medis',
                'title'    => 'Sertifikat Pelatihan Rekam Medis',
                'division' => 'pnd',
            ],
            TrainingApplication::TYPE_PEMULSARAN_JENAZAH => [
                'type'     => 'training_pemulsaran_jenazah',
                'title'    => 'Sertifikat Pelatihan Pemulsaran Jenazah',
                'division' => 'pnd',
            ],
            default => [
                'type'     => 'training_' . $application->training_type,
                'title'    => 'Sertifikat ' . ($application->type_label ?? 'Pelatihan Medis'),
                'division' => 'pnd',
            ],
        };

        // Cek apakah sertifikat tipe ini sudah ada untuk user
        $cert = MemberCertification::where('user_id', $userId)
            ->where(function ($q) use ($config) {
                $q->where('type', $config['type'])
                  ->orWhere('title', $config['title']);
            })
            ->first();

        $batchInfo = !empty($application->batch) ? " (Batch {$application->batch})" : "";
        $notes = "Diterbitkan otomatis melalui persetujuan {$application->type_label}{$batchInfo}.";

        if (!$cert) {
            $year = now()->format('Y');
            $randomNum = rand(100, 999);
            $certNumber = sprintf('ALTA/PND/%s/%04d', $year, $randomNum);

            $cert = MemberCertification::create([
                'user_id'            => $userId,
                'type'               => $config['type'],
                'division'           => $config['division'],
                'title'              => $config['title'],
                'certificate_number' => $certNumber,
                'issued_by_user_id'  => $reviewerId ?? Auth::id(),
                'issue_date'         => now(),
                'notes'              => $notes,
                'status'             => 'active',
            ]);
        } else {
            $cert->update([
                'status'            => 'active',
                'issued_by_user_id' => $reviewerId ?? Auth::id(),
                'issue_date'        => now(),
                'notes'             => $notes,
            ]);
        }

        // Generate file SVG sertifikat
        try {
            $filePath = CertificateGeneratorService::generate($cert);
            $cert->update(['file_path' => $filePath]);
        } catch (\Throwable $e) {
            Log::warning('[TrainingCert] Gagal generate SVG: ' . $e->getMessage());
        }

        return $cert;
    }

    /**
     * Update individual application status (Approve / Reject / Pending) with review notes
     */
    public function updateStatus(Request $request, TrainingApplication $application)
    {
        $this->checkIsPnd();

        $validated = $request->validate([
            'status'      => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $certNotice = '';
        if ($validated['status'] === 'approved') {
            $this->grantTrainingCertificate($application, Auth::id());
            $certNotice = ' Sertifikat resmi otomatis diterbitkan ke profil anggota.';
        }

        $statusText = match ($validated['status']) {
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            default    => 'dikembalikan ke pending',
        };

        return back()->with('success', "Pendaftaran {$application->nama_ic} ({$application->type_label}) berhasil {$statusText}!{$certNotice}");
    }

    /**
     * Bulk action (approve, reject, delete)
     */
    public function bulkAction(Request $request)
    {
        $this->checkIsPnd();

        $validated = $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:training_applications,id',
            'notes'  => 'nullable|string|max:500',
        ]);

        $ids    = $validated['ids'];
        $action = $validated['action'];
        $notes  = $validated['notes'] ?? null;

        if ($action === 'delete') {
            TrainingApplication::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' data pendaftaran berhasil dihapus.');
        }

        if ($action === 'approve') {
            $applicationsToApprove = TrainingApplication::whereIn('id', $ids)->get();
            foreach ($applicationsToApprove as $appItem) {
                $appItem->update([
                    'status'      => TrainingApplication::STATUS_APPROVED,
                    'admin_notes' => $notes,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
                $this->grantTrainingCertificate($appItem, Auth::id());
            }
            return back()->with('success', count($ids) . ' data pendaftaran berhasil disetujui dan seluruh sertifikat otomatis diterbitkan ke profil anggota.');
        } else {
            TrainingApplication::whereIn('id', $ids)->update([
                'status'      => TrainingApplication::STATUS_REJECTED,
                'admin_notes' => $notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            return back()->with('success', count($ids) . ' data pendaftaran berhasil ditolak.');
        }
    }

    /**
     * Delete application
     */
    public function destroy(TrainingApplication $application)
    {
        $this->checkIsPnd();
        $name = $application->nama_ic;
        $application->delete();

        return back()->with('success', "Data pendaftaran {$name} berhasil dihapus.");
    }

    /**
     * Export Discord announcement for accepted applicants
     */
    public function exportDiscord(Request $request)
    {
        $this->checkIsPnd();

        $type  = $request->get('type', TrainingApplication::TYPE_OPERASI);
        $batch = $request->get('batch', '');

        $query = TrainingApplication::where('training_type', $type)
            ->where('status', TrainingApplication::STATUS_APPROVED);

        if (!empty($batch) && $batch !== 'all') {
            $query->where('batch', $batch);
        }

        $applicants = $query->orderBy('nama_ic')->get();

        $typeLabel = TrainingApplication::typeLabels()[$type] ?? 'Pelatihan';
        $batchLabel = !empty($batch) && $batch !== 'all' ? $batch : 'Semua Batch';

        $template = "# 📋 PENGUMUMAN PESERTA LOLOS SELEKSI " . strtoupper($typeLabel) . "\n";
        $template .= "### {$batchLabel} — IME MEDICAL CENTER\n\n";
        $template .= "**Selamat malam rekan-rekan IME Medical Center,**\n";
        $template .= "**Berikut ini kami sampaikan daftar peserta yang telah resmi disetujui dan terdaftar dalam {$typeLabel} ({$batchLabel}):**\n\n";
        $template .= "```\n";

        if ($applicants->isEmpty()) {
            $template .= "Belum ada peserta yang disetujui untuk filter ini.\n";
        } else {
            foreach ($applicants as $idx => $item) {
                $num = $idx + 1;
                $gender = $item->gender ?? '-';
                $jabatan = $item->jabatan ? " - " . $item->jabatan : "";
                $phone = $item->phone_ic ? " [{$item->phone_ic}]" : "";
                $template .= "{$num}. {$item->nama_ic}{$jabatan} ({$gender}){$phone}\n";
            }
        }

        $template .= "```\n\n";
        $template .= "**Catatan Penting Pelatihan:**\n";
        $template .= "1. Seluruh peserta yang tertera namanya di atas diwajibkan hadir tepat waktu sesuai jadwal yang telah ditentukan.\n";
        $template .= "2. Peserta diharapkan mempersiapkan perlengkapan dan SOP yang berlaku.\n";
        $template .= "3. Apabila berhalangan hadir karena keadaan darurat, mohon segera konfirmasi kepada panitia PND.\n\n";
        $template .= "*Demikian pengumuman ini disampaikan, atas perhatiannya kami ucapkan terima kasih.*\n\n";
        $template .= "*Regards,*\n";
        $template .= "**Divisi People & Development (PND)**\n";
        $template .= "**IME Medical Center**";

        return response()->json([
            'success'  => true,
            'template' => $template,
            'count'    => $applicants->count(),
        ]);
    }
}
