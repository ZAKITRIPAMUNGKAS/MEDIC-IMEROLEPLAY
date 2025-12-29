<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceIntegrationService
{
    /**
     * Integrasikan data absensi otomatis dengan sistem manual
     * 
     * @param string $playerId
     * @param string $playerName
     * @param string $clockIn
     * @param string $clockOut
     * @param string $timeOnDuty
     * @return array
     */
    public function integrateAttendanceData($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty)
    {
        try {
            // Cari user berdasarkan player_id atau player_name
            $user = $this->findUserByPlayerId($playerId, $playerName);
            
            if (!$user) {
                Log::warning('User not found for player', [
                    'player_id' => $playerId,
                    'player_name' => $playerName
                ]);
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan untuk player ID: ' . $playerId
                ];
            }

            // Cek apakah ada konflik dengan absensi manual
            $conflict = $this->checkManualAttendanceConflict($user->id, $clockIn, $clockOut);
            
            if ($conflict['has_conflict']) {
                return $this->handleAttendanceConflict($user, $conflict, $playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
            }

            // Simpan data absensi otomatis
            $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
            
            // Buat record di sistem manual untuk konsistensi
            $this->createManualAttendanceRecord($user, $absensi);

            return [
                'success' => true,
                'message' => 'Data absensi berhasil diintegrasikan',
                'data' => $absensi
            ];

        } catch (\Exception $e) {
            Log::error('Error integrating attendance data', [
                'player_id' => $playerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengintegrasikan data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cari user berdasarkan player_id atau player_name
     */
    private function findUserByPlayerId($playerId, $playerName)
    {
        // Coba cari berdasarkan staff_id yang sama dengan player_id
        $user = User::where('staff_id', $playerId)->first();
        
        if (!$user) {
            // Coba cari berdasarkan nama yang mirip
            $user = User::where('name', 'LIKE', '%' . $playerName . '%')->first();
        }

        return $user;
    }

    /**
     * Cek apakah ada konflik dengan absensi manual
     */
    private function checkManualAttendanceConflict($userId, $clockIn, $clockOut)
    {
        $clockInDate = Carbon::parse($clockIn);
        $clockOutDate = $clockOut ? Carbon::parse($clockOut) : null;

        // Cek apakah ada sesi aktif manual pada hari yang sama
        $activeSession = Attendance::getActiveSession($userId, $clockInDate->toDateString());
        
        if ($activeSession) {
            return [
                'has_conflict' => true,
                'type' => 'active_session',
                'conflicting_record' => $activeSession,
                'message' => 'Ada sesi manual aktif pada hari yang sama'
            ];
        }

        // Cek apakah ada overlap dengan sesi manual yang sudah ada
        $overlappingSessions = Attendance::forUser($userId)
            ->forDate($clockInDate->toDateString())
            ->where(function ($query) use ($clockInDate, $clockOutDate) {
                $query->whereBetween('clock_in', [$clockInDate, $clockOutDate ?? now()])
                      ->orWhereBetween('clock_out', [$clockInDate, $clockOutDate ?? now()])
                      ->orWhere(function ($q) use ($clockInDate, $clockOutDate) {
                          $q->where('clock_in', '<=', $clockInDate)
                            ->where('clock_out', '>=', $clockOutDate ?? now());
                      });
            })
            ->get();

        if ($overlappingSessions->count() > 0) {
            return [
                'has_conflict' => true,
                'type' => 'overlapping_session',
                'conflicting_records' => $overlappingSessions,
                'message' => 'Ada sesi manual yang overlap dengan waktu absensi otomatis'
            ];
        }

        return ['has_conflict' => false];
    }

    /**
     * Handle konflik absensi
     */
    private function handleAttendanceConflict($user, $conflict, $playerId, $playerName, $clockIn, $clockOut, $timeOnDuty)
    {
        switch ($conflict['type']) {
            case 'active_session':
                // Prioritas: Manual > Otomatis
                // Update sesi manual dengan data dari otomatis
                $activeSession = $conflict['conflicting_record'];
                
                if ($clockOut) {
                    $activeSession->clock_out = Carbon::parse($clockOut);
                    $activeSession->session_duration = $this->calculateDuration($clockIn, $clockOut);
                    $activeSession->closeSession();
                }

                // Simpan data otomatis sebagai backup
                $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);

                return [
                    'success' => true,
                    'message' => 'Data absensi manual diupdate dengan data otomatis',
                    'priority' => 'manual',
                    'updated_record' => $activeSession
                ];

            case 'overlapping_session':
                // Prioritas: Manual > Otomatis
                // Simpan data otomatis dengan catatan konflik
                $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
                $absensi->update(['notes' => 'Konflik dengan sesi manual - prioritas manual']);

                return [
                    'success' => true,
                    'message' => 'Data absensi otomatis disimpan dengan catatan konflik',
                    'priority' => 'manual',
                    'conflict_note' => 'Ada overlap dengan sesi manual'
                ];

            default:
                return [
                    'success' => false,
                    'message' => 'Konflik tidak dapat diselesaikan'
                ];
        }
    }

    /**
     * Simpan data absensi otomatis
     */
    private function saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty)
    {
        return Absensi::create([
            'player_id' => $playerId,
            'player_name' => $playerName,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'time_on_duty' => $timeOnDuty,
            'source' => 'automatic' // Tambahkan field source untuk tracking
        ]);
    }

    /**
     * Buat record di sistem manual untuk konsistensi
     */
    private function createManualAttendanceRecord($user, $absensi)
    {
        $workDate = Carbon::parse($absensi->clock_in)->toDateString();
        $sessionNumber = Attendance::getNextSessionNumber($user->id, $workDate);

        return Attendance::create([
            'user_id' => $user->id,
            'clock_in' => $absensi->clock_in,
            'clock_out' => $absensi->clock_out,
            'work_date' => $workDate,
            'session_number' => $sessionNumber,
            'session_type' => 'work',
            'is_active' => !$absensi->clock_out,
            'session_duration' => $absensi->clock_out ? $this->calculateDuration($absensi->clock_in, $absensi->clock_out) : null,
            'notes' => 'Generated from automatic attendance (FiveM)',
            'total_hours' => $absensi->clock_out ? $this->calculateDuration($absensi->clock_in, $absensi->clock_out) : null
        ]);
    }

    /**
     * Hitung durasi dalam menit
     */
    private function calculateDuration($clockIn, $clockOut)
    {
        return Carbon::parse($clockIn)->diffInMinutes(Carbon::parse($clockOut));
    }

    /**
     * Get combined attendance data for user
     */
    public function getCombinedAttendanceData($userId, $dateFrom = null, $dateTo = null)
    {
        $dateFrom = $dateFrom ?? now()->startOfMonth();
        $dateTo = $dateTo ?? now()->endOfMonth();

        // Get manual attendance
        $manualAttendance = Attendance::forUser($userId)
            ->whereBetween('work_date', [$dateFrom, $dateTo])
            ->orderBy('work_date', 'desc')
            ->get();

        // Get automatic attendance (if user has player_id mapping)
        $user = User::find($userId);
        $automaticAttendance = collect();

        if ($user && $user->staff_id) {
            $automaticAttendance = Absensi::byPlayer($user->staff_id)
                ->whereBetween('clock_in', [$dateFrom, $dateTo])
                ->orderBy('clock_in', 'desc')
                ->get();
        }

        return [
            'manual' => $manualAttendance,
            'automatic' => $automaticAttendance,
            'combined' => $this->mergeAttendanceData($manualAttendance, $automaticAttendance)
        ];
    }

    /**
     * Merge manual and automatic attendance data
     */
    private function mergeAttendanceData($manual, $automatic)
    {
        $combined = collect();

        // Add manual attendance
        foreach ($manual as $record) {
            $combined->push([
                'type' => 'manual',
                'id' => $record->id,
                'clock_in' => $record->clock_in,
                'clock_out' => $record->clock_out,
                'duration' => $record->calculateTotalHours(), // Use consistent method
                'duration_formatted' => $record->getFormattedDuration(),
                'source' => 'Manual System',
                'notes' => $record->notes
            ]);
        }

        // Add automatic attendance
        foreach ($automatic as $record) {
            $durationSeconds = $record->getDurationInSeconds();
            $combined->push([
                'type' => 'automatic',
                'id' => $record->id,
                'clock_in' => $record->clock_in,
                'clock_out' => $record->clock_out,
                'duration' => $durationSeconds,
                'duration_formatted' => $durationSeconds ? gmdate('H:i:s', $durationSeconds) : '00:00:00',
                'source' => 'FiveM System',
                'notes' => $record->notes ?? 'Automatic from FiveM'
            ]);
        }

        return $combined->sortByDesc('clock_in');
    }

    /**
     * Get total work hours for user (combined manual + automatic)
     */
    public function getTotalWorkHours($userId, $period = 'month')
    {
        $dateFrom = $this->getPeriodStart($period);
        $dateTo = $this->getPeriodEnd($period);

        $data = $this->getCombinedAttendanceData($userId, $dateFrom, $dateTo);
        
        $totalMinutes = 0;
        
        // Calculate from manual attendance
        foreach ($data['manual'] as $record) {
            if ($record->session_duration) {
                $totalMinutes += $record->session_duration;
            }
        }

        // Calculate from automatic attendance
        foreach ($data['automatic'] as $record) {
            if ($record->time_on_duty) {
                $timeParts = explode(':', $record->time_on_duty);
                $totalMinutes += ($timeParts[0] * 60) + $timeParts[1] + ($timeParts[2] / 60);
            }
        }

        return [
            'total_minutes' => $totalMinutes,
            'total_hours' => round($totalMinutes / 60, 2),
            'formatted_time' => $this->formatTime($totalMinutes)
        ];
    }

    /**
     * Get period start date
     */
    private function getPeriodStart($period)
    {
        switch ($period) {
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->startOfMonth();
        }
    }

    /**
     * Get period end date
     */
    private function getPeriodEnd($period)
    {
        switch ($period) {
            case 'week':
                return now()->endOfWeek();
            case 'month':
                return now()->endOfMonth();
            case 'year':
                return now()->endOfYear();
            default:
                return now()->endOfMonth();
        }
    }

    /**
     * Format time in HH:MM:SS
     */
    private function formatTime($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        $seconds = ($minutes - floor($minutes)) * 60;
        
        return sprintf('%02d:%02d:%02d', $hours, floor($minutes), floor($seconds));
    }
}
