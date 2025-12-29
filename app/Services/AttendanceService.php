<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use App\Helpers\AttendanceHelper;
use App\Helpers\TimeHelper;
use App\Constants\AttendanceConstants;
use App\Exceptions\AttendanceException;
use App\Jobs\SendDiscordWebhookJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Process clock in for user
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function processClockIn(User $user, array $data): array
    {
        // Validate input
        $validation = AttendanceHelper::validateClockData(array_merge($data, ['user_id' => $user->id]));
        if (!empty($validation)) {
            throw AttendanceException::validationFailed($validation, ['user_id' => $user->id]);
        }

        // Check if user can clock in
        $canClockIn = AttendanceHelper::canClockIn($user->id);
        if (!$canClockIn['can_clock_in']) {
            throw AttendanceException::duplicateClockIn([
                'user_id' => $user->id,
                'active_session' => $canClockIn['active_session']
            ]);
        }

        DB::beginTransaction();
        try {
            $currentTime = Carbon::now(AttendanceConstants::DEFAULT_TIMEZONE);
            $currentDate = Carbon::today(AttendanceConstants::DEFAULT_TIMEZONE);
            $sessionNumber = Attendance::getNextSessionNumber($user->id, $currentDate);
            $sessionType = $data['session_type'] ?? 'work';

            // Prepare attendance data
            $attendanceData = [
                'user_id' => $user->id,
                'clock_in' => $currentTime,
                'work_date' => $currentDate,
                'notes' => $data['notes'] ?? null,
                'session_number' => $sessionNumber,
                'session_type' => $sessionType,
                'is_active' => true
            ];

            // Handle scheduled duty minutes (optional timer feature)
            if (isset($data['scheduled_duty_minutes']) && $data['scheduled_duty_minutes'] > 0) {
                $scheduledMinutes = (int) $data['scheduled_duty_minutes'];
                $scheduledEndTime = $currentTime->copy()->addMinutes($scheduledMinutes);
                
                $attendanceData['scheduled_duty_minutes'] = $scheduledMinutes;
                $attendanceData['scheduled_end_time'] = $scheduledEndTime;
            }

            $attendance = Attendance::create($attendanceData);

            DB::commit();

            // Log successful clock in
            Log::info('Clock in successful', [
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'clock_in' => $attendance->clock_in->toDateTimeString(),
                'work_date' => $attendance->work_date->toDateString(),
                'session_type' => $sessionType,
                'session_number' => $sessionNumber
            ]);

            // Dispatch Discord webhook job
            SendDiscordWebhookJob::dispatch(
                'check-in',
                $user->id,
                $user->name,
                $currentTime
            );

            $message = sprintf(
                'Clock in berhasil pada %s. Sesi #%d (%s) - Work Date: %s',
                $currentTime->format('d/m/Y H:i'),
                $sessionNumber,
                ucfirst($sessionType),
                $currentDate->format('d/m/Y')
            );

            return [
                'success' => true,
                'message' => $message,
                'data' => $attendance
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Clock in failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat clock in. Silakan coba lagi.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process clock out for user
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function processClockOut(User $user, array $data): array
    {
        $currentDate = Carbon::today(AttendanceConstants::DEFAULT_TIMEZONE);
        
        // Check if user can clock out
        $canClockOut = AttendanceHelper::canClockOut($user->id, $currentDate);
        if (!$canClockOut['can_clock_out']) {
            throw AttendanceException::noActiveSession([
                'user_id' => $user->id,
                'date' => $currentDate
            ]);
        }

        $attendance = $canClockOut['active_session'];

        DB::beginTransaction();
        try {
            // Update notes if provided
            if (!empty($data['notes'])) {
                $currentNotes = trim($attendance->notes ?? '');
                $newNotes = trim($data['notes']);
                $attendance->notes = $currentNotes ? $currentNotes . "\n" . $newNotes : $newNotes;
                $attendance->save();
            }

            // Close the session
            $closeResult = $attendance->closeSession();
            if (!$closeResult) {
                throw AttendanceException::sessionCloseFailed([
                    'attendance_id' => $attendance->id,
                    'user_id' => $user->id
                ]);
            }

            DB::commit();

            // Get session info for success message
            $isCrossDay = $attendance->isCrossDay();
            $duration = $attendance->getFormattedDuration();
            $durationHours = $attendance->getDurationInHours();

            // Log successful clock out
            Log::info('Clock out successful', [
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'clock_in' => $attendance->clock_in->toDateTimeString(),
                'clock_out' => $attendance->clock_out->toDateTimeString(),
                'duration_seconds' => $attendance->session_duration,
                'duration_formatted' => $duration,
                'is_cross_day' => $isCrossDay
            ]);

            // Dispatch Discord webhook job
            $todayHours = Attendance::getDailyTotalHours($user->id, $currentDate);
            $weeklyHours = $this->getWeeklyHours($user->id);
            
            SendDiscordWebhookJob::dispatch(
                'check-out',
                $user->id,
                $user->name,
                now(),
                $todayHours,
                $weeklyHours
            );

            $message = sprintf(
                'Clock out berhasil. Durasi kerja: %s (%.2f jam)%s',
                $duration,
                $durationHours,
                $isCrossDay ? ' - Cross-Day Session ✓' : ''
            );

            return [
                'success' => true,
                'message' => $message,
                'data' => $attendance
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Clock out failed', [
                'attendance_id' => $attendance->id ?? null,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat clock out. Silakan coba lagi.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get attendance history for user
     *
     * @param User $user
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAttendanceHistory(User $user, int $perPage = 20)
    {
        return Attendance::where('user_id', $user->id)
            ->orderBy('work_date', 'desc')
            ->orderBy('session_number')
            ->paginate($perPage);
    }

    /**
     * Get today's attendance data for user
     *
     * @param User $user
     * @return array
     */
    public function getTodayAttendance(User $user): array
    {
        return AttendanceHelper::getDailySummary($user->id);
    }

    /**
     * Get weekly hours for user
     *
     * @param int $userId
     * @return int
     */
    private function getWeeklyHours(int $userId): int
    {
        return Attendance::where('user_id', $userId)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('session_type', 'work')
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->sum('session_duration');
    }

    /**
     * Get attendance statistics for period
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getAttendanceStatistics(string $startDate, string $endDate): array
    {
        return AttendanceHelper::getPeriodStatistics($startDate, $endDate);
    }

    /**
     * Get long duty sessions
     *
     * @param int $thresholdHours
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLongDutySessions(int $thresholdHours = 8)
    {
        return AttendanceHelper::getLongDutySessions($thresholdHours);
    }

    /**
     * Validate attendance data
     *
     * @param array $data
     * @return array
     */
    public function validateAttendanceData(array $data): array
    {
        return AttendanceHelper::validateClockData($data);
    }
}
