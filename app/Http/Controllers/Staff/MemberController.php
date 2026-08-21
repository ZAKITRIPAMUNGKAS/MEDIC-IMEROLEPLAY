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

        $query = User::whereNotNull('users.role_id');

        $query->with('role');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.staff_id', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhereHas('role', function ($qr) use ($search) {
                      $qr->where('display_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($hospital !== 'all') {
            $query->where('users.hospital', $hospital);
        }

        $members = $query->join('staff_roles', 'users.role_id', '=', 'staff_roles.id')
            ->where('staff_roles.level', '>=', 0)
            ->select('users.*')
            ->orderByDesc('staff_roles.level')
            ->orderBy('users.name', 'asc')
            ->paginate(12)
            ->withQueryString();

        $schedules = \App\Models\DoctorSchedule::where('is_active', true)->get()->groupBy('doctor_name');

        return view('staff.members.index', compact('members', 'search', 'hospital', 'schedules'));
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
        $managerEvaluations = \App\Models\ManagerEvaluation::with(['evaluator.role'])
            ->where('manager_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

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

        return view('staff.members.show', compact('user', 'stats', 'timeline', 'canViewMedical', 'canSeeAll', 'operations', 'forms', 'managerEvaluations', 'evaluationsAvg', 'evaluationsCount'));
    }
}
