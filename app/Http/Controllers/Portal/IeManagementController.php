<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffRole;
use App\Models\StaffSubRole;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\DutyExemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IeManagementController extends Controller
{
    private function checkIsIe(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('ie')) {
            abort(403, 'Hanya anggota divisi IE (Industrial & Employee Relations) yang memiliki akses ke modul ini.');
        }
    }

    // ─── 1. MANAJEMEN JABATAN OLEH IE ───────────────────────────────────────

    /**
     * Tampilkan antarmuka manajemen jabatan manajerial & medis seluruh staf oleh IE
     */
    public function rolesIndex(Request $request)
    {
        $this->checkIsIe();
        $user = Auth::user();

        $query = User::with(['role', 'medicRole', 'subRole'])
            ->whereNotNull('role_id')
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_active', true);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%")
                  ->orWhere('citizen_id', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->get('role_id')) {
            $query->where('role_id', $roleId);
        }

        if ($medicRoleId = $request->get('medic_role_id')) {
            $query->where('medic_role_id', $medicRoleId);
        }

        if ($subRoleId = $request->get('sub_role_id')) {
            $query->where('sub_role_id', $subRoleId);
        }

        $staffList = $query->orderBy('name')->paginate(25)->withQueryString();

        $allRoles = StaffRole::orderBy('display_name')->get();
        $medicalRoles = StaffRole::whereIn('name', ['trainee', 'perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'])
            ->orderBy('level')
            ->get();
        $subRoles = StaffSubRole::orderBy('sort_order')->get();

        return view('portal.ie.roles', compact('staffList', 'allRoles', 'medicalRoles', 'subRoles'));
    }

    /**
     * Update jabatan medis dan manajerial staf oleh IE
     */
    public function updateRole(Request $request, User $user)
    {
        $this->checkIsIe();

        $validated = $request->validate([
            'role_id'       => 'required|exists:staff_roles,id',
            'medic_role_id' => 'nullable|exists:staff_roles,id',
            'sub_role_id'   => 'nullable|exists:staff_sub_roles,id',
        ]);

        $oldRole = $user->role?->display_name;
        $oldMedic = $user->medicRole?->display_name;

        $user->update([
            'role_id'       => $validated['role_id'],
            'medic_role_id' => $validated['medic_role_id'] ?? null,
            'sub_role_id'   => $validated['sub_role_id'] ?? null,
        ]);

        $newRole = $user->fresh()->role?->display_name;
        $newMedic = $user->fresh()->medicRole?->display_name;

        \Illuminate\Support\Facades\Log::info('[IE-Role-Update] ' . Auth::user()->name . " mengubah jabatan {$user->name}: Role [{$oldRole} -> {$newRole}], Medis [{$oldMedic} -> {$newMedic}]");

        return back()->with('success', "Jabatan staf {$user->name} berhasil diperbarui oleh Divisi IE.");
    }

    // ─── 2. PEMUTIHAN DUTY (< 10 JAM / BULAN) & PENGECUALIAN CUTI ─────────────

    /**
     * Tampilkan daftar pemutihan duty staf medis
     */
    public function pemutihanIndex(Request $request)
    {
        $this->checkIsIe();
        $user = Auth::user();

        // Default bulan berjalan
        $period = $request->get('period', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::createFromFormat('Y-m', $period)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::createFromFormat('Y-m', $period)->endOfMonth()->toDateString();

        // Ambil semua staf aktif medis di rumah sakit
        $allStaff = User::with(['role', 'medicRole', 'subRole'])
            ->where('is_active', true)
            ->whereNotNull('role_id')
            ->where('hospital', $user->hospital ?? 'alta')
            ->orderBy('name')
            ->get();

        // Ambil akumulasi duty per user pada bulan tersebut
        $dutySums = Attendance::selectRaw('user_id, SUM(session_duration) as total_seconds')
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->groupBy('user_id')
            ->pluck('total_seconds', 'user_id');

        // Ambil data pengecualian duty yang sudah dicatat IE untuk bulan ini
        $exemptions = DutyExemption::with('exemptedBy', 'leaveRequest')
            ->where('month_period', $period)
            ->get()
            ->keyBy('user_id');

        // Ambil izin cuti yang disetujui pada bulan tersebut
        $approvedLeaves = LeaveRequest::where('status', 'approved')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($sub) use ($startOfMonth, $endOfMonth) {
                      $sub->where('start_date', '<=', $startOfMonth)
                          ->where('end_date', '>=', $endOfMonth);
                  });
            })
            ->get()
            ->groupBy('user_id');

        $pemutihanList = [];

        foreach ($allStaff as $staf) {
            $totalSeconds = $dutySums[$staf->id] ?? 0;
            $totalHours = round($totalSeconds / 3600, 2);

            // Kriteria pemutihan: total duty < 10 jam dalam satu bulan
            $isUnder10Hours = $totalHours < 10.0;

            $stafLeaves = $approvedLeaves[$staf->id] ?? collect();
            $hasApprovedLeave = $stafLeaves->isNotEmpty();
            $exemption = $exemptions[$staf->id] ?? null;
            $isExempted = $exemption !== null;

            if ($isUnder10Hours) {
                $pemutihanList[] = [
                    'user'               => $staf,
                    'total_hours'        => $totalHours,
                    'total_seconds'      => $totalSeconds,
                    'has_approved_leave' => $hasApprovedLeave,
                    'leaves'             => $stafLeaves,
                    'is_exempted'        => $isExempted,
                    'exemption'          => $exemption,
                ];
            }
        }

        return view('portal.ie.pemutihan', compact('pemutihanList', 'period', 'startOfMonth', 'endOfMonth'));
    }

    /**
     * Berikan / cabut pengecualian pemutihan kepada staf berdasarkan periode cuti
     */
    public function toggleExemption(Request $request, User $user)
    {
        $this->checkIsIe();

        $validated = $request->validate([
            'month_period'     => 'required|string|regex:/^\d{4}-\d{2}$/',
            'leave_request_id' => 'nullable|exists:leave_requests,id',
            'reason'           => 'nullable|string|max:500',
        ]);

        $existing = DutyExemption::where('user_id', $user->id)
            ->where('month_period', $validated['month_period'])
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', "Pengecualian pemutihan untuk {$user->name} pada periode {$validated['month_period']} telah dicabut.");
        }

        DutyExemption::create([
            'user_id'          => $user->id,
            'month_period'     => $validated['month_period'],
            'leave_request_id' => $validated['leave_request_id'] ?? null,
            'exempted_by'      => Auth::id(),
            'reason'           => $validated['reason'] ?? 'Pengecualian pemutihan karena memiliki izin cuti yang sah.',
        ]);

        return back()->with('success', "Staf {$user->name} berhasil dikecualikan dari daftar pemutihan periode {$validated['month_period']}.");
    }
}
