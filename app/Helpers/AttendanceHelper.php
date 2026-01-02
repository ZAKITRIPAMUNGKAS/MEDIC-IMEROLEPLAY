<?php

namespace App\Helpers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceHelper
{
    /**
     * Get weekly leaderboard data
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWeeklyLeaderboard(int $limit = 10)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return User::with(['role'])
            ->whereHas('attendances', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                    ->where('session_type', 'work')
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0);
            })
            ->withCount([
                'attendances' => function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                        ->where('session_type', 'work')
                        ->whereNotNull('session_duration')
                        ->where('session_duration', '>', 0);
                }
            ])
            ->withSum([
                'attendances' => function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                        ->whereIn('session_type', ['work', 'meeting'])
                        ->whereNotNull('session_duration')
                        ->where('session_duration', '>', 0);
                }
            ], 'session_duration')
            ->orderBy('attendances_sum_session_duration', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user) use ($startOfWeek, $endOfWeek) {
                // Add unique work days count
                $uniqueWorkDays = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                    ->where('session_type', 'work')
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->distinct('work_date')
                    ->count('work_date');

                $user->unique_work_days = $uniqueWorkDays;
                $user->total_hours_formatted = TimeHelper::formatDuration($user->attendances_sum_session_duration);
                $user->total_hours_decimal = TimeHelper::secondsToHours($user->attendances_sum_session_duration);

                return $user;
            });
    }

    /**
     * Get total number of times a user has been #1 in weekly leaderboard
     *
     * @param int $userId
     * @param string|null $userHospital Filter by hospital (alta/roxwood) or null for all
     * @return int
     */
    /**
     * Get total number of times a user has been #1 in weekly leaderboard
     * Optimized with Caching
     *
     * @param int $userId
     * @param string|null $userHospital Filter by hospital (alta/roxwood) or null for all
     * @return int
     */
    public static function getTotalJuara1Count(int $userId, ?string $userHospital = null): int
    {
        $cacheKey = "weekly_stats_history_" . ($userHospital ?? 'all');

        // 1. Get History (Past Weeks) - Cached for 24 hours
        // Returns array of user_ids who won in past weeks
        $historicalWinners = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60 * 60 * 24, function () use ($userHospital) {
            return self::calculateHistoricalWinners($userHospital);
        });

        // 2. Get Current Week Winner - Cached for 1 hour (updates more frequently)
        $currentWeekKey = "weekly_stats_current_" . ($userHospital ?? 'all') . "_" . now()->format('Y_W');
        $currentWinner = \Illuminate\Support\Facades\Cache::remember($currentWeekKey, 60 * 60, function () use ($userHospital) {
            return self::calculateCurrentWeekWinner($userHospital);
        });

        // 3. Count occurrences
        $count = 0;

        // Count from history
        foreach ($historicalWinners as $winnerId) {
            if ($winnerId == $userId) {
                $count++;
            }
        }

        // Count current week
        if ($currentWinner && $currentWinner == $userId) {
            $count++;
        }

