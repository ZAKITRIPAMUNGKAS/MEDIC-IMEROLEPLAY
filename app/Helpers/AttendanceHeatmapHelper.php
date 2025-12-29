<?php

namespace App\Helpers;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceHeatmapHelper
{
    /**
     * Generate attendance heatmap data for a user
     *
     * @param int $userId
     * @param int $year
     * @return array
     */
    public static function generateHeatmapData($userId, $year = null)
    {
        $year = $year ?: now()->year;
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);
        
        // Get all attendance records for the year
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->whereNotNull('clock_in')
            ->get()
            ->groupBy(function ($attendance) {
                return $attendance->work_date->format('Y-m-d');
            });
        
        
        // Calculate daily work hours
        $dailyData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $dayAttendances = $attendances->get($dateKey, collect());
            
            // Calculate total hours from session_duration or total_hours
            $totalHours = 0;
            foreach ($dayAttendances as $attendance) {
                if ($attendance->session_duration && $attendance->session_duration > 0) {
                    $totalHours += $attendance->session_duration;
                } elseif ($attendance->total_hours && $attendance->total_hours > 0) {
                    $totalHours += $attendance->total_hours;
                } elseif ($attendance->clock_out) {
                    // Calculate from clock_in and clock_out if session_duration is not set
                    try {
                        $clockIn = Carbon::parse($attendance->clock_in);
                        $clockOut = Carbon::parse($attendance->clock_out);
                        $totalHours += $clockOut->diffInSeconds($clockIn);
                    } catch (\Exception $e) {
                        // Skip if date parsing fails
                        continue;
                    }
                } else {
                    // If only clock_in exists (no clock_out), count as 1 hour minimum
                    $totalHours += 3600; // 1 hour
                }
            }
            
            $sessionCount = $dayAttendances->count();
            
            $dailyData[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('d'),
                'month' => $currentDate->format('M'),
                'weekday' => $currentDate->format('D'),
                'total_hours' => $totalHours,
                'session_count' => $sessionCount,
                'level' => self::getContributionLevel($totalHours),
                'is_today' => $currentDate->isToday(),
                'is_weekend' => $currentDate->isWeekend(),
            ];
            
