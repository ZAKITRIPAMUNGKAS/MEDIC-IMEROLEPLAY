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
        $normalizedPlayerId = trim(strtolower($playerId));

        // 1. Coba cari berdasarkan citizen_id (FiveM ID) - Prioritas utama
        // Gunakan whereRaw untuk case-insensitive yang konsisten antar database drivers
        $user = User::whereRaw('LOWER(citizen_id) = ?', [$normalizedPlayerId])->first();

        if ($user) {
            return $user;
        }

        // 2. Coba cari berdasarkan staff_id (Legacy / Badge Number)
        $user = User::whereRaw('LOWER(staff_id) = ?', [$normalizedPlayerId])->first();

        if ($user) {
            return $user;
        }

        // 3. Coba cari berdasarkan nama yang mirip (Last resort)
        // Hanya jika nama cukup panjang untuk menghindari false positive pendek
        if (strlen($playerName) > 3) {
            $user = User::where('name', 'LIKE', '%' . $playerName . '%')->first();
        }

        return $user ?? null;
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
                // REVISI: Jika FiveM login ('clockIn') saat ada sesi manual aktif:
                // TUTUP sesi manual pada saat FiveM clockIn.
                // Biarkan sesi FiveM berjalan sebagai sesi baru (Automatic).
                // Ini memisahkan jam manual dan jam FiveM agar tidak tumpang tindih berlebihan.

                $activeSession = $conflict['conflicting_record'];

                // Validasi: Pastikan activeSession valid
                if (!$activeSession || !$activeSession->clock_in) {
                    Log::warning('Invalid active session in conflict handler', [
                        'player_id' => $playerId,
                        'active_session' => $activeSession
                    ]);

                    // Fallback: Simpan sebagai sesi baru
                    $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
                    $this->createManualAttendanceRecord($user, $absensi);

                    return [
                        'success' => true,
                        'message' => 'Sesi manual tidak valid, absensi FiveM dimulai baru.',
                        'priority' => 'automatic',
                        'data' => $absensi
                    ];
                }

                // Set jam keluar manual = jam masuk FiveM
                $manualClockOutTime = Carbon::parse($clockIn);
                $manualClockInTime = Carbon::parse($activeSession->clock_in);

                // Pastikan tidak error logika (clock out < clock in)
                // Jika FiveM login SEBELUM manual clock in, ini edge case aneh - skip close
                if ($manualClockOutTime->lte($manualClockInTime)) {
                    Log::warning('FiveM clock_in is before or equal to manual clock_in, skipping close', [
                        'player_id' => $playerId,
                        'manual_clock_in' => $manualClockInTime->toDateTimeString(),
                        'fivem_clock_in' => $manualClockOutTime->toDateTimeString()
                    ]);

                    // Simpan FiveM sebagai sesi terpisah tanpa menutup manual
                    $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
                    $this->createManualAttendanceRecord($user, $absensi);

                    return [
                        'success' => true,
                        'message' => 'Absensi FiveM dimulai (sesi manual tetap aktif karena timing conflict).',
                        'priority' => 'automatic',
                        'conflict_note' => 'Manual session remains active due to timing anomaly'
                    ];
                }

                // Tutup sesi manual
                $activeSession->clock_out = $manualClockOutTime;
                $activeSession->session_duration = $this->calculateDuration($activeSession->clock_in, $activeSession->clock_out);
                $activeSession->is_active = false;

                // Handle null notes safely
                $existingNotes = $activeSession->notes ?? '';
                $activeSession->notes = trim($existingNotes . " (Auto-closed by FiveM Login)");

                $activeSession->save();

                // Update user status agar tidak stuck
                try {
                    $user->update(['status' => 'offline']); // Reset ke offline, nanti FiveM akan set 'working' via createManualAttendanceRecord
                } catch (\Exception $e) {
                    Log::warning('Failed to update user status', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Setelah sesi manual ditutup, Simpan data FiveM sebagai sesi BARU (Automatic)
                $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);

                // Kita juga membuat record MANAUAL baru yang mirror ke FiveM?
                // Logic createManualAttendanceRecord akan dipanggil di `integrateAttendanceData` setelah ini return?
                // TIDAK. `integrateAttendanceData` return hasil dari fungsi ini jika konflik.
                // Jadi kita harus handle pembuatan record FiveM-Mirror dngn createManualAttendanceRecord DISINI jika perlu.

                // Tapi tunggu, `createManualAttendanceRecord` di `integrateAttendanceData` dipanggil SETELAH `saveAutomaticAttendance`
                // DAN `handleAttendanceConflict` return array.
                // Di `integrateAttendanceData`:
                // if ($conflict['has_conflict']) { return $this->handleAttendanceConflict(...); }
                // Jadi `integrateAttendanceData` BERHENTI setelah ini.

                // Maka kita harus create manual mirror disini juga agar konsisten.
                $this->createManualAttendanceRecord($user, $absensi);

                return [
                    'success' => true,
                    'message' => 'Sesi manual ditutup otomatis. Absensi FiveM dimulai baru.',
                    'priority' => 'automatic', // Switch ke automatic
                    'updated_record' => $absensi
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
        // 1. Jika ini adalah Clock Out, tutup SEMUA sesi aktif sebelumnya untuk player ini
        // Ini untuk membersihkan "zombie session" jika terjadi error sebelumnya
        if ($clockOut) {
            $activeSessions = Absensi::where('player_id', $playerId)
                ->whereNull('clock_out')
                ->get();

            foreach ($activeSessions as $session) {
                $session->update([
                    'clock_out' => $clockOut,
                    'time_on_duty' => $timeOnDuty ?? $session->getFormattedDuration(),
                    'source' => 'automatic_cleanup'
                ]);
            }
        }

        // 2. Gunakan updateOrCreate untuk menyimpan data saat ini
        // Jika kriteria cocok (player_id & clock_in sama), update record tersebut
        return Absensi::updateOrCreate(
            [
                'player_id' => $playerId,
                'clock_in' => $clockIn
            ],
            [
                'player_name' => $playerName,
                'clock_out' => $clockOut,
                'time_on_duty' => $timeOnDuty,
                'source' => 'automatic'
            ]
        );
    }

    /**
     * Buat record di sistem manual untuk konsistensi
     */
    private function createManualAttendanceRecord($user, $absensi)
    {
        $workDate = Carbon::parse($absensi->clock_in)->toDateString();

        // Cek apakah sudah ada record untuk user ini di tanggal yang sama dengan clock_in yang sama
        $existingRecord = Attendance::where('user_id', $user->id)
            ->where('work_date', $workDate)
            ->where('clock_in', $absensi->clock_in)
            ->first();

        if ($existingRecord) {
            // Jika sudah ada, update saja
            $existingRecord->update([
                'clock_out'        => $absensi->clock_out,
                'is_active'        => !$absensi->clock_out,
                // session_duration = DETIK (untuk perhitungan akurat)
                'session_duration' => $absensi->clock_out ? $this->calculateDurationSeconds($absensi->clock_in, $absensi->clock_out) : null,
                // total_hours = MENIT (backward compatibility)
                'total_hours'      => $absensi->clock_out ? $this->calculateDuration($absensi->clock_in, $absensi->clock_out) : null
            ]);
            return $existingRecord;
        }

        // Jika belum ada, buat baru
        $sessionNumber = Attendance::getNextSessionNumber($user->id, $workDate);

        $attendance = Attendance::create([
            'user_id'          => $user->id,
            'clock_in'         => $absensi->clock_in,
            'clock_out'        => $absensi->clock_out,
            'work_date'        => $workDate,
            'session_number'   => $sessionNumber,
            'session_type'     => 'work',
            'is_active'        => !$absensi->clock_out,
            // session_duration = DETIK (untuk perhitungan akurat)
            'session_duration' => $absensi->clock_out ? $this->calculateDurationSeconds($absensi->clock_in, $absensi->clock_out) : null,
            'notes'            => 'Generated from automatic attendance (FiveM)',
            // total_hours = MENIT (backward compatibility)
            'total_hours'      => $absensi->clock_out ? $this->calculateDuration($absensi->clock_in, $absensi->clock_out) : null,
            'source'           => 'fivem'
        ]);

        // Update user status to 'working' to match manual clock-in behavior
        // This ensures dashboard indicators (like status dots) update correctly
        // If clock_out is set, user is offline (session already ended)
        if ($absensi->clock_out) {
            $user->update(['status' => 'offline']);
        } else {
            $user->update(['status' => 'working']);
        }

        return $attendance;
    }

    /**
     * Hitung durasi dalam menit (untuk total_hours - backward compatibility)
     */
    private function calculateDuration($clockIn, $clockOut)
    {
        return Carbon::parse($clockIn)->diffInMinutes(Carbon::parse($clockOut));
    }

    /**
     * Hitung durasi dalam detik (untuk session_duration - nilai akurat)
     */
    private function calculateDurationSeconds($clockIn, $clockOut)
    {
        return Carbon::parse($clockIn)->diffInSeconds(Carbon::parse($clockOut));
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
