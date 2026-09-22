<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\MedicalForm;
use App\Models\OperationRecord;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of all hospital members.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $hospital = $request->input('hospital', 'all');
        $batch = $request->input('batch', '');

        $query = User::whereNotNull('users.role_id');

        $query->with('role')
              ->withCount(['certifications' => function ($q) {
                  $q->where('status', 'active');
              }]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.staff_id', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.batch', 'like', "%{$search}%")
                  ->orWhereHas('role', function ($qr) use ($search) {
                      $qr->where('display_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($hospital !== 'all') {
            $query->where('users.hospital', $hospital);
        }

        if (!empty($batch)) {
            $query->where(function ($qb) use ($batch) {
                $qb->where('users.batch', $batch);
                if (isset(User::BATCH_LIST[$batch])) {
                    $qb->orWhere('users.batch', User::BATCH_LIST[$batch]['roman']);
                }
            });
        }

        $members = $query->join('staff_roles', 'users.role_id', '=', 'staff_roles.id')
            ->where('staff_roles.level', '>=', 0)
            ->select('users.*')
            ->orderByDesc('staff_roles.level')
            ->orderBy('users.name', 'asc')
            ->paginate(12)
            ->withQueryString();

        $schedules = \App\Models\DoctorSchedule::where('is_active', true)->get()->groupBy('doctor_name');
        $batches = User::BATCH_LIST;

        return view('staff.members.index', compact('members', 'search', 'hospital', 'batch', 'batches', 'schedules'));
    }

    /**
     * Display a specific member's profile with statistics and work timeline.
     */
    public function show(User $user)
    {
        // 1. Calculate stats
        $totalDutySeconds = $user->getTotalDutySeconds();
        $totalDutyFormatted = $user->getTotalDutyHoursFormatted();

        // Count operations where they created, DPJP, or are members
        $totalOperations = OperationRecord::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('dpjp_id', $user->id)
                  ->orWhereHas('members', function ($q) use ($user) {
                      $q->where('users.id', $user->id);
                  });
        })->count();

        // Count forms processed
        $totalFormsProcessed = MedicalForm::where('processed_by', $user->id)->count();

        $stats = [
            'total_duty_seconds' => $totalDutySeconds,
            'total_duty_formatted' => $totalDutyFormatted,
            'total_operations' => $totalOperations,
            'total_forms_processed' => $totalFormsProcessed,
        ];

        // 2. Fetch Timeline Data
        // A. Attendances (Duty Sessions)
        $attendances = Attendance::where('user_id', $user->id)
            ->whereNotNull('clock_out')
            ->orderBy('clock_in', 'desc')
            ->take(50)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'attendance',
                    'title' => 'Selesai Tugas (Clock Out)',
                    'description' => 'Sesi duty selama ' . \App\Helpers\TimeHelper::getHumanReadableDuration($item->session_duration) . ' (' . ($item->notes ?? 'Tugas Biasa') . ')',
                    'timestamp' => $item->clock_out,
                    'data' => $item
                ];
            });

        // B. Operations (Medical Procedures)
        $operations = OperationRecord::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('dpjp_id', $user->id)
                  ->orWhereHas('members', function ($q) use ($user) {
                      $q->where('users.id', $user->id);
                  });
        })
        ->with(['creator', 'dpjp', 'members'])
        ->orderBy('tanggal_waktu', 'desc')
        ->take(50)
        ->get()
        ->map(function ($item) use ($user) {
            $roleLabel = 'Asisten / Anggota';
            if ($item->created_by == $user->id) $roleLabel = 'Pembuat Laporan';
            if ($item->dpjp_id == $user->id) $roleLabel = 'Dokter Penanggung Jawab (DPJP)';

            return [
                'type' => 'operation',
                'title' => 'Tindakan: ' . $item->jenis_operasi,
                'description' => 'Terlibat sebagai ' . $roleLabel . ' di ' . ($item->lokasi ?? 'Alta Hospital'),
                'timestamp' => $item->tanggal_waktu,
                'data' => $item
            ];
        });

        // C. Medical Forms (Surat-surat yang ditangani)
        $forms = MedicalForm::where('processed_by', $user->id)
            ->orderBy('processed_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($item) {
                $statusLabel = $item->status === 'approved' ? 'Menyetujui' : ($item->status === 'rejected' ? 'Menolak' : 'Membatalkan');
                $formTypes = [
                    'surat_kesehatan' => 'Surat Kesehatan',
                    'tes_psikologi' => 'Tes Psikologi',
                    'surat_psikolog' => 'Surat Psikolog',
                    'operasi_plastik' => 'Operasi Plastik',
                    'pendaftaran_karakter' => 'Pendaftaran Karakter'
                ];
                $typeName = $formTypes[$item->form_type] ?? 'Formulir Medis';

                return [
                    'type' => 'medical_form',
                    'title' => $statusLabel . ' ' . $typeName,
                    'description' => 'Memproses surat ' . $typeName . ' dengan status ' . ucfirst($item->status),
                    'timestamp' => $item->processed_at,
                    'data' => $item
                ];
            });

        // Merge and sort timeline
        $timeline = collect()
            ->concat($attendances)
            ->concat($operations)
            ->concat($forms)
            ->sortByDesc('timestamp')
            ->values();

        // Fetch Anonymous Manager Evaluations for this member
        if (\Illuminate\Support\Facades\Schema::hasTable('manager_evaluations')) {
            $managerEvaluations = \App\Models\ManagerEvaluation::with(['evaluator.role'])
                ->where('manager_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $managerEvaluations = collect([]);
        }

        $evaluationsAvg = round($managerEvaluations->avg('rating') ?? 0, 1);
        $evaluationsCount = $managerEvaluations->count();

        $stats['evaluations_avg'] = $evaluationsAvg;
        $stats['evaluations_count'] = $evaluationsCount;

        // 3. Access control check for sensitive data
        $canViewMedical = auth()->user()->isAdmin() || auth()->user()->hasPermission('view_medical_records');
        $canSeeAll = auth()->user()->isAdmin() 
            || strtolower(auth()->user()->role?->name ?? '') === 'admin' 
            || strtolower(auth()->user()->role?->name ?? '') === 'executive' 
            || (auth()->user()->role?->level ?? 0) >= 7;

        // 4. Deteksi Kenaikan Jabatan & Credit Score dari Database
        $user->loadMissing(['role', 'subRole']);
        $promotionTargetRole = null;
        $promotionChecklist = [];
        $promotionProgressPercent = 0;
        $promotionAllMet = false;
        $isHighestLevel = false;
        $creditScore = 100;

        try {
            if (method_exists($user, 'getCreditBalance')) {
                $creditScore = $user->getCreditBalance();
            }
        } catch (\Throwable $e) {
            $creditScore = 100;
        }

        $userHospital = strtolower(trim($user->hospital ?? 'alta'));
        // Deteksi Jabatan Medis staf (bukan manager atau sub-role divisi)
        $currentMedicRole = $user->effective_medic_role;
        if (!$currentMedicRole || !in_array(strtolower($currentMedicRole->name), ['trainee', 'perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'])) {
            if ($user->role && in_array(strtolower($user->role->name), ['trainee', 'perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'])) {
                $currentMedicRole = $user->role;
            } else {
                $currentMedicRole = \App\Models\StaffRole::where('name', 'trainee')->first();
            }
        }

        if ($userHospital === 'alta') {
            $currentMedicLevel = $currentMedicRole?->level ?? 0;
            $currentMedicName = strtolower($currentMedicRole?->name ?? '');

            // Dokter Spesialis (level 4) adalah jenjang klinis/medis tertinggi
            if ($currentMedicLevel >= 4 || $currentMedicName === 'dokter_spesialis') {
                $isHighestLevel = true;
            } else {
                // Target kenaikan promosi berikutnya hanya pada jenjang medis
                $promotionTargetRole = \App\Models\StaffRole::whereIn('name', ['perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'])
                    ->where('level', '>', $currentMedicLevel)
                    ->orderBy('level', 'asc')
                    ->first();

                if ($promotionTargetRole && method_exists($user, 'buildPromotionChecklist')) {
                    try {
                        $promotionChecklist = $user->buildPromotionChecklist($promotionTargetRole);
                        if (!empty($promotionChecklist)) {
                            $metCount = collect($promotionChecklist)->where('met', true)->count();
                            $totalCount = count($promotionChecklist);
                            $promotionProgressPercent = round(($metCount / $totalCount) * 100);
                            $promotionAllMet = ($metCount === $totalCount);
                        }
                    } catch (\Throwable $e) {
                        $promotionChecklist = [];
                    }
                }
            }
        }

        // 5. Sertifikat & Lisensi Medis (GA, MSL, PND, IE)
        $certifications = \App\Models\MemberCertification::with(['issuedBy:id,name'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('issue_date')
            ->get();

        foreach ($certifications as $cert) {
            $needsRegeneration = empty($cert->file_path) 
                || !\Illuminate\Support\Facades\Storage::disk('public')->exists($cert->file_path)
                || !str_ends_with(strtolower($cert->file_path), '.svg');

            if ($needsRegeneration) {
                try {
                    $newPath = \App\Services\CertificateGeneratorService::generate($cert);
                    $cert->update(['file_path' => $newPath]);
                } catch (\Throwable $e) {
                    // silent fallback
                }
            }
        }

        return view('staff.members.show', compact(
            'user', 'stats', 'timeline', 'canViewMedical', 'canSeeAll',
            'operations', 'forms', 'managerEvaluations', 'evaluationsAvg', 'evaluationsCount',
            'promotionTargetRole', 'promotionChecklist', 'promotionProgressPercent', 'promotionAllMet', 'isHighestLevel', 'creditScore',
            'certifications', 'currentMedicRole'
        ));
    }

    /**
     * Direktori Semua Sertifikat & Lisensi Resmi Seluruh Medic
     */
    public function certificates(Request $request)
    {
        $search   = trim($request->input('search', ''));
        $division = $request->input('division', 'all');
        $batch    = $request->input('batch', '');
        $hospital = $request->input('hospital', 'all');

        $query = \App\Models\MemberCertification::with([
            'user:id,name,staff_id,citizen_id,hospital,batch,profile_image,role_id',
            'user.role:id,name,display_name',
            'issuedBy:id,name'
        ])->where('status', 'active');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%")
                        ->orWhere('citizen_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($division !== 'all' && !empty($division)) {
            $query->where('division', $division);
        }

        if ($hospital !== 'all' && !empty($hospital)) {
            $query->whereHas('user', function ($qu) use ($hospital) {
                $qu->where('hospital', $hospital);
            });
        }

        if (!empty($batch)) {
            $query->whereHas('user', function ($qu) use ($batch) {
                $qu->where('batch', $batch);
                if (isset(User::BATCH_LIST[$batch])) {
                    $qu->orWhere('batch', User::BATCH_LIST[$batch]['roman']);
                }
            });
        }

        $certifications = $query->latest('issue_date')->paginate(12)->withQueryString();

        // Regenerate SVG if needed
        foreach ($certifications as $cert) {
            $needsRegeneration = empty($cert->file_path) 
                || !\Illuminate\Support\Facades\Storage::disk('public')->exists($cert->file_path)
                || !str_ends_with(strtolower($cert->file_path), '.svg');

            if ($needsRegeneration) {
                try {
                    $newPath = \App\Services\CertificateGeneratorService::generate($cert);
                    $cert->update(['file_path' => $newPath]);
                } catch (\Throwable $e) {
                    // silent fallback
                }
            }
        }

        $batches = User::BATCH_LIST;
        $totalCerts = \App\Models\MemberCertification::where('status', 'active')->count();

        return view('staff.certificates.index', compact(
            'certifications', 'search', 'division', 'batch', 'hospital', 'batches', 'totalCerts'
        ));
    }
}
