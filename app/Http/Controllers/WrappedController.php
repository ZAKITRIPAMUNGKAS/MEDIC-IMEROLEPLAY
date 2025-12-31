<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\MedicalForm;
use App\Models\Payroll;
use App\Models\UserWrappedView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WrappedController extends Controller
{
    /**
     * Show the wrapped statistics for the authenticated user
     */
    public function show(Request $request, $year = null)
    {
        $user = auth()->user();
        $year = $year ?? now()->year;

        // Cache wrapped statistics for 24 hours
        $stats = Cache::remember("wrapped_{$user->id}_{$year}", 86400, function () use ($user, $year) {
            return $this->calculateStatistics($user, $year);
        });

        // Get a random inspirational quote (not cached, changes each view)
        $quote = $this->getRandomQuote();

        return view('wrapped.index', [
            'stats' => $stats,
            'year' => $year,
            'user' => $user,
            'quote' => $quote,
        ]);
    }

    /**
     * Calculate all statistics for the user
     */
    private function calculateStatistics($user, $year)
    {
        // 1. Total Working Hours (in hours, decimal)
        $totalSeconds = Attendance::where('user_id', $user->id)
            ->whereYear('work_date', $year)
            ->valid()
            ->sum('session_duration');

        $totalHours = round($totalSeconds / 3600, 1);

        // 2. Total Patients Processed
        $totalPatients = MedicalForm::where('processed_by', $user->id)
            ->whereYear('processed_at', $year)
            ->whereNotNull('processed_at')
            ->count();

        // 3. Total Salary Earned
        $totalSalary = Payroll::where('user_id', $user->id)
            ->whereYear('period_start', $year)
            ->where('status', 'paid')
            ->sum('calculated_salary');

        // 4. Busiest Month
        $busiestMonthData = Attendance::where('user_id', $user->id)
            ->whereYear('work_date', $year)
            ->valid()
            ->selectRaw('MONTH(work_date) as month, SUM(session_duration) as total')
            ->groupBy('month')
            ->orderByDesc('total')
            ->first();

        $busiestMonth = $busiestMonthData ? [
            'month' => $busiestMonthData->month,
            'name' => Carbon::create()->month($busiestMonthData->month)->translatedFormat('F'),
            'hours' => round($busiestMonthData->total / 3600, 1)
        ] : null;

        // 5. Most Active Day of Week
        $mostActiveDayData = Attendance::where('user_id', $user->id)
            ->whereYear('work_date', $year)
            ->valid()
            ->selectRaw('DAYOFWEEK(work_date) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->orderByDesc('count')
            ->first();

        $mostActiveDay = $mostActiveDayData ? [
            'day' => $mostActiveDayData->day_of_week,
            'name' => $this->getDayName($mostActiveDayData->day_of_week),
            'count' => $mostActiveDayData->count
        ] : null;

        // 6. Most Active Time (Hour of day)
        $mostActiveHourData = Attendance::where('user_id', $user->id)
            ->whereYear('work_date', $year)
            ->valid()
            ->selectRaw('HOUR(clock_in) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->first();

        $mostActiveHour = $mostActiveHourData ? [
            'hour' => $mostActiveHourData->hour,
            'formatted' => sprintf('%02d:00', $mostActiveHourData->hour),
            'count' => $mostActiveHourData->count
        ] : null;

        // 7. Total Sessions/Days Worked
        $totalSessions = Attendance::where('user_id', $user->id)
            ->whereYear('work_date', $year)
            ->valid()
            ->count();

        $totalDaysWorked = Attendance::where('user_id', $user->id)
            ->whereYear('work_date', $year)
            ->valid()
            ->distinct('work_date')
            ->count('work_date');

        // 8. Average Hours Per Day
        $averageHoursPerDay = $totalDaysWorked > 0 ? round($totalHours / $totalDaysWorked, 1) : 0;

        // 9. Calculate Badge/Title
        $badge = $this->calculateBadge($totalHours, $totalPatients, $mostActiveDay, $mostActiveHour);

        // 10. Most Common Form Type (if applicable)
        $mostCommonFormType = MedicalForm::where('processed_by', $user->id)
            ->whereYear('processed_at', $year)
            ->whereNotNull('processed_at')
            ->selectRaw('form_type, COUNT(*) as count')
            ->groupBy('form_type')
            ->orderByDesc('count')
            ->first();

        return [
            'total_hours' => $totalHours,
            'total_patients' => $totalPatients,
            'total_salary' => $totalSalary,
            'busiest_month' => $busiestMonth,
            'most_active_day' => $mostActiveDay,
            'most_active_hour' => $mostActiveHour,
            'total_sessions' => $totalSessions,
            'total_days_worked' => $totalDaysWorked,
            'average_hours_per_day' => $averageHoursPerDay,
            'badge' => $badge,
            'most_common_form_type' => $mostCommonFormType ? [
                'type' => $mostCommonFormType->form_type,
                'count' => $mostCommonFormType->count,
                'formatted' => ucwords(str_replace('_', ' ', $mostCommonFormType->form_type))
            ] : null,
        ];
    }

    /**
     * Calculate user badge/title based on achievements
     */
    private function calculateBadge($totalHours, $totalPatients, $mostActiveDay, $mostActiveHour)
    {
        $badges = [];

        // Badge based on working hours
        if ($totalHours >= 1000) {
            $badges[] = [
                'title' => 'The Iron Doctor',
                'description' => 'Lebih dari 1000 jam dedikasi!',
                'icon' => '🏆',
                'color' => 'from-yellow-500 to-orange-500'
            ];
        } elseif ($totalHours >= 500) {
            $badges[] = [
                'title' => 'The Night Owl',
                'description' => '500+ jam kerja keras!',
                'icon' => '🦉',
                'color' => 'from-indigo-500 to-purple-500'
            ];
        } elseif ($totalHours >= 100) {
            $badges[] = [
                'title' => 'The Dedicated',
                'description' => 'Komitmen yang luar biasa!',
                'icon' => '💪',
                'color' => 'from-blue-500 to-cyan-500'
            ];
        } else {
            $badges[] = [
                'title' => 'The Newcomer',
                'description' => 'Awal yang baik!',
                'icon' => '🌟',
                'color' => 'from-green-500 to-teal-500'
            ];
        }

        // Badge based on patients
        if ($totalPatients >= 500) {
            $badges[] = [
                'title' => 'Life Saver',
                'description' => '500+ pasien terbantu!',
                'icon' => '❤️',
                'color' => 'from-red-500 to-pink-500'
            ];
        } elseif ($totalPatients >= 200) {
            $badges[] = [
                'title' => 'Medical Hero',
                'description' => 'Pahlawan medis sejati!',
                'icon' => '🦸',
                'color' => 'from-purple-500 to-pink-500'
            ];
        } elseif ($totalPatients >= 50) {
            $badges[] = [
                'title' => 'The Healer',
                'description' => 'Tangan penyembuh!',
                'icon' => '✨',
                'color' => 'from-cyan-500 to-blue-500'
            ];
        }

        // Special badges based on behavior
        if ($mostActiveDay && $mostActiveDay['day'] == 2) { // Monday
            $badges[] = [
                'title' => 'Monday Warrior',
                'description' => 'Penakluk Senin!',
                'icon' => '⚔️',
                'color' => 'from-gray-700 to-gray-900'
            ];
        }

        if ($mostActiveDay && ($mostActiveDay['day'] == 1 || $mostActiveDay['day'] == 7)) { // Weekend
            $badges[] = [
                'title' => 'Weekend Warrior',
                'description' => 'Dedikasi tanpa henti!',
                'icon' => '🎯',
                'color' => 'from-orange-500 to-red-500'
            ];
        }

        if ($mostActiveHour && $mostActiveHour['hour'] >= 2 && $mostActiveHour['hour'] <= 6) {
            $badges[] = [
                'title' => 'The Night Shift',
                'description' => 'Penjaga malam!',
                'icon' => '🌙',
                'color' => 'from-slate-700 to-indigo-900'
            ];
        }

        if ($mostActiveHour && $mostActiveHour['hour'] >= 5 && $mostActiveHour['hour'] <= 7) {
            $badges[] = [
                'title' => 'Early Bird',
                'description' => 'Pagi adalah kekuatanmu!',
                'icon' => '🌅',
                'color' => 'from-amber-500 to-orange-500'
            ];
        }

        // Return primary badge (first/most prestigious) and all badges
        return [
            'primary' => $badges[0] ?? [
                'title' => 'The Medical Professional',
                'description' => 'Selalu siap melayani!',
                'icon' => '👨‍⚕️',
                'color' => 'from-blue-500 to-indigo-500'
            ],
            'all' => $badges
        ];
    }

    /**
     * Get a random inspirational quote for the outro slide
     */
    private function getRandomQuote()
    {
        $quotes = [
            [
                'text' => 'Datang shift dengan doa, pulang dengan cerita. 📖',
                'author' => null
            ],
            [
                'text' => 'Nggak semua hari happy, tapi kamu tetap hadir. That matters. 🤍',
                'author' => null
            ],
            [
                'text' => 'Kamu itu bukti kalau kebaikan masih jalan meski dunia ribet. 🌍',
                'author' => null
            ],
            [
                'text' => 'Tahun ini kamu bukan cuma kerja, tapi literally ngasih harapan. Big W! 🏆',
                'author' => null
            ],
            [
                'text' => 'Terima kasih sudah jadi manusia baik di dunia yang capek. 🌱',
                'author' => null
            ],
            [
                'text' => 'Tahun ini kamu literally ngebantu banyak orang. That\'s a flex, bestie! 💪',
                'author' => null
            ],
            [
                'text' => 'POV: Kamu adalah alasan kenapa orang masih percaya sama tenaga medis. No cap! 🔥',
                'author' => null
            ],
            [
                'text' => 'Siapa bilang pahlawan cuma ada di film Marvel? Kamu literally one of them! 🦸',
                'author' => null
            ],
            [
                'text' => 'Main character energy banget sih tahun ini. Keep slaying! ✨',
                'author' => null
            ],
            [
                'text' => 'Kalau burnout punya trophy, kamu juara bertahan. But you still showed up. Respect! 👑',
                'author' => null
            ],
            [
                'text' => 'They see you healing, they vibing. Kamu emang built different! 💯',
                'author' => null
            ],
            [
                'text' => 'Dedikasi kamu tahun ini? Chef\'s kiss! Pasien happy, kamu legend! 😘👌',
                'author' => null
            ],
            [
                'text' => 'Bukan sekedar kerja, tapi literally save lives. That hits different, fr fr! 💖',
                'author' => null
            ],
            [
                'text' => 'Slay the day, save the lives. Kamu emang go hard or go home! 🚀',
                'author' => null
            ],
        ];

        return $quotes[array_rand($quotes)];
    }

    /**
     * Get day name from MySQL DAYOFWEEK value (1=Sunday, 2=Monday, etc.)
     */
    private function getDayName($dayOfWeek)
    {
        $days = [
            1 => 'Minggu',
            2 => 'Senin',
            3 => 'Selasa',
            4 => 'Rabu',
            5 => 'Kamis',
            6 => 'Jumat',
            7 => 'Sabtu'
        ];

        return $days[$dayOfWeek] ?? 'Unknown';
    }

    /**
     * Dismiss the wrapped modal (mark as viewed via session)
     */
    public function dismiss(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Mark in session
        session(['wrapped_dismissed' => true]);
        session(["wrapped_{$year}_viewed" => true]);

        // Also record to database for analytics
        UserWrappedView::recordView(auth()->id(), $year);

        return response()->json([
            'success' => true,
            'message' => 'Wrapped dismissed successfully'
        ]);
    }

    /**
     * Record wrapped view (for analytics)
     */
    public function recordView(Request $request)
    {
        $year = $request->input('year', now()->year);

        UserWrappedView::recordView(auth()->id(), $year);

        return response()->json([
            'success' => true,
            'message' => 'View recorded'
        ]);
    }
}
