<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Absensi;
use App\Models\User;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DutyMonitoringApiController extends Controller
{
    /**
     * Ambang batas default jam duty untuk syarat Sertifikat Heli
     */
    protected const DEFAULT_HELI_THRESHOLD_HOURS = 10.0;

    /**
     * Endpoint utama monitoring jam duty medis
     * GET /api/duty-hours
     * 
     * Mendukung filter:
     * - period: today, yesterday, 1_week, this_week, last_week, 1_month, this_month, last_month, all
     * - date_from / start_date: YYYY-MM-DD
     * - date_to / end_date: YYYY-MM-DD
     * - citizen_id / staff_id / player_id: string
     * - search / name / q: string
     * - role: string (nama role)
     * - hospital: alta / roxwood
     * - min_hours: float (hanya tampilkan yang duty >= X jam)
     * - heli_threshold: float (default 10 jam)
     * - is_on_duty: 1 / 0 (status aktif sekarang)
     * - include_sessions: 1 / true (sertakan detail sesi per record)
     * - sort_by: total_hours (default), name, sessions, last_duty
     * - sort_dir: desc (default) / asc
     * - per_page / limit: integer (default 50, -1 untuk semua)
     * - page: integer (default 1)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // 1. Tentukan Rentang Tanggal (Date Range)
            [$dateFrom, $dateTo, $periodName] = $this->resolveDateRange($request);

            // 2. Ambang Batas Jam Heli
            $heliThreshold = (float) ($request->get('heli_threshold', $request->get('heli_min_hours', self::DEFAULT_HELI_THRESHOLD_HOURS)));
            if ($heliThreshold <= 0) {
                $heliThreshold = self::DEFAULT_HELI_THRESHOLD_HOURS;
            }

            // 3. Query User Medis
            $userQuery = User::with(['role', 'medicRole']);

            // Filter Rumah Sakit (Alta / Roxwood)
            if ($request->filled('hospital')) {
                $hosp = strtolower(trim($request->get('hospital')));
                if ($hosp === 'roxwood') {
                    $userQuery->roxwood();
                } elseif ($hosp === 'alta') {
                    $userQuery->alta();
                }
            }

            // Filter Identifier (Citizen ID / Staff ID / Player ID)
            $identifier = $request->get('citizen_id', $request->get('staff_id', $request->get('player_id')));
            if (!empty($identifier)) {
                $cleanId = trim($identifier);
                $userQuery->where(function ($q) use ($cleanId) {
                    $q->where('citizen_id', $cleanId)
                      ->orWhere('staff_id', $cleanId)
                      ->orWhere('id', $cleanId);
                });
            }

            // Filter Pencarian Nama
            if ($request->filled('search') || $request->filled('name') || $request->filled('q')) {
                $search = trim($request->get('search', $request->get('name', $request->get('q'))));
                $userQuery->where('name', 'like', "%{$search}%");
            }

            // Filter Role
            if ($request->filled('role')) {
                $roleName = strtolower(trim($request->get('role')));
                $userQuery->whereHas('role', function ($q) use ($roleName) {
                    $q->where('name', 'like', "%{$roleName}%")
                      ->orWhere('display_name', 'like', "%{$roleName}%");
                });
            }

            $users = $userQuery->get();

            // 4. Query Sesi Absensi pada Rentang Tanggal
            $attendanceQuery = Attendance::query();
            if ($dateFrom && $dateTo) {
                $attendanceQuery->where(function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('work_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                      ->orWhereBetween('clock_in', [$dateFrom, $dateTo])
                      ->orWhere(function ($overlap) use ($dateFrom, $dateTo) {
                          $overlap->where('clock_in', '<=', $dateTo)
                                  ->where(function ($out) use ($dateFrom) {
                                      $out->where('clock_out', '>=', $dateFrom)
                                          ->orWhereNull('clock_out');
                                  });
                      });
                });
            }

            $attendancesByUser = $attendanceQuery->orderBy('clock_in', 'desc')
                ->get()
                ->groupBy('user_id');

            // 5. Agregasi Data Per Staf
            $includeSessions = filter_var($request->get('include_sessions', false), FILTER_VALIDATE_BOOLEAN);
            $results = collect();

            foreach ($users as $user) {
                $userAttendances = $attendancesByUser->get($user->id, collect());
                
                $totalSeconds = 0;
                $activeSessionFound = false;
                $lastDutyTime = null;
                $formattedSessions = [];

                foreach ($userAttendances as $att) {
                    $sessionSecs = $this->calculateSessionSeconds($att);
                    $totalSeconds += $sessionSecs;

                    if ($att->is_active && is_null($att->clock_out)) {
                        $activeSessionFound = true;
                    }

                    if (is_null($lastDutyTime) || ($att->clock_in && $att->clock_in->gt($lastDutyTime))) {
                        $lastDutyTime = $att->clock_in;
                    }

                    if ($includeSessions) {
                        $formattedSessions[] = [
                            'session_id'   => $att->id,
                            'work_date'    => $att->work_date ? $att->work_date->toDateString() : ($att->clock_in ? $att->clock_in->toDateString() : null),
                            'clock_in'     => $att->clock_in ? $att->clock_in->toDateTimeString() : null,
                            'clock_out'    => $att->clock_out ? $att->clock_out->toDateTimeString() : null,
                            'duration_sec' => $sessionSecs,
                            'duration_fmt' => TimeHelper::formatDuration($sessionSecs),
                            'is_active'    => (bool) $att->is_active,
                            'source'       => $att->source ?? 'fivem',
                            'notes'        => $att->notes
                        ];
                    }
                }

                $totalHours = round($totalSeconds / 3600, 2);
                $totalMinutes = (int) round($totalSeconds / 60);
                $hoursPart = floor($totalSeconds / 3600);
                $minutesPart = floor(($totalSeconds % 3600) / 60);

                // Dapatkan nama role yang informatif
                $roleDisplay = $user->role ? $user->role->display_name : '-';
                if ($user->medicRole && $user->medicRole->id !== $user->role_id) {
                    $roleDisplay .= ' (' . $user->medicRole->display_name . ')';
                }

                $entry = [
                    'user_id'              => $user->id,
                    'citizen_id'           => $user->citizen_id ?? $user->staff_id ?? '-',
                    'staff_id'             => $user->staff_id ?? $user->citizen_id ?? '-',
                    'name'                 => $user->name,
                    'hospital'             => ucfirst($user->hospital ?? 'Alta Hospital'),
                    'role'                 => $roleDisplay,
                    'role_name'            => $user->role?->name ?? 'unknown',
                    'total_hours'          => $totalHours,
                    'total_minutes'        => $totalMinutes,
                    'total_seconds'        => $totalSeconds,
                    'formatted_duty_time'  => sprintf('%d Jam %d Menit', $hoursPart, $minutesPart),
                    'formatted_clock'      => TimeHelper::formatDuration($totalSeconds),
                    'total_sessions'       => $userAttendances->count(),
                    'is_currently_on_duty' => $activeSessionFound,
                    'last_duty_at'         => $lastDutyTime ? $lastDutyTime->toDateTimeString() : null,
                    'heli_eligible'        => ($totalHours >= $heliThreshold),
                    'heli_threshold_hours' => $heliThreshold,
                    'heli_hours_remaining' => max(0, round($heliThreshold - $totalHours, 2)),
                ];

                if ($includeSessions) {
                    $entry['sessions'] = $formattedSessions;
                }

                $results->push($entry);
            }

            // 6. Filter Lanjutan
            // Filter On Duty saat ini
            if ($request->has('is_on_duty')) {
                $onDutyFilter = filter_var($request->get('is_on_duty'), FILTER_VALIDATE_BOOLEAN);
                $results = $results->filter(fn($r) => $r['is_currently_on_duty'] === $onDutyFilter);
            }

            // Filter Minimal Jam Duty (min_hours)
            if ($request->filled('min_hours')) {
                $minHours = (float) $request->get('min_hours');
                $results = $results->filter(fn($r) => $r['total_hours'] >= $minHours);
            }

            // Filter Hanya yang Memenuhi Syarat Heli
            if ($request->has('heli_eligible_only') && filter_var($request->get('heli_eligible_only'), FILTER_VALIDATE_BOOLEAN)) {
                $results = $results->filter(fn($r) => $r['heli_eligible'] === true);
            }

            // Filter Hanya yang Punya Jam Duty > 0 (jika diminta)
            if ($request->has('duty_only') && filter_var($request->get('duty_only'), FILTER_VALIDATE_BOOLEAN)) {
                $results = $results->filter(fn($r) => $r['total_seconds'] > 0);
            }

            // 7. Pengurutan (Sorting)
            $sortBy = $request->get('sort_by', 'total_hours');
            $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            $sortKey = match($sortBy) {
                'name'      => 'name',
                'sessions'  => 'total_sessions',
                'last_duty' => 'last_duty_at',
                default     => 'total_hours',
            };

            $results = $sortDir === 'asc' 
                ? $results->sortBy($sortKey)->values() 
                : $results->sortByDesc($sortKey)->values();

            // 8. Ringkasan Global Periode Ini
            $summary = [
                'total_medical_staff' => $results->count(),
                'active_duty_staff'   => $results->where('total_seconds', '>', 0)->count(),
                'currently_on_duty'   => $results->where('is_currently_on_duty', true)->count(),
                'heli_eligible_count' => $results->where('heli_eligible', true)->count(),
                'heli_threshold_hours'=> $heliThreshold,
                'cumulative_hours'    => round($results->sum('total_hours'), 2),
                'cumulative_minutes'  => (int) $results->sum('total_minutes'),
            ];

            // 9. Pagination
            $perPage = (int) $request->get('per_page', $request->get('limit', 50));
            $page = max(1, (int) $request->get('page', 1));

            if ($perPage > 0) {
                $totalRecords = $results->count();
                $totalPages = (int) ceil($totalRecords / $perPage);
                $paginatedData = $results->slice(($page - 1) * $perPage, $perPage)->values();

                $pagination = [
                    'current_page' => $page,
                    'per_page'     => $perPage,
                    'total_items'  => $totalRecords,
                    'total_pages'  => $totalPages,
                    'has_next'     => ($page < $totalPages),
                    'has_prev'     => ($page > 1),
                ];
            } else {
                // Return all (limit=-1)
                $paginatedData = $results;
                $pagination = [
                    'current_page' => 1,
                    'per_page'     => $results->count(),
                    'total_items'  => $results->count(),
                    'total_pages'  => 1,
                    'has_next'     => false,
                    'has_prev'     => false,
                ];
            }

            return response()->json([
                'success'    => true,
                'period'     => $periodName,
                'date_range' => [
                    'from' => $dateFrom ? $dateFrom->toDateTimeString() : null,
                    'to'   => $dateTo ? $dateTo->toDateTimeString() : null,
                ],
                'summary'    => $summary,
                'pagination' => $pagination,
                'data'       => $paginatedData,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('DutyMonitoringApi index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat memproses data duty: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint ringkasan singkat & leaderboard duty
     * GET /api/duty-hours/summary
     */
    public function summary(Request $request): JsonResponse
    {
        // Panggil index dengan limit 5 untuk top duty
        $request->merge(['limit' => 5, 'sort_by' => 'total_hours', 'sort_dir' => 'desc', 'duty_only' => true]);
        $response = $this->index($request);
        $original = $response->getData(true);

        if (!$original['success']) {
            return $response;
        }

        return response()->json([
            'success'        => true,
            'period'         => $original['period'],
            'date_range'     => $original['date_range'],
            'summary'        => $original['summary'],
            'top_duty_staff' => $original['data'],
        ], 200);
    }

    /**
     * Endpoint detail jam duty untuk 1 staf spesifik
     * GET /api/duty-hours/{identifier}
     */
    public function show(Request $request, $identifier): JsonResponse
    {
        try {
            $cleanId = trim($identifier);

            // Cari User berdasarkan citizen_id, staff_id, atau id
            $user = User::with(['role', 'medicRole'])
                ->where('citizen_id', $cleanId)
                ->orWhere('staff_id', $cleanId)
                ->orWhere('id', $cleanId)
                ->orWhere('name', 'like', "%{$cleanId}%")
                ->first();

            // 1. Tentukan Rentang Tanggal
            [$dateFrom, $dateTo, $periodName] = $this->resolveDateRange($request);
            $heliThreshold = (float) ($request->get('heli_threshold', $request->get('heli_min_hours', self::DEFAULT_HELI_THRESHOLD_HOURS)));

            if ($user) {
                // Ambil attendances milik user
                $attendanceQuery = Attendance::where('user_id', $user->id);
                if ($dateFrom && $dateTo) {
                    $attendanceQuery->where(function ($q) use ($dateFrom, $dateTo) {
                        $q->whereBetween('work_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                          ->orWhereBetween('clock_in', [$dateFrom, $dateTo]);
                    });
                }

                $attendances = $attendanceQuery->orderBy('clock_in', 'desc')->get();
                $totalSeconds = 0;
                $activeSession = null;
                $sessions = [];

                foreach ($attendances as $att) {
                    $secs = $this->calculateSessionSeconds($att);
                    $totalSeconds += $secs;

                    if ($att->is_active && is_null($att->clock_out)) {
                        $activeSession = $att;
                    }

                    $sessions[] = [
                        'session_id'   => $att->id,
                        'work_date'    => $att->work_date ? $att->work_date->toDateString() : ($att->clock_in ? $att->clock_in->toDateString() : null),
                        'clock_in'     => $att->clock_in ? $att->clock_in->toDateTimeString() : null,
                        'clock_out'    => $att->clock_out ? $att->clock_out->toDateTimeString() : null,
                        'duration_sec' => $secs,
                        'duration_fmt' => TimeHelper::formatDuration($secs),
                        'is_active'    => (bool) $att->is_active,
                        'source'       => $att->source ?? 'fivem',
                        'notes'        => $att->notes
                    ];
                }

                $totalHours = round($totalSeconds / 3600, 2);
                $totalMinutes = (int) round($totalSeconds / 60);

                $roleDisplay = $user->role ? $user->role->display_name : '-';
                if ($user->medicRole && $user->medicRole->id !== $user->role_id) {
                    $roleDisplay .= ' (' . $user->medicRole->display_name . ')';
                }

                return response()->json([
                    'success'    => true,
                    'period'     => $periodName,
                    'date_range' => [
                        'from' => $dateFrom ? $dateFrom->toDateTimeString() : null,
                        'to'   => $dateTo ? $dateTo->toDateTimeString() : null,
                    ],
                    'data' => [
                        'user_id'              => $user->id,
                        'citizen_id'           => $user->citizen_id ?? $user->staff_id ?? '-',
                        'staff_id'             => $user->staff_id ?? $user->citizen_id ?? '-',
                        'name'                 => $user->name,
                        'hospital'             => ucfirst($user->hospital ?? 'Alta Hospital'),
                        'role'                 => $roleDisplay,
                        'total_hours'          => $totalHours,
                        'total_minutes'        => $totalMinutes,
                        'formatted_duty_time'  => sprintf('%d Jam %d Menit', floor($totalSeconds / 3600), floor(($totalSeconds % 3600) / 60)),
                        'formatted_clock'      => TimeHelper::formatDuration($totalSeconds),
                        'total_sessions'       => $attendances->count(),
                        'is_currently_on_duty' => !is_null($activeSession),
                        'current_active_session' => $activeSession ? [
                            'session_id' => $activeSession->id,
                            'clock_in'   => $activeSession->clock_in?->toDateTimeString(),
                            'active_for' => TimeHelper::formatDuration($activeSession->clock_in ? $activeSession->clock_in->diffInSeconds(now()) : 0),
                        ] : null,
                        'heli_status' => [
                            'eligible'         => ($totalHours >= $heliThreshold),
                            'threshold_hours'  => $heliThreshold,
                            'hours_achieved'   => $totalHours,
                            'hours_remaining'  => max(0, round($heliThreshold - $totalHours, 2)),
                            'percentage'       => min(100, round(($totalHours / $heliThreshold) * 100, 1)),
                        ],
                        'sessions' => $sessions
                    ]
                ], 200);
            }

            // Fallback: Jika tidak ditemukan di User, cari di tabel Absensi FiveM
            $absensiQuery = Absensi::byPlayer($cleanId);
            if ($dateFrom && $dateTo) {
                $absensiQuery->whereBetween('clock_in', [$dateFrom, $dateTo]);
            }
            $absensiList = $absensiQuery->orderBy('clock_in', 'desc')->get();

            if ($absensiList->isNotEmpty()) {
                $totalSecs = 0;
                $activeAbsensi = null;
                $sessions = [];

                foreach ($absensiList as $abs) {
                    $sec = 0;
                    if ($abs->time_on_duty && preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $abs->time_on_duty, $m)) {
                        $sec = ((int)$m[1] * 3600) + ((int)$m[2] * 60) + (int)$m[3];
                    } elseif ($abs->clock_in && $abs->clock_out) {
                        $sec = $abs->clock_in->diffInSeconds($abs->clock_out);
                    } elseif ($abs->clock_in && is_null($abs->clock_out)) {
                        $sec = $abs->clock_in->diffInSeconds(now());
                        $activeAbsensi = $abs;
                    }
                    $totalSecs += $sec;

                    $sessions[] = [
                        'clock_in'     => $abs->clock_in?->toDateTimeString(),
                        'clock_out'    => $abs->clock_out?->toDateTimeString(),
                        'duration_fmt' => TimeHelper::formatDuration($sec),
                        'is_active'    => is_null($abs->clock_out),
                    ];
                }

                $hours = round($totalSecs / 3600, 2);

                return response()->json([
                    'success'    => true,
                    'source'     => 'fivem_absensi_raw',
                    'period'     => $periodName,
                    'date_range' => [
                        'from' => $dateFrom ? $dateFrom->toDateTimeString() : null,
                        'to'   => $dateTo ? $dateTo->toDateTimeString() : null,
                    ],
                    'data' => [
                        'player_id'            => $cleanId,
                        'name'                 => $absensiList->first()->player_name ?? $cleanId,
                        'role'                 => 'Staff Medis (FiveM)',
                        'hospital'             => 'Alta Hospital',
                        'total_hours'          => $hours,
                        'total_minutes'        => (int) round($totalSecs / 60),
                        'formatted_duty_time'  => sprintf('%d Jam %d Menit', floor($totalSecs / 3600), floor(($totalSecs % 3600) / 60)),
                        'formatted_clock'      => TimeHelper::formatDuration($totalSecs),
                        'total_sessions'       => $absensiList->count(),
                        'is_currently_on_duty' => !is_null($activeAbsensi),
                        'heli_status' => [
                            'eligible'         => ($hours >= $heliThreshold),
                            'threshold_hours'  => $heliThreshold,
                            'hours_achieved'   => $hours,
                            'hours_remaining'  => max(0, round($heliThreshold - $hours, 2)),
                            'percentage'       => min(100, round(($hours / $heliThreshold) * 100, 1)),
                        ],
                        'sessions' => $sessions
                    ]
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => "Staf medis dengan identitas '{$cleanId}' tidak ditemukan di sistem."
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hitung durasi sesi absensi dalam detik (akurat & toleran real-time)
     */
    protected function calculateSessionSeconds(Attendance $attendance): int
    {
        if ($attendance->session_duration && $attendance->session_duration > 0) {
            return (int) $attendance->session_duration;
        }

        if ($attendance->is_active && $attendance->clock_in) {
            $now = Carbon::now('Asia/Jakarta');
            return max(0, (int) $attendance->clock_in->diffInSeconds($now));
        }

        if ($attendance->clock_in && $attendance->clock_out) {
            return max(0, (int) $attendance->clock_in->diffInSeconds($attendance->clock_out));
        }

        if ($attendance->total_hours) {
            return (int) ($attendance->total_hours * 60);
        }

        return 0;
    }

    /**
     * Resolusi rentang tanggal dari request
     */
    protected function resolveDateRange(Request $request): array
    {
        // 1. Jika ada parameter tanggal kustom
        if ($request->filled('date_from') || $request->filled('start_date')) {
            $fromStr = $request->get('date_from', $request->get('start_date'));
            $toStr = $request->get('date_to', $request->get('end_date', now()->toDateTimeString()));

            $dateFrom = Carbon::parse($fromStr)->startOfDay();
            $dateTo = Carbon::parse($toStr)->endOfDay();

            return [$dateFrom, $dateTo, 'custom_range'];
        }

        // 2. Berdasarkan pilihan period
        $period = strtolower(trim($request->get('period', 'this_month')));

        return match ($period) {
            'today', '1_day'          => [now()->startOfDay(), now()->endOfDay(), 'today'],
            'yesterday'               => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay(), 'yesterday'],
            'this_week', 'week'       => [now()->startOfWeek(), now()->endOfWeek(), 'this_week'],
            'last_week'               => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek(), 'last_week'],
            '1_week', '7_days'        => [now()->subDays(6)->startOfDay(), now()->endOfDay(), '1_week'],
            'this_month', 'month'     => [now()->startOfMonth(), now()->endOfMonth(), 'this_month'],
            'last_month'              => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(), 'last_month'],
            '1_month', '30_days'      => [now()->subDays(29)->startOfDay(), now()->endOfDay(), '1_month'],
            'all', 'all_time'         => [null, null, 'all_time'],
            default                   => [now()->startOfMonth(), now()->endOfMonth(), 'this_month'],
        };
    }
}