        return $count;
    }

    /**
     * Calculate winners for all past completed weeks
     */
    private static function calculateHistoricalWinners($userHospital)
    {
        $winners = [];

        // Get all distinct weeks EXCLUDING current week
        // Using YEARWEEK mode 1 (Monday-Sunday)
        $currentYearWeek = now()->format('oW'); // ISO YearWeek

        $weeks = Attendance::selectRaw('YEARWEEK(work_date, 1) as week_year, MIN(work_date) as week_start')
            ->whereIn('session_type', ['work', 'meeting'])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->whereRaw('YEARWEEK(work_date, 1) < ?', [$currentYearWeek]) // Only past weeks
            ->groupBy('week_year')
            ->orderBy('week_year', 'desc')
            ->get();

        foreach ($weeks as $week) {
            $weekStart = Carbon::parse($week->week_start)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $winnerId = self::getWinnerForPeriod($weekStart, $weekEnd, $userHospital);

            if ($winnerId) {
                $winners[] = $winnerId;
            }
        }

        return $winners;
    }

    /**
     * Calculate winner for the current active week
     */
    private static function calculateCurrentWeekWinner($userHospital)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return self::getWinnerForPeriod($startOfWeek, $endOfWeek, $userHospital);
    }

    /**
     * Helper to find winner ID for a specific date range
     */
    private static function getWinnerForPeriod($startDate, $endDate, $userHospital)
    {
        // Build query for top user
        $topUserQuery = User::whereHas('attendances', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('work_date', [$startDate, $endDate])
                ->whereIn('session_type', ['work', 'meeting'])
                ->whereNotNull('session_duration')
                ->where('session_duration', '>', 0);
        });

        // Apply hospital filter
        if ($userHospital) {
            if ($userHospital === 'alta') {
                $topUserQuery->where(function ($q) {
                    $q->whereRaw('LOWER(name) NOT LIKE ?', ['%rh%'])
                        ->whereRaw('LOWER(name) NOT LIKE ?', ['%roxwood%'])
                        ->whereRaw('LOWER(name) NOT LIKE ?', ['%rh -%'])
                        ->whereRaw('LOWER(name) NOT LIKE ?', ['%rh-%'])
                        ->where(function ($sq) {
                            $sq->whereNull('staff_id')
                                ->orWhere(function ($ssq) {
                                    $ssq->whereRaw('LOWER(staff_id) NOT LIKE ?', ['%rh%'])
                                        ->whereRaw('LOWER(staff_id) NOT LIKE ?', ['%rh -%'])
                                        ->whereRaw('LOWER(staff_id) NOT LIKE ?', ['%rh-%']);
                                });
                        });
                });
            } else {
                $topUserQuery->where(function ($q) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%rh%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%roxwood%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%rh -%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%rh-%'])
                        ->orWhere(function ($sq) {
                            $sq->whereNotNull('staff_id')
                                ->where(function ($ssq) {
                                    $ssq->whereRaw('LOWER(staff_id) LIKE ?', ['%rh%'])
                                        ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh -%'])
                                        ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh-%']);
                                });
                        });
                });
            }
        }

        $topUser = $topUserQuery
            ->withSum([
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('work_date', [$startDate, $endDate])
                        ->whereIn('session_type', ['work', 'meeting'])
                        ->whereNotNull('session_duration')
                        ->where('session_duration', '>', 0);
                }
            ], 'session_duration')
            ->orderBy('attendances_sum_session_duration', 'desc')
            ->first();

        return $topUser ? $topUser->id : null;
    }

    /**
     * Get user weekly statistics
     *
     * @param int $userId
     * @return \stdClass|null
     */
    public static function getUserWeeklyStats(int $userId)
    {
        $stats = Attendance::where('user_id', $userId)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('session_type', ['work', 'meeting'])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->selectRaw('
                COUNT(DISTINCT work_date) as total_days,
                COALESCE(SUM(session_duration), 0) as total_seconds,
                COALESCE(AVG(session_duration), 0) as avg_seconds_per_day
            ')
            ->first();

        if ($stats) {
            $stats->total_hours = TimeHelper::secondsToHours((int) $stats->total_seconds);
            $stats->avg_hours_per_day = TimeHelper::secondsToHours((int) $stats->avg_seconds_per_day);
            $stats->total_hours_formatted = TimeHelper::formatDuration((int) $stats->total_seconds);
        }

        return $stats;
    }

    /**
     * Get accumulated attendance hours for a user (default: all work sessions)
     *
     * @param int $userId
     * @param string|null $sessionType
     * @return array
     */
    public static function getUserTotalHours(int $userId, ?string $sessionType = 'work'): array
    {
        $totalSeconds = Attendance::where('user_id', $userId)
            ->when($sessionType, function ($query) use ($sessionType) {
                if ($sessionType === 'work') {
                    $query->whereIn('session_type', ['work', 'meeting']);
                } else {
                    $query->where('session_type', $sessionType);
                }
            })
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->sum('session_duration');

        $totalSeconds = (int) $totalSeconds;

        return [
            'total_seconds' => $totalSeconds,
            'formatted' => TimeHelper::formatDuration($totalSeconds),
            'hours_decimal' => TimeHelper::secondsToHours($totalSeconds),
        ];
    }

    /**
     * Get daily attendance summary
     *
     * @param int $userId
     * @param string|null $date
     * @return array
     */
    public static function getDailySummary(int $userId, ?string $date = null): array
    {
        $date = $date ?? today();

        $sessions = Attendance::getTodaySessions($userId);
        $activeSession = Attendance::getActiveSession($userId, $date);
        $totalHours = Attendance::getDailyTotalHours($userId, $date);

        return [
            'date' => $date,
            'sessions' => $sessions,
            'active_session' => $activeSession,
            'total_hours' => $totalHours,
            'total_hours_formatted' => TimeHelper::formatDuration($totalHours),
            'total_hours_decimal' => TimeHelper::secondsToHours($totalHours),
            'session_count' => $sessions->count(),
            'is_active' => $activeSession !== null
        ];
    }

    /**
     * Validate clock in/out data
     *
     * @param array $data
     * @return array
     */
    public static function validateClockData(array $data): array
    {
        $errors = [];

        if (empty($data['user_id'])) {
            $errors[] = 'User ID is required';
        }

        // Only validate clock_in if it's provided (for manual clock in/out)
        // For service-based clock in, clock_in is generated automatically
        if (isset($data['clock_in']) && empty($data['clock_in'])) {
            $errors[] = 'Clock in time is required';
        }

        if (!empty($data['clock_in'])) {
            try {
                $clockIn = Carbon::parse($data['clock_in']);
                if ($clockIn->isFuture()) {
                    $errors[] = 'Clock in time cannot be in the future';
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid clock in time format';
            }
        }

        if (!empty($data['clock_out'])) {
            try {
                $clockOut = Carbon::parse($data['clock_out']);
                if ($clockOut->isFuture()) {
                    $errors[] = 'Clock out time cannot be in the future';
                }

                if (!empty($data['clock_in'])) {
                    $clockIn = Carbon::parse($data['clock_in']);
                    if ($clockOut->lt($clockIn)) {
                        $errors[] = 'Clock out time must be after clock in time';
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid clock out time format';
            }
        }

        return $errors;
    }

    /**
     * Check for long duty sessions
     *
     * @param int $thresholdHours
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLongDutySessions(int $thresholdHours = 8)
    {
        $thresholdSeconds = TimeHelper::hoursToSeconds($thresholdHours);

        return Attendance::where('is_active', true)
            ->where('clock_in', '<=', now()->subSeconds($thresholdSeconds))
            ->with('user')
            ->get()
            ->map(function ($session) {
                $session->duration_seconds = $session->calculateTotalHours();
                $session->duration_formatted = TimeHelper::formatDuration($session->duration_seconds);
                $session->duration_hours = TimeHelper::secondsToHours($session->duration_seconds);
                return $session;
            });
    }

    /**
     * Get attendance statistics for period
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getPeriodStatistics(string $startDate, string $endDate): array
    {
        $totalSessions = Attendance::whereBetween('work_date', [$startDate, $endDate])
            ->whereIn('session_type', ['work', 'meeting'])
            ->count();

        $totalHours = Attendance::whereBetween('work_date', [$startDate, $endDate])
            ->whereIn('session_type', ['work', 'meeting'])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->sum('session_duration');

        $activeSessions = Attendance::whereBetween('work_date', [$startDate, $endDate])
            ->where('is_active', true)
            ->count();

        $uniqueUsers = Attendance::whereBetween('work_date', [$startDate, $endDate])
            ->whereIn('session_type', ['work', 'meeting'])
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total_sessions' => $totalSessions,
            'total_hours' => $totalHours,
            'total_hours_formatted' => TimeHelper::formatDuration($totalHours),
            'total_hours_decimal' => TimeHelper::secondsToHours($totalHours),
            'active_sessions' => $activeSessions,
            'unique_users' => $uniqueUsers,
            'avg_hours_per_session' => $totalSessions > 0 ? TimeHelper::secondsToHours($totalHours / $totalSessions) : 0
        ];
    }

    /**
     * Check if user can clock in
     *
     * @param int $userId
     * @return array
     */
    public static function canClockIn(int $userId): array
    {
        $anyActiveSession = Attendance::getAnyActiveSession($userId);

        if ($anyActiveSession) {
            return [
                'can_clock_in' => false,
                'reason' => 'active_session_exists',
                'active_session' => $anyActiveSession,
                'message' => sprintf(
                    'Anda masih memiliki sesi aktif yang belum di-clock out. Clock In: %s (Work Date: %s). Durasi saat ini: %s. Silakan clock out terlebih dahulu.',
                    $anyActiveSession->clock_in->format('d/m/Y H:i'),
                    $anyActiveSession->work_date->format('d/m/Y'),
                    $anyActiveSession->getFormattedDuration()
                )
            ];
        }

        return [
            'can_clock_in' => true,
            'reason' => null,
            'active_session' => null,
            'message' => null
        ];
    }

    /**
     * Check if user can clock out
     *
     * @param int $userId
     * @param string|null $date
     * @return array
     */
    public static function canClockOut(int $userId, ?string $date = null): array
    {
        $date = $date ?? today();

        $activeSession = Attendance::getActiveSession($userId, $date);

        if (!$activeSession) {
            $anyActiveSession = Attendance::getAnyActiveSession($userId);

            if ($anyActiveSession) {
                return [
                    'can_clock_out' => true,
                    'reason' => 'cross_day_session',
                    'active_session' => $anyActiveSession,
                    'message' => 'Menggunakan sesi aktif dari hari sebelumnya'
                ];
            }

            return [
                'can_clock_out' => false,
                'reason' => 'no_active_session',
                'active_session' => null,
                'message' => 'Anda belum melakukan clock in'
            ];
        }

        return [
            'can_clock_out' => true,
            'reason' => 'normal_session',
            'active_session' => $activeSession,
            'message' => null
        ];
    }
}