            $currentDate->addDay();
        }
        
        // Calculate streaks
        $streaks = self::calculateStreaks($dailyData);
        
        return [
            'year' => $year,
            'total_days' => count($dailyData),
            'work_days' => collect($dailyData)->where('total_hours', '>', 0)->count(),
            'total_hours' => collect($dailyData)->sum('total_hours'),
            'daily_data' => $dailyData,
            'weeks_data' => self::organizeDataByWeeks($dailyData, $year),
            'months' => self::getMonthLabels($year),
            'weekdays' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'streaks' => $streaks,
            'current_streak' => $streaks['current'],
            'longest_streak' => $streaks['longest'],
        ];
    }
    
    /**
     * Get contribution level based on work hours
     *
     * @param int $totalHours (in seconds)
     * @return int
     */
    private static function getContributionLevel($totalHours)
    {
        $hours = $totalHours / 3600; // Convert to hours
        
        if ($hours == 0) return 0;
        if ($hours < 2) return 1;
        if ($hours < 4) return 2;
        if ($hours < 6) return 3;
        if ($hours < 8) return 4;
        return 5;
    }
    
    /**
     * Get month labels for the year
     *
     * @param int $year
     * @return array
     */
    private static function getMonthLabels($year)
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = Carbon::create($year, $i, 1);
            $endDate = $date->copy()->endOfMonth();
            $weeksInMonth = $date->diffInWeeks($endDate) + 1;
            
            $months[] = [
                'name' => $date->format('M'),
                'start_day' => $date->dayOfYear,
                'days_in_month' => $date->daysInMonth,
                'week_count' => $weeksInMonth,
            ];
        }
        return $months;
    }
    
    /**
     * Get heatmap statistics
     *
     * @param array $heatmapData
     * @return array
     */
    public static function getStatistics($heatmapData)
    {
        $dailyData = $heatmapData['daily_data'];
        $workDays = collect($dailyData)->where('total_hours', '>', 0);
        
        return [
            'total_contributions' => $workDays->count(),
            'total_hours' => $workDays->sum('total_hours'),
            'average_hours_per_day' => $workDays->avg('total_hours'),
            'longest_streak' => self::getLongestStreak($dailyData),
            'current_streak' => self::getCurrentStreak($dailyData),
            'most_active_month' => self::getMostActiveMonth($dailyData),
        ];
    }
    
    /**
     * Get longest work streak
     *
     * @param array $dailyData
     * @return int
     */
    private static function getLongestStreak($dailyData)
    {
        $maxStreak = 0;
        $currentStreak = 0;
        
        foreach ($dailyData as $day) {
            if ($day['total_hours'] > 0) {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }
        
        return $maxStreak;
    }
    
    /**
     * Get current work streak
     *
     * @param array $dailyData
     * @return int
     */
    private static function getCurrentStreak($dailyData)
    {
        $streak = 0;
        $reversedData = array_reverse($dailyData);
        
        foreach ($reversedData as $day) {
            if ($day['total_hours'] > 0) {
                $streak++;
            } else {
                break;
            }
        }
        
        return $streak;
    }
    
    /**
     * Get most active month
     *
     * @param array $dailyData
     * @return string
     */
    private static function getMostActiveMonth($dailyData)
    {
        $monthlyHours = [];
        
        foreach ($dailyData as $day) {
            $month = Carbon::parse($day['date'])->format('M');
            $monthlyHours[$month] = ($monthlyHours[$month] ?? 0) + $day['total_hours'];
        }
        
        return array_search(max($monthlyHours), $monthlyHours) ?: 'None';
    }
    
    /**
     * Organize daily data by weeks for proper heatmap display
     *
     * @param array $dailyData
     * @param int $year
     * @return array
     */
    private static function organizeDataByWeeks($dailyData, $year)
    {
        $weeks = [];
        
        // Create a map of dates to data
        $dateMap = [];
        foreach ($dailyData as $day) {
            $dateMap[$day['date']] = $day;
        }
        
        // Start from January 1st of the year
        $startOfYear = Carbon::create($year, 1, 1);
        
        // Find the Sunday of the week that contains January 1st
        $currentWeek = $startOfYear->copy();
        while ($currentWeek->dayOfWeek != 0) { // 0 = Sunday
            $currentWeek->subDay();
        }
        
        // Generate all weeks that contain days from the year
        $weekNumber = 1;
        $maxWeeks = 53; // Maximum weeks in a year
        
        while ($weekNumber <= $maxWeeks) {
            $weekDays = [];
            
            // Add 7 days for this week
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentWeek->format('Y-m-d');
                $weekDays[] = $dateMap[$dateKey] ?? null;
                $currentWeek->addDay();
            }
            
            $weeks[$weekNumber] = $weekDays;
            $weekNumber++;
            
            // Break if we've gone past the year
            if ($currentWeek->year > $year) {
                break;
            }
        }
        
        return $weeks;
    }
    
    /**
     * Calculate work streaks
     *
     * @param array $dailyData
     * @return array
     */
    private static function calculateStreaks($dailyData)
    {
        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 0;
        
        // Calculate current streak (from today backwards)
        $reversedData = array_reverse($dailyData);
        foreach ($reversedData as $day) {
            if ($day['total_hours'] > 0) {
                $currentStreak++;
            } else {
                break;
            }
        }
        
        // Calculate longest streak
        foreach ($dailyData as $day) {
            if ($day['total_hours'] > 0) {
                $tempStreak++;
                $longestStreak = max($longestStreak, $tempStreak);
            } else {
                $tempStreak = 0;
            }
        }
        
        return [
            'current' => $currentStreak,
            'longest' => $longestStreak,
        ];
    }
    
}
