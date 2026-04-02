<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\MedicalForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DutyTrackingController extends Controller
{
    /**
     * Display duty tracking dashboard with rankings.
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $mode = $request->input('mode', 'monthly');

        // Get selected months from request (array)
        $selectedMonths = $request->input('months', []);
        if (empty($selectedMonths)) {
            $selectedMonths = [Carbon::now()->format('Y-m')];
        }

        // Get available months from attendance data
        $availableMonths = Attendance::selectRaw('DATE_FORMAT(clock_in, "%Y-%m") as month')
            ->whereNotNull('clock_out')
            ->groupBy('month')
            ->orderByDesc('month')
            ->pluck('month')
            ->toArray();

        // Get selected weeks from request (array)
        $selectedWeeks = $request->input('weeks', []);
        if (empty($selectedWeeks)) {
            $selectedWeeks = [Carbon::now()->startOfWeek()->format('Y-m-d')];
        }

        // Get available weeks (Mondays)
        $availableWeeks = Attendance::selectRaw('DATE(DATE_SUB(clock_in, INTERVAL WEEKDAY(clock_in) DAY)) as week_start')
            ->whereNotNull('clock_out')
            ->groupBy('week_start')
            ->orderByDesc('week_start')
            ->pluck('week_start')
            ->toArray();

        $hospital = $request->input('hospital', 'all');

        // Build query for rankings
        $rankingsQuery = User::select('users.*')
            ->selectRaw('SUM(attendances.session_duration) as total_duty_seconds')
            ->selectRaw('COUNT(attendances.id) as session_count')
            ->selectRaw('AVG(attendances.session_duration) as avg_duty_seconds')
            ->join('attendances', 'users.id', '=', 'attendances.user_id')
            ->whereNotNull('attendances.clock_out');

        if ($hospital !== 'all') {
            $rankingsQuery->where('users.hospital', $hospital);
        }

        if ($mode === 'weekly') {
            $rankingsQuery->whereIn(DB::raw('DATE(DATE_SUB(attendances.clock_in, INTERVAL WEEKDAY(attendances.clock_in) DAY))'), $selectedWeeks);
        } else {
            $rankingsQuery->whereIn(DB::raw('DATE_FORMAT(attendances.clock_in, "%Y-%m")'), $selectedMonths);
        }

        $rankings = $rankingsQuery->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.role_id',
                'users.staff_id',
                'users.citizen_id',
                'users.hospital',
                'users.is_active',
                'users.profile_image',
                'users.custom_permissions',
                'users.custom_salary',
                'users.status',
                'users.created_at',
                'users.updated_at',
                'users.password',
                'users.remember_token',
                'users.email_verified_at'
            )
            ->orderByDesc('total_duty_seconds')
            ->paginate(50);

        // Calculate overall statistics
        $statsQuery = Attendance::whereNotNull('clock_out');
        if ($hospital !== 'all') {
            $statsQuery->whereHas('user', function($q) use ($hospital) {
                $q->where('hospital', $hospital);
            });
        }
        
        if ($mode === 'weekly') {
            $statsQuery->whereIn(DB::raw('DATE(DATE_SUB(clock_in, INTERVAL WEEKDAY(clock_in) DAY))'), $selectedWeeks);
        } else {
            $statsQuery->whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths);
        }

        $stats = [
            'total_staff' => $rankings->total(),
            'total_duty_seconds' => (clone $statsQuery)->sum('session_duration'),
            'total_sessions' => (clone $statsQuery)->count(),
            'avg_duty_seconds' => (clone $statsQuery)->avg('session_duration'),
        ];

        // Calculate Service Letter Statistics (Top Approvers Overall)
        $topApproversQuery = MedicalForm::select('processed_by', DB::raw('count(*) as total'))
            ->with([
                'processedBy' => function ($query) {
                    $query->select('id', 'name', 'profile_image', 'role_id', 'staff_id')
                        ->with('role:id,name');
                }
            ])
            ->where('status', 'approved')
            ->whereNotNull('processed_by');
            
        if ($mode === 'weekly') {
            $topApproversQuery->whereIn(DB::raw('DATE(DATE_SUB(processed_at, INTERVAL WEEKDAY(processed_at) DAY))'), $selectedWeeks);
        } else {
            $topApproversQuery->whereIn(DB::raw('DATE_FORMAT(processed_at, "%Y-%m")'), $selectedMonths);
        }

        if ($hospital !== 'all') {
            $topApproversQuery->whereHas('processedBy', function($q) use ($hospital) {
                $q->where('hospital', $hospital);
            });
        }

        $topApprovers = $topApproversQuery->groupBy('processed_by')
            ->orderByDesc('total')
            ->take(4)
            ->get();

        return view('admin.duty-tracking.index', compact('rankings', 'stats', 'selectedMonths', 'availableMonths', 'selectedWeeks', 'availableWeeks', 'topApprovers', 'mode', 'hospital'));
    }

    /**
     * Show detailed duty history for a specific user.
     */
    public function show(User $user, Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Get selected months
        $selectedMonths = $request->input('months', []);

        if (empty($selectedMonths)) {
            $selectedMonths = [Carbon::now()->format('Y-m')];
        }

        // Get duty history
        $query = Attendance::where('user_id', $user->id)
            ->whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths)
            ->whereNotNull('clock_out')
            ->orderBy('clock_in', 'desc');

        $attendances = $query->paginate(50);

        // Calculate stats for this user
        $stats = [
            'total_duty_seconds' => Attendance::where('user_id', $user->id)
                ->whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths)
                ->whereNotNull('clock_out')
                ->sum('session_duration'),
            'session_count' => $attendances->total(),
            'avg_duty_seconds' => Attendance::where('user_id', $user->id)
                ->whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths)
                ->whereNotNull('clock_out')
                ->avg('session_duration'),
        ];

        // Get available months for this user
        $availableMonths = Attendance::where('user_id', $user->id)
            ->selectRaw('DATE_FORMAT(clock_in, "%Y-%m") as month')
            ->whereNotNull('clock_out')
            ->groupBy('month')
            ->orderByDesc('month')
            ->pluck('month')
            ->toArray();

        return view('admin.duty-tracking.show', compact('user', 'attendances', 'stats', 'selectedMonths', 'availableMonths'));
    }

    /**
     * Export Top Leader Mingguan to CSV.
     */
    public function exportWeekly(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $selectedWeeks = $request->input('weeks', []);
        if (empty($selectedWeeks)) {
            $selectedWeeks = [Carbon::now()->startOfWeek()->format('Y-m-d')];
        }

        $hospital = $request->input('hospital', 'all');

        $rankingsQuery = User::select('users.*')
            ->selectRaw('SUM(attendances.session_duration) as total_duty_seconds')
            ->selectRaw('COUNT(attendances.id) as session_count')
            ->join('attendances', 'users.id', '=', 'attendances.user_id')
            ->whereNotNull('attendances.clock_out')
            ->whereIn(DB::raw('DATE(DATE_SUB(attendances.clock_in, INTERVAL WEEKDAY(attendances.clock_in) DAY))'), $selectedWeeks);
            
        if ($hospital !== 'all') {
            $rankingsQuery->where('users.hospital', $hospital);
        }

        $rankings = $rankingsQuery->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.role_id',
                'users.staff_id',
                'users.citizen_id',
                'users.hospital',
                'users.is_active',
                'users.profile_image',
                'users.custom_permissions',
                'users.custom_salary',
                'users.status',
                'users.created_at',
                'users.updated_at',
                'users.password',
                'users.remember_token',
                'users.email_verified_at'
            )
            ->orderByDesc('total_duty_seconds')
            ->get();

        $weekStr = implode('_', $selectedWeeks);
        $filename = 'top_leader_mingguan_' . $weekStr . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rankings, $selectedWeeks) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
            
            $weekLabel = collect($selectedWeeks)->map(function ($w) {
                return Carbon::parse($w)->format('d/m/Y') . ' - ' . Carbon::parse($w)->endOfWeek()->format('d/m/Y');
            })->implode(', ');

            fputcsv($file, ['TOP LEADER MINGGUAN']);
            fputcsv($file, ['Minggu: ' . $weekLabel]);
            fputcsv($file, []);
            fputcsv($file, ['Rank', 'Nama', 'Role', 'Total Sesi', 'Total Jam Duty']);

            $rank = 1;
            foreach ($rankings as $user) {
                fputcsv($file, [
                    $rank++,
                    $user->name,
                    $user->role->display_name ?? 'Staff',
                    $user->session_count,
                    \App\Helpers\TimeHelper::formatDuration($user->total_duty_seconds)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
