<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
        'total_hours', // Duration in minutes for backward compatibility
        'work_date',
        'notes',
        'session_number',
        'session_type',
        'is_active',
        'session_duration', // Duration in seconds (new field)
        'scheduled_duty_minutes', // Scheduled duty duration in minutes
        'scheduled_end_time', // Scheduled end time (clock_in + scheduled_duty_minutes)
        'auto_checked_out' // Flag for auto checkout
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'work_date' => 'date',
        'is_active' => 'boolean',
        'session_duration' => 'integer',
        'scheduled_duty_minutes' => 'integer',
        'scheduled_end_time' => 'datetime',
        'auto_checked_out' => 'boolean'
    ];

    /**
     * Boot method to add model event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Validate data before saving
        static::saving(function ($attendance) {
            $attendance->validateAttendanceData();
        });

        // Cache Invalidation after save (create/update)
        static::saved(function ($attendance) {
            self::clearAttendanceCache($attendance);
        });

        // Cache Invalidation after delete
        static::deleted(function ($attendance) {
            self::clearAttendanceCache($attendance);
        });
    }

    /**
     * Clear relevant caches for attendance
     */
    protected static function clearAttendanceCache($attendance)
    {
        $userId = $attendance->user_id;
        $workDate = $attendance->work_date;
        $year = $workDate ? $workDate->year : now()->year;
        $yearWeek = $workDate ? $workDate->format('Y_W') : now()->format('Y_W');

        // 1. Clear Heatmap Cache
        \Illuminate\Support\Facades\Cache::forget("user_heatmap_{$userId}_{$year}");

        // 2. Clear Weekly Leaderboard Cache (Current Week)
        // Since we don't know the hospital context easily here without loading user relation,
        // we clear all potential hospital keys
        \Illuminate\Support\Facades\Cache::forget("weekly_stats_current_all_{$yearWeek}");
        \Illuminate\Support\Facades\Cache::forget("weekly_stats_current_alta_{$yearWeek}");
        \Illuminate\Support\Facades\Cache::forget("weekly_stats_current_roxwood_{$yearWeek}");

        // 3. Clear Historical Cache if the change is in the past
        // (Just to be safe, though history rarely changes)
        \Illuminate\Support\Facades\Cache::forget("weekly_stats_history_all");
        \Illuminate\Support\Facades\Cache::forget("weekly_stats_history_alta");
        \Illuminate\Support\Facades\Cache::forget("weekly_stats_history_roxwood");

    }

    /**
     * Validate attendance data before saving
     */
    public function validateAttendanceData()
    {
        // Validate clock_in and clock_out consistency
        if ($this->clock_in && $this->clock_out) {
            if ($this->clock_out->lt($this->clock_in)) {
                throw new \InvalidArgumentException(
                    'Clock out time cannot be before clock in time. ' .
                    'Clock in: ' . $this->clock_in->format('Y-m-d H:i:s') . ', ' .
                    'Clock out: ' . $this->clock_out->format('Y-m-d H:i:s')
                );
            }
        }

        // Validate work_date consistency with clock_in
        if ($this->clock_in && $this->work_date) {
            $clockInDate = $this->clock_in->toDateString();
            $workDate = $this->work_date->toDateString();

            // For cross-day sessions, work_date should match clock_in date
            if ($clockInDate !== $workDate) {
                \Log::warning('Work date mismatch detected', [
                    'attendance_id' => $this->id,
                    'clock_in_date' => $clockInDate,
                    'work_date' => $workDate,
                    'auto_fixing' => true
                ]);

                // Auto-fix: Set work_date to match clock_in date
                $this->work_date = $clockInDate;
            }
        }

        // Validate session duration if both clock_in and clock_out exist
        if ($this->clock_in && $this->clock_out && !$this->is_active) {
            $calculatedDuration = $this->clock_in->diffInSeconds($this->clock_out);

            // If session_duration is set, validate it matches calculated duration
            // EXCEPT for auto checkout with timer - use scheduled time, not calculated time
            // EXCEPT for manual checkout with timer - use elapsed time, not calculated time
            $isAutoCheckout = $this->auto_checked_out ?? false;
            $hasScheduledTimer = $this->scheduled_duty_minutes && $this->scheduled_duty_minutes > 0;

            // CRITICAL: Never override duration for auto checkout - it uses scheduled time exactly
            if ($isAutoCheckout && $hasScheduledTimer) {
                // For auto checkout with timer: session_duration MUST be scheduled_duty_minutes * 60
                $expectedScheduledSeconds = $this->scheduled_duty_minutes * 60;
                if ($this->session_duration != $expectedScheduledSeconds) {
                    \Log::warning('Auto checkout duration mismatch - fixing to scheduled time', [
                        'attendance_id' => $this->id,
                        'current_session_duration' => $this->session_duration,
                        'expected_scheduled_seconds' => $expectedScheduledSeconds,
                        'calculated_duration' => $calculatedDuration,
                        'scheduled_duty_minutes' => $this->scheduled_duty_minutes,
                        'auto_fixing' => true
                    ]);

                    // Force fix: Use scheduled time (exact), not calculated time
                    $this->session_duration = $expectedScheduledSeconds;
                    $this->total_hours = max(1, $this->scheduled_duty_minutes);
                }
                // Don't validate against calculated duration - it may differ due to delay
                return;
            }

            // Only validate if not auto checkout and not manual checkout with timer
            if ($this->session_duration && !$isAutoCheckout && !$hasScheduledTimer) {
                // For normal mode only: validate it matches calculated duration
                if (abs($this->session_duration - $calculatedDuration) > 1) {
                    \Log::warning('Session duration mismatch detected (normal mode)', [
                        'attendance_id' => $this->id,
                        'calculated_duration' => $calculatedDuration,
                        'stored_duration' => $this->session_duration,
                        'auto_fixing' => true
                    ]);

                    // Auto-fix: Update session_duration to match calculated duration (only for normal mode)
                    $this->session_duration = $calculatedDuration;
                }
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk sesi aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk sesi hari tertentu
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('work_date', $date);
    }

    /**
     * Scope untuk user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk sesi yang sudah selesai (clock out)
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('clock_out')->where('is_active', false);
    }

    /**
     * Scope untuk sesi yang valid (session_duration > 0)
     */
    public function scopeValid($query)
    {
        return $query->where('session_duration', '>', 0);
    }

    /**
     * Get active session for user today
     */
    public static function getActiveSession($userId, $date = null)
    {
        $date = $date ?? today();
        return self::forUser($userId)
            ->forDate($date)
            ->active()
            ->first();
    }

    /**
     * Get any active session for user (any date)
     */
    public static function getAnyActiveSession($userId)
    {
        return self::forUser($userId)
            ->active()
            ->first();
    }

    /**
     * Get daily sessions for user
     */
    public static function getDailySessions($userId, $date = null)
    {
        $date = $date ?? today();
        return self::forUser($userId)
            ->forDate($date)
            ->orderBy('session_number')
            ->get();
    }

    /**
     * Get next session number for user on date
     */
    public static function getNextSessionNumber($userId, $date = null)
    {
        $date = $date ?? today();
        $lastSession = self::forUser($userId)
            ->forDate($date)
            ->orderBy('session_number', 'desc')
            ->first();

        return $lastSession ? $lastSession->session_number + 1 : 1;
    }

    /**
     * Get daily total hours for user
     */
    public static function getDailyTotalHours($userId, $date = null)
    {
        $date = $date ?? today();
        return self::forUser($userId)
            ->forDate($date)
            ->valid()
            ->sum('session_duration');
    }

    /**
     * Get weekly leaderboard
     */
    public static function getWeeklyLeaderboard($limit = 10)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return self::with('user')
            ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
            ->valid()
            ->selectRaw('user_id, SUM(session_duration) as total_hours')
            ->groupBy('user_id')
            ->orderBy('total_hours', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Close session (clock out)
     */
    public function closeSession()
    {
        if (!$this->is_active) {
            \Log::warning('Attempting to close inactive session', [
                'attendance_id' => $this->id
            ]);
            return false;
        }

        // Use explicit timezone to ensure consistency
        $clockOutTime = Carbon::now('Asia/Jakarta');

        // Ensure clock_in is also Carbon with Asia/Jakarta timezone
        $clockInTime = $this->clock_in->setTimezone('Asia/Jakarta');

        // Validate: Clock out tidak boleh sebelum clock in
        if ($clockOutTime->lt($clockInTime)) {
            \Log::error('Clock out time is before clock in time', [
                'attendance_id' => $this->id,
                'clock_in' => $clockInTime->toDateTimeString(),
                'clock_out' => $clockOutTime->toDateTimeString(),
                'clock_in_tz' => $clockInTime->timezone->getName(),
                'clock_out_tz' => $clockOutTime->timezone->getName()
            ]);
            return false;
        }

        $this->clock_out = $clockOutTime;
        $this->is_active = false;

        // Check if this is manual checkout with scheduled duty timer
        $isManualCheckoutWithTimer = false;
        if ($this->scheduled_duty_minutes && $this->scheduled_duty_minutes > 0 && !$this->auto_checked_out) {
            $isManualCheckoutWithTimer = true;
        }

        // Calculate session duration in SECONDS (for accuracy) - use timezone-aware times
        $durationSeconds = $clockInTime->diffInSeconds($clockOutTime);
        $durationHours = $durationSeconds / 3600;
        $durationMinutes = floor($durationSeconds / 60);

        // Check if this is a cross-day session
        $isCrossDay = $clockInTime->toDateString() !== $clockOutTime->toDateString();

        // Prepare notes
        $additionalNotes = [];

        // If manual checkout with timer: use elapsed time (not scheduled time)
        // This matches Example 1: Clock in 60 min, manual checkout after 40 min = 40 min duty time
        if ($isManualCheckoutWithTimer) {
            // Manual checkout: gunakan waktu yang sudah berlalu (elapsed time)
            $this->session_duration = $durationSeconds;
            $this->total_hours = max(1, $durationMinutes);

            // Tambahkan catatan tentang manual checkout
            $additionalNotes[] = sprintf(
                '[Manual checkout - scheduled: %d min, actual: %d min]',
                $this->scheduled_duty_minutes,
                $durationMinutes
            );
        } else {
            // Normal mode atau auto checkout: gunakan calculated duration
            // Note: Auto checkout sudah set duration di autoCloseSession(), jadi ini hanya untuk normal mode
            $this->session_duration = $durationSeconds;
            $this->total_hours = max(1, $durationMinutes);
        }

        if ($isCrossDay) {
            $additionalNotes[] = sprintf(
                '[Cross-Day Session: %s → %s]',
                $clockInTime->format('Y-m-d H:i'),
                $clockOutTime->format('Y-m-d H:i')
            );

            \Log::info('Cross-day session detected', [
                'attendance_id' => $this->id,
                'clock_in' => $clockInTime->toDateTimeString(),
                'clock_out' => $clockOutTime->toDateTimeString(),
                'duration_hours' => round($durationHours, 2)
            ]);
        }

        // Validate: Durasi maksimum 48 jam (untuk detect anomali - lebih toleran untuk cross-day)
        if ($durationHours > 48) {
            $additionalNotes[] = sprintf(
                '[WARNING: Durasi %.1f jam melebihi 48 jam - Mohon verifikasi]',
                $durationHours
            );

            \Log::warning('Session duration exceeds 48 hours', [
                'attendance_id' => $this->id,
                'duration_hours' => $durationHours,
                'duration_seconds' => $durationSeconds,
                'clock_in' => $clockInTime->toDateTimeString(),
                'clock_out' => $clockOutTime->toDateTimeString(),
                'is_cross_day' => $isCrossDay
            ]);
        } elseif ($durationHours > 24 && !$isCrossDay) {
            // Warning khusus untuk durasi > 24 jam tapi BUKAN cross-day (kemungkinan lupa clock out)
            $additionalNotes[] = sprintf(
                '[INFO: Durasi %.1f jam dalam satu hari - Kemungkinan lupa clock out]',
                $durationHours
            );

            \Log::warning('Long session in single day (possible forgot to clock out)', [
                'attendance_id' => $this->id,
                'duration_hours' => $durationHours,
                'clock_in' => $clockInTime->toDateTimeString(),
                'clock_out' => $clockOutTime->toDateTimeString()
            ]);
        }

        // Append additional notes if any
        if (!empty($additionalNotes)) {
            $currentNotes = trim($this->notes ?? '');
            $newNotes = implode("\n", $additionalNotes);
            $this->notes = $currentNotes ? $currentNotes . "\n" . $newNotes : $newNotes;
        }

        // Duration sudah di-set di atas (baik untuk manual checkout dengan timer maupun normal mode)
        // Tidak perlu set ulang di sini untuk menghindari duplikasi

        $this->save();

        // Auto-split cross-boundary sessions after saving
        // Hanya split jika data valid (clock_out > clock_in dan tanggal berbeda dengan benar)
        if ($isCrossDay) {
            // Validasi tambahan sebelum split: pastikan clock_out > clock_in
            if ($clockOutTime->gt($clockInTime)) {
                \Log::info('Auto-splitting cross-day session', [
                    'attendance_id' => $this->id,
                    'user_id' => $this->user_id,
                    'clock_in' => $clockInTime->format('Y-m-d H:i:s'),
                    'clock_out' => $clockOutTime->format('Y-m-d H:i:s')
                ]);

                // Split cross-day session automatically
                $splitResult = $this->splitCrossDaySession();

                if (!$splitResult) {
                    \Log::warning('Auto-split cross-day session failed, but session was saved', [
                        'attendance_id' => $this->id,
                        'user_id' => $this->user_id
                    ]);
                }
            } else {
                \Log::error('Cannot auto-split: clock_out is not after clock_in', [
                    'attendance_id' => $this->id,
                    'user_id' => $this->user_id,
                    'clock_in' => $clockInTime->format('Y-m-d H:i:s'),
                    'clock_out' => $clockOutTime->format('Y-m-d H:i:s')
                ]);
            }
        }

        // Check for cross-week and split if needed
        if ($this->isCrossWeek()) {
            \Log::info('Auto-splitting cross-week session', [
                'attendance_id' => $this->id,
                'user_id' => $this->user_id
            ]);

            // Split cross-week session automatically
            $this->splitCrossWeekSession();
        }

        \Log::info('Session closed successfully', [
            'attendance_id' => $this->id,
            'user_id' => $this->user_id,
            'work_date' => $this->work_date,
            'duration_seconds' => $durationSeconds,
            'duration_hours' => round($durationHours, 2),
            'is_cross_day' => $isCrossDay
        ]);

        return true;
    }

    /**
     * Fix inconsistent data
     */
    public static function fixInconsistentData()
    {
        $fixed = 0;

        // Fix records where session_duration is null but clock_out exists
        $records = self::whereNotNull('clock_out')
            ->whereNull('session_duration')
            ->get();

        foreach ($records as $record) {
            $durationSeconds = $record->clock_in->diffInSeconds($record->clock_out);
            $record->session_duration = $durationSeconds;
            $record->total_hours = max(1, floor($durationSeconds / 60)); // Backward compatibility
            $record->save();
            $fixed++;
        }

        return $fixed;
    }

    /**
     * Calculate total hours/duration for current session
     * Returns duration in seconds for display
     */
    public function calculateTotalHours()
    {
        // Priority 1: session_duration (already in seconds)
        if ($this->session_duration) {
            return $this->session_duration;
        }

        // Priority 2: Jika masih aktif, hitung real-time dengan timezone yang benar
        if ($this->is_active && $this->clock_in) {
            $now = Carbon::now('Asia/Jakarta');
            return $this->clock_in->diffInSeconds($now);
        }

        // Priority 3: Jika ada clock_out, hitung dari clock_in ke clock_out
        if ($this->clock_out && $this->clock_in) {
            return $this->clock_in->diffInSeconds($this->clock_out);
        }

        // Priority 4: total_hours (legacy field, convert to seconds for backward compatibility)
        if ($this->total_hours) {
            return $this->total_hours * 60;
        }

        return 0;
    }

    /**
     * Get formatted duration (HH:MM:SS)
     */
    public function getFormattedDuration()
    {
        $seconds = $this->calculateTotalHours();
        return \App\Helpers\TimeHelper::formatDuration($seconds);
    }

    /**
     * Get duration in hours (decimal)
     */
    public function getDurationInHours()
    {
        $seconds = $this->calculateTotalHours();
        return \App\Helpers\TimeHelper::secondsToHours($seconds);
    }

    /**
     * Check if session crosses day boundary
     */
    public function isCrossDay()
    {
        if (!$this->clock_out) {
            return false;
        }

        return $this->clock_in->toDateString() !== $this->clock_out->toDateString();
    }

    /**
     * Get session info for cross-day sessions
     */
    public function getCrossDayInfo()
    {
        if (!$this->isCrossDay()) {
            return null;
        }

        // Validasi: pastikan clock_out > clock_in
        if ($this->clock_out->lte($this->clock_in)) {
            \Log::warning('Invalid cross-day session: clock_out <= clock_in', [
                'attendance_id' => $this->id,
                'clock_in' => $this->clock_in->format('Y-m-d H:i:s'),
                'clock_out' => $this->clock_out->format('Y-m-d H:i:s')
            ]);
            return null;
        }

        // Pastikan timezone konsisten
        $clockIn = $this->clock_in->copy()->setTimezone('Asia/Jakarta');
        $clockOut = $this->clock_out->copy()->setTimezone('Asia/Jakarta');

        // Pastikan second_day > first_day
        $firstDay = $clockIn->toDateString();
        $secondDay = $clockOut->toDateString();

        if ($secondDay <= $firstDay) {
            \Log::warning('Invalid cross-day session: second_day <= first_day', [
                'attendance_id' => $this->id,
                'first_day' => $firstDay,
                'second_day' => $secondDay,
                'clock_in' => $clockIn->format('Y-m-d H:i:s'),
                'clock_out' => $clockOut->format('Y-m-d H:i:s')
            ]);
            return null;
        }

        // Hitung end of day untuk hari pertama
        $firstDayEnd = $clockIn->copy()->endOfDay();

        // Hitung start of day untuk hari kedua
        $secondDayStart = $clockOut->copy()->startOfDay();

        // Hitung durasi dalam detik (lebih akurat)
        $firstDaySeconds = $clockIn->diffInSeconds($firstDayEnd);
        $secondDaySeconds = $secondDayStart->diffInSeconds($clockOut);

        // Validasi: durasi harus positif
        if ($firstDaySeconds <= 0 || $secondDaySeconds <= 0) {
            \Log::warning('Invalid cross-day session: invalid duration calculation', [
                'attendance_id' => $this->id,
                'first_day_seconds' => $firstDaySeconds,
                'second_day_seconds' => $secondDaySeconds
            ]);
            return null;
        }

        return [
            'is_cross_day' => true,
            'first_day' => $firstDay,
            'second_day' => $secondDay,
            'first_day_minutes' => floor($firstDaySeconds / 60),
            'second_day_minutes' => floor($secondDaySeconds / 60),
            'first_day_seconds' => $firstDaySeconds,
            'second_day_seconds' => $secondDaySeconds,
            'total_minutes' => $this->session_duration ? floor($this->session_duration / 60) : null
        ];
    }

    /**
     * Check if session crosses week boundary
     */
    public function isCrossWeek()
    {
        if (!$this->clock_out) {
            return false;
        }

        // Use copy() to avoid modifying the original Carbon instance
        $clockInWeek = $this->clock_in->copy()->startOfWeek();
        $clockOutWeek = $this->clock_out->copy()->startOfWeek();

        return $clockInWeek->ne($clockOutWeek);
    }

    /**
     * Get session info for cross-week sessions
     */
    public function getCrossWeekInfo()
    {
        if (!$this->isCrossWeek()) {
            return null;
        }

        // Use copy() to avoid modifying the original Carbon instance
        $clockInWeek = $this->clock_in->copy()->startOfWeek();
        $clockOutWeek = $this->clock_out->copy()->startOfWeek();

        // Calculate duration for each week
        $weekInfo = [];
        $currentWeek = $clockInWeek->copy();

        while ($currentWeek->lte($clockOutWeek)) {
            $weekStart = $currentWeek->copy();
            $weekEnd = $currentWeek->copy()->endOfWeek();

            // Determine actual start and end times for this week
            $actualStart = $this->clock_in->gt($weekStart) ? $this->clock_in : $weekStart;
            $actualEnd = $this->clock_out->lt($weekEnd) ? $this->clock_out : $weekEnd;

            $durationSeconds = $actualStart->diffInSeconds($actualEnd);

            $weekInfo[] = [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'actual_start' => $actualStart,
                'actual_end' => $actualEnd,
                'duration_seconds' => $durationSeconds,
                'duration_hours' => round($durationSeconds / 3600, 2)
            ];

            $currentWeek->addWeek();
        }

        return [
            'is_cross_week' => true,
            'weeks' => $weekInfo,
            'total_duration' => $this->session_duration
        ];
    }

    /**
     * Split cross-day session into daily sessions
     */
    public function splitCrossDaySession()
    {
        if (!$this->isCrossDay()) {
            return false;
        }

        // CRITICAL VALIDATION: Pastikan clock_out > clock_in sebelum split
        if ($this->clock_out->lte($this->clock_in)) {
            \Log::error('Cannot split cross-day session: clock_out must be after clock_in', [
                'attendance_id' => $this->id,
                'clock_in' => $this->clock_in->format('Y-m-d H:i:s'),
                'clock_out' => $this->clock_out->format('Y-m-d H:i:s')
            ]);
            return false;
        }

        $info = $this->getCrossDayInfo();
        if (!$info) {
            return false;
        }

        // Validasi tambahan: pastikan second_day > first_day
        if ($info['second_day'] <= $info['first_day']) {
            \Log::error('Cannot split cross-day session: second_day must be after first_day', [
                'attendance_id' => $this->id,
                'first_day' => $info['first_day'],
                'second_day' => $info['second_day'],
                'clock_in' => $this->clock_in->format('Y-m-d H:i:s'),
                'clock_out' => $this->clock_out->format('Y-m-d H:i:s')
            ]);
            return false;
        }

        try {
            \DB::beginTransaction();

            // Pastikan timezone konsisten
            $clockIn = $this->clock_in->copy()->setTimezone('Asia/Jakarta');
            $clockOut = $this->clock_out->copy()->setTimezone('Asia/Jakarta');

            // Update original record untuk hari pertama (sampai end of day)
            // endOfDay() akan menghasilkan 23:59:59 pada tanggal yang sama dengan clock_in
            $firstDayEnd = $clockIn->copy()->endOfDay();

            // Validasi: firstDayEnd harus <= clockOut
            if ($firstDayEnd->gt($clockOut)) {
                \Log::error('Cannot split: first day end is after clock out', [
                    'attendance_id' => $this->id,
                    'clock_in' => $clockIn->format('Y-m-d H:i:s'),
                    'clock_out' => $clockOut->format('Y-m-d H:i:s'),
                    'first_day_end' => $firstDayEnd->format('Y-m-d H:i:s')
                ]);
                \DB::rollBack();
                return false;
            }

            // Hitung durasi hari pertama (dari clock_in sampai end of day)
            $firstDayDuration = $clockIn->diffInSeconds($firstDayEnd);

            $this->update([
                'clock_out' => $firstDayEnd,
                'session_duration' => $firstDayDuration,
                'total_hours' => max(1, floor($firstDayDuration / 60)), // Keep minutes for compatibility
                'notes' => ($this->notes ?? '') . "\n[Split: Day 1 of cross-day session]"
            ]);

            // Buat record baru untuk hari kedua (dari start of day kedua sampai clock out asli)
            // startOfDay() akan menghasilkan 00:00:00 pada tanggal clock_out
            $secondDayStart = $clockOut->copy()->startOfDay();

            // Validasi: secondDayStart harus < clockOut
            if ($secondDayStart->gte($clockOut)) {
                \Log::error('Cannot split: second day start is not before clock out', [
                    'attendance_id' => $this->id,
                    'clock_out' => $clockOut->format('Y-m-d H:i:s'),
                    'second_day_start' => $secondDayStart->format('Y-m-d H:i:s')
                ]);
                \DB::rollBack();
                return false;
            }

            // Hitung durasi hari kedua (dari start of day sampai clock_out)
            $secondDayDuration = $secondDayStart->diffInSeconds($clockOut);

            // Validasi: durasi harus positif
            if ($secondDayDuration <= 0) {
                \Log::error('Cannot split: second day duration is invalid', [
                    'attendance_id' => $this->id,
                    'second_day_duration' => $secondDayDuration,
                    'second_day_start' => $secondDayStart->format('Y-m-d H:i:s'),
                    'clock_out' => $clockOut->format('Y-m-d H:i:s')
                ]);
                \DB::rollBack();
                return false;
            }

            $nextSessionNumber = self::getNextSessionNumber($this->user_id, $info['second_day']);

            self::create([
                'user_id' => $this->user_id,
                'clock_in' => $secondDayStart,
                'clock_out' => $clockOut,
                'work_date' => $info['second_day'],
                'session_number' => $nextSessionNumber,
                'session_type' => $this->session_type,
                'is_active' => false,
                'session_duration' => $secondDayDuration,
                'total_hours' => max(1, floor($secondDayDuration / 60)), // Keep minutes for compatibility
                'notes' => ($this->notes ?? '') . "\n[Split: Day 2 of cross-day session]"
            ]);

            \DB::commit();

            \Log::info('Cross-day session split successfully', [
                'attendance_id' => $this->id,
                'first_day' => $info['first_day'],
                'second_day' => $info['second_day'],
                'first_day_duration' => $firstDayDuration,
                'second_day_duration' => $secondDayDuration
            ]);

            return true;

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to split cross-day session', [
                'attendance_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Split cross-week session into weekly sessions
     */
    public function splitCrossWeekSession()
    {
        if (!$this->isCrossWeek()) {
            return false;
        }

        $info = $this->getCrossWeekInfo();
        if (!$info) {
            return false;
        }

        try {
            \DB::beginTransaction();

            $createdSessions = [];

            foreach ($info['weeks'] as $index => $week) {
                if ($index === 0) {
                    // Update original record for first week
                    $this->update([
                        'clock_out' => $week['actual_end'],
                        'session_duration' => $week['duration_seconds'],
                        'total_hours' => floor($week['duration_seconds'] / 60), // Convert to minutes for compatibility
                        'notes' => ($this->notes ?? '') . "\n[Split: Week 1 of cross-week session]"
                    ]);
                } else {
                    // Create new record for subsequent weeks
                    $sessionNumber = self::getNextSessionNumber($this->user_id, $week['actual_start']->toDateString());

                    $newSession = self::create([
                        'user_id' => $this->user_id,
                        'clock_in' => $week['actual_start'],
                        'clock_out' => $week['actual_end'],
                        'work_date' => $week['actual_start']->toDateString(),
                        'session_number' => $sessionNumber,
                        'session_type' => $this->session_type,
                        'is_active' => false,
                        'session_duration' => $week['duration_seconds'],
                        'total_hours' => floor($week['duration_seconds'] / 60), // Convert to minutes for compatibility
                        'notes' => ($this->notes ?? '') . "\n[Split: Week " . ($index + 1) . " of cross-week session]"
                    ]);

                    $createdSessions[] = $newSession;
                }
            }

            \DB::commit();
            return $createdSessions;

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to split cross-week session', [
                'attendance_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Fix cross-day sessions (split into separate daily sessions)
     */
    public static function fixCrossDaySessions()
    {
        $fixed = 0;
        $skipped = 0;

        // Find sessions that cross days - using safe date comparison
        $records = self::whereNotNull('clock_out')
            ->whereRaw('DATE(clock_in) != DATE(clock_out)')
            ->where('is_active', false)
            ->get();

        foreach ($records as $record) {
            // Validasi: pastikan clock_out > clock_in sebelum mencoba split
            if ($record->clock_out->lte($record->clock_in)) {
                $skipped++;
                \Log::warning('Skipping invalid cross-day session in fixCrossDaySessions', [
                    'attendance_id' => $record->id,
                    'user_id' => $record->user_id,
                    'clock_in' => $record->clock_in->format('Y-m-d H:i:s'),
                    'clock_out' => $record->clock_out->format('Y-m-d H:i:s')
                ]);
                continue;
            }

            if ($record->splitCrossDaySession()) {
                $fixed++;
                \Log::info('Cross-day session split successfully', [
                    'attendance_id' => $record->id,
                    'user_id' => $record->user_id
                ]);
            }
        }

        if ($skipped > 0) {
            \Log::info('Cross-day sessions fix completed', [
                'fixed' => $fixed,
                'skipped_invalid' => $skipped,
                'total_processed' => $records->count()
            ]);
        }

        return $fixed;
    }

    /**
     * Fix cross-week sessions (split into separate weekly sessions)
     */
    public static function fixCrossWeekSessions()
    {
        $fixed = 0;

        // Find sessions that cross weeks
        $records = self::whereNotNull('clock_out')
            ->where('is_active', false)
            ->get()
            ->filter(function ($record) {
                return $record->isCrossWeek();
            });

        foreach ($records as $record) {
            if ($record->splitCrossWeekSession()) {
                $fixed++;
                \Log::info('Cross-week session split successfully', [
                    'attendance_id' => $record->id,
                    'user_id' => $record->user_id
                ]);
            }
        }

        return $fixed;
    }

    /**
     * Fix all cross-boundary sessions (both day and week)
     */
    public static function fixAllCrossBoundarySessions()
    {
        $dayFixed = self::fixCrossDaySessions();
        $weekFixed = self::fixCrossWeekSessions();

        \Log::info('Cross-boundary sessions fixed', [
            'cross_day_fixed' => $dayFixed,
            'cross_week_fixed' => $weekFixed
        ]);

        return [
            'cross_day_fixed' => $dayFixed,
            'cross_week_fixed' => $weekFixed,
            'total_fixed' => $dayFixed + $weekFixed
        ];
    }

    /**
     * Get sessions for today (only sessions that actually started today)
     */
    public static function getTodaySessions($userId)
    {
        $today = today();
        return self::where('user_id', $userId)
            ->where('work_date', $today)
            ->whereDate('clock_in', $today) // Additional check: ensure clock_in is also today
            ->orderBy('session_number')
            ->get();
    }

    /**
     * Get active session for today
     */
    public static function getTodayActiveSession($userId)
    {
        $today = today();
        return self::where('user_id', $userId)
            ->where('work_date', $today)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get remaining time in seconds for scheduled duty timer
     * Returns null if no timer is set
     *
     * @return int|null Remaining time in seconds, or null if no timer
     */
    public function getRemainingTime()
    {
        if (!$this->scheduled_duty_minutes || !$this->is_active) {
            return null; // Tidak ada timer atau session sudah tidak aktif
        }

        if (!$this->scheduled_end_time) {
            return null; // scheduled_end_time belum di-set
        }

        $now = Carbon::now('Asia/Jakarta');
        $endTime = $this->scheduled_end_time->setTimezone('Asia/Jakarta');

        if ($now >= $endTime) {
            return 0; // Waktu sudah habis
        }

        return $now->diffInSeconds($endTime); // Return dalam detik
    }

    /**
     * Auto close session when scheduled time expires
     * Uses scheduled_duty_minutes as the session duration
     *
     * @return bool
     */
    public function autoCloseSession()
    {
        if (!$this->is_active || !$this->scheduled_duty_minutes) {
            return false;
        }

        if (!$this->scheduled_end_time) {
            \Log::warning('Cannot auto close: scheduled_end_time is not set', [
                'attendance_id' => $this->id
            ]);
            return false;
        }

        // Reload to ensure we have latest data
        $this->refresh();

        // Double check session is still active after refresh
        if (!$this->is_active || !$this->scheduled_duty_minutes) {
            \Log::warning('Session is no longer active or scheduled_duty_minutes is missing after refresh', [
                'attendance_id' => $this->id,
                'is_active' => $this->is_active,
                'scheduled_duty_minutes' => $this->scheduled_duty_minutes
            ]);
            return false;
        }

        // Ensure scheduled_end_time is Carbon instance with correct timezone
        $clockOutTime = \Carbon\Carbon::parse($this->scheduled_end_time)->setTimezone('Asia/Jakarta');
        $clockInTime = $this->clock_in->copy()->setTimezone('Asia/Jakarta');

        // Set clock out time to scheduled_end_time (exact time, not now())
        $this->clock_out = $clockOutTime;
        $this->is_active = false;
        $this->auto_checked_out = true;

        // IMPORTANT: Gunakan scheduled_duty_minutes sebagai durasi (sesuai contoh 2)
        // Jangan gunakan calculated duration dari clock_in ke clock_out
        // karena mungkin ada delay dalam proses auto checkout
        $scheduledSeconds = $this->scheduled_duty_minutes * 60;
        $this->session_duration = $scheduledSeconds;
        $this->total_hours = max(1, $this->scheduled_duty_minutes);

        // Tambahkan catatan
        $currentNotes = trim($this->notes ?? '');
        $autoNote = sprintf(
            "\n[Auto checkout - scheduled duty time: %d min (exact)]",
            $this->scheduled_duty_minutes
        );
        $this->notes = $currentNotes ? $currentNotes . $autoNote : trim($autoNote);

        $this->save();

        // Log dengan informasi lengkap untuk debugging
        $actualDurationFromTimes = $clockInTime->diffInSeconds($clockOutTime);

        \Log::info('Auto checkout completed', [
            'attendance_id' => $this->id,
            'user_id' => $this->user_id,
            'scheduled_minutes' => $this->scheduled_duty_minutes,
            'scheduled_seconds' => $scheduledSeconds,
            'session_duration_set' => $this->session_duration,
            'clock_in' => $clockInTime->toDateTimeString(),
            'scheduled_end_time' => $clockOutTime->toDateTimeString(),
            'clock_out' => $this->clock_out ? $this->clock_out->toDateTimeString() : null,
            'actual_duration_from_times' => $actualDurationFromTimes,
            'now' => \Carbon\Carbon::now('Asia/Jakarta')->toDateTimeString()
        ]);

        return true;
    }
}
