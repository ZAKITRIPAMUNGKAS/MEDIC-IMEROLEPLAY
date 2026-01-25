<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
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

        // Get selected months from request (array)
        $selectedMonths = $request->input('months', []);

        // If no months selected, default to current month
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

        // Build query for rankings
        $rankings = User::select('users.*')
            ->selectRaw('SUM(attendances.session_duration) as total_duty_seconds')
            ->selectRaw('COUNT(attendances.id) as session_count')
            ->selectRaw('AVG(attendances.session_duration) as avg_duty_seconds')
            ->join('attendances', 'users.id', '=', 'attendances.user_id')
            ->whereIn(DB::raw('DATE_FORMAT(attendances.clock_in, "%Y-%m")'), $selectedMonths)
            ->whereNotNull('attendances.clock_out')
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.role_id',
                'users.staff_id',
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
        $stats = [
            'total_staff' => $rankings->total(),
            'total_duty_seconds' => Attendance::whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths)
                ->whereNotNull('clock_out')
                ->sum('session_duration'),
            'total_sessions' => Attendance::whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths)
                ->whereNotNull('clock_out')
                ->count(),
            'avg_duty_seconds' => Attendance::whereIn(DB::raw('DATE_FORMAT(clock_in, "%Y-%m")'), $selectedMonths)
                ->whereNotNull('clock_out')
                ->avg('session_duration'),
        ];

        return view('admin.duty-tracking.index', compact('rankings', 'stats', 'selectedMonths', 'availableMonths'));
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
}
