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
     * Mendukung pembersihan prefix FiveM (char1:, citizen:, license:, #), whitespace tolerance,
     * serta smart name matching.
     */
    private function findUserByPlayerId($playerId, $playerName)
    {
        if (empty($playerId) && empty($playerName)) {
            return null;
        }

        $rawPlayerId = (string) $playerId;
        $normalizedPlayerId = trim(strtolower($rawPlayerId));
        
        // Bersihkan prefix umum FiveM (char1:, char2:, citizen:, cid:, id:, license:, steam:, discord:)
        $strippedPlayerId = preg_replace('/^(char\d+:|citizen:|cid:|id:|license:|license2:|steam:|discord:)/i', '', $normalizedPlayerId);
        $strippedPlayerId = trim(str_replace(['#', ' ', '-', '.'], '', $strippedPlayerId));

        // 1. Coba cari berdasarkan citizen_id atau staff_id - Exact Match Case-Insensitive
        $user = User::where(function($q) use ($normalizedPlayerId, $strippedPlayerId) {
            $q->whereRaw('LOWER(TRIM(citizen_id)) = ?', [$normalizedPlayerId])
              ->orWhereRaw('LOWER(TRIM(citizen_id)) = ?', [$strippedPlayerId])
              ->orWhereRaw('LOWER(TRIM(staff_id)) = ?', [$normalizedPlayerId])
              ->orWhereRaw('LOWER(TRIM(staff_id)) = ?', [$strippedPlayerId]);
        })->first();

        if ($user) {
            return $user;
        }

        // 2. Coba jika di DB ada prefix atau kebalikannya (Stripped DB citizen_id / staff_id = strippedPlayerId)
        if (!empty($strippedPlayerId)) {
            $user = User::where(function ($q) use ($strippedPlayerId) {
                $q->where(function($sq) use ($strippedPlayerId) {
                    $sq->whereNotNull('citizen_id')
                       ->where(function($ssq) use ($strippedPlayerId) {
                           $ssq->whereRaw("LOWER(REPLACE(REPLACE(REPLACE(TRIM(citizen_id), ' ', ''), '#', ''), '-', '')) = ?", [$strippedPlayerId])
                               ->orWhereRaw("LOWER(REPLACE(citizen_id, 'char1:', '')) = ?", [$strippedPlayerId])
                               ->orWhereRaw("LOWER(REPLACE(citizen_id, 'char2:', '')) = ?", [$strippedPlayerId])
                               ->orWhereRaw("LOWER(REPLACE(citizen_id, 'citizen:', '')) = ?", [$strippedPlayerId]);
                       });
                })
                ->orWhere(function($sq) use ($strippedPlayerId) {
                    $sq->whereNotNull('staff_id')
                       ->where(function($ssq) use ($strippedPlayerId) {
                           $ssq->whereRaw("LOWER(REPLACE(REPLACE(REPLACE(TRIM(staff_id), ' ', ''), '#', ''), '-', '')) = ?", [$strippedPlayerId])
                               ->orWhereRaw("LOWER(REPLACE(staff_id, 'char1:', '')) = ?", [$strippedPlayerId])
                               ->orWhereRaw("LOWER(REPLACE(staff_id, 'char2:', '')) = ?", [$strippedPlayerId])
                               ->orWhereRaw("LOWER(REPLACE(staff_id, 'citizen:', '')) = ?", [$strippedPlayerId]);
                       });
                });
            })->first();

            if ($user) {
                return $user;
            }
        }

        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
        $concatCit = $isSqlite ? "('%' || LOWER(TRIM(citizen_id)) || '%')" : "CONCAT('%', LOWER(TRIM(citizen_id)), '%')";
        $concatStaff = $isSqlite ? "('%' || LOWER(TRIM(staff_id)) || '%')" : "CONCAT('%', LOWER(TRIM(staff_id)), '%')";

        // 3. Coba cari jika salah satu mengandung yang lain (Substring match untuk ID >= 4 karakter)
        if (strlen($strippedPlayerId) >= 4) {
            $user = User::where(function ($q) use ($strippedPlayerId, $concatCit, $concatStaff) {
                $q->where(function($sq) use ($strippedPlayerId, $concatCit) {
                    $sq->whereNotNull('citizen_id')
                       ->where(function($ssq) use ($strippedPlayerId, $concatCit) {
                           $ssq->whereRaw('LOWER(citizen_id) LIKE ?', ['%' . $strippedPlayerId . '%'])
                               ->orWhereRaw("? LIKE {$concatCit}", [$strippedPlayerId]);
                       });
                })
                ->orWhere(function($sq) use ($strippedPlayerId, $concatStaff) {
                    $sq->whereNotNull('staff_id')
                       ->where(function($ssq) use ($strippedPlayerId, $concatStaff) {
                           $ssq->whereRaw('LOWER(staff_id) LIKE ?', ['%' . $strippedPlayerId . '%'])
                               ->orWhereRaw("? LIKE {$concatStaff}", [$strippedPlayerId]);
                       });
                });
            })->first();

            if ($user) {
                return $user;
            }
        }

        // 4. Coba cari berdasarkan nama (Smart Name Matching Fallback)
        if (!empty($playerName)) {
            $cleanPlayerName = trim(preg_replace('/^(rh\s*[-|–]?\s*|alta\s*[-|–]?\s*|roxwood\s*[-|–]?\s*|medic\s*[-|–]?\s*|ems\s*[-|–]?\s*|\[[^\]]+\]|\bdr\.?|\bdokter\b)/i', '', (string)$playerName));
            $cleanPlayerName = trim(str_replace('_', ' ', $cleanPlayerName));

            if (strlen($cleanPlayerName) >= 3) {
                $concatName = $isSqlite ? "('%' || name || '%')" : "CONCAT('%', name, '%')";
                $user = User::where('name', 'LIKE', '%' . $cleanPlayerName . '%')
                    ->orWhereRaw("? LIKE {$concatName}", [$cleanPlayerName])
                    ->first();

                if ($user) {
                    Log::info('[AttendanceIntegration] User matched via playerName fallback', [
                        'player_id'    => $playerId,
                        'player_name'  => $playerName,
                        'matched_user' => $user->name,
                        'user_cit_id'  => $user->citizen_id,
                    ]);
                    
                    // Auto-sync citizen_id & staff_id jika kosong pada user yang cocok
                    if (empty($user->citizen_id) && !empty($strippedPlayerId)) {
                        $user->citizen_id = strtoupper($strippedPlayerId);
                        $user->staff_id = $user->staff_id ?: strtoupper($strippedPlayerId);
                        $user->save();
                    }

                    return $user;
                }
            }
        }

        Log::warning('[AttendanceIntegration] User NOT found for FiveM player', [
            'player_id'     => $playerId,
            'player_name'   => $playerName,
            'normalized_id' => $normalizedPlayerId,
            'stripped_id'   => $strippedPlayerId
        ]);

        return null;
    }

    /**
     * Cek apakah ada konflik dengan absensi manual
     */
    private function checkManualAttendanceConflict($userId, $clockIn, $clockOut)
    {
        $clockInDate = Carbon::parse($clockIn);
        $clockOutDate = $clockOut ? Carbon::parse($clockOut) : null;

        // Cek apakah ada sesi aktif untuk user ini (lintas hari / kapanpun)
        $activeSession = Attendance::getAnyActiveSession($userId);

        if ($activeSession) {
            return [
                'has_conflict' => true,
                'type' => 'active_session',
                'conflicting_record' => $activeSession,
                'message' => 'Ada sesi aktif untuk user ini'
            ];
        }

        // Cek apakah ada overlap dengan sesi manual yang sudah ada (abaikan sesi fivem sebelumnya)
        $overlappingSessions = Attendance::forUser($userId)
            ->where('source', '!=', 'fivem')
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
                        'message' => 'Sesi sebelumnya tidak valid, absensi FiveM dimulai baru.',
                        'priority' => 'automatic',
                        'data' => $absensi
                    ];
                }

                // KASUS 1: Request ini adalah Clock Out (off-duty FiveM)
                if ($clockOut) {
                    $clockOutTime = Carbon::parse($clockOut);
                    $clockInTime = Carbon::parse($activeSession->clock_in);

                    // Durasi dari clock_in sesi aktif hingga clock_out ini
                    $durationSeconds = max(0, $clockInTime->diffInSeconds($clockOutTime));
                    $activeSession->update([
                        'clock_out'        => $clockOutTime,
                        'is_active'        => false,
                        'session_duration' => $durationSeconds,
                        'total_hours'      => max(1, floor($durationSeconds / 60)),
                    ]);

                    // AUTO-SPLIT jika melewati tengah malam (cross-day)
                    if ($activeSession->isCrossDay()) {
                        try {
                            $activeSession->splitCrossDaySession();
                        } catch (\Exception $e) {
                            Log::error('Failed to split cross-day in handleAttendanceConflict clock_out', [
                                'attendance_id' => $activeSession->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $activeSession->clock_in, $clockOut, $timeOnDuty);
                    try {
                        $user->update(['status' => 'offline']);
                    } catch (\Exception $e) {}

                    return [
                        'success'  => true,
                        'message'  => 'Sesi aktif berhasil ditutup dengan FiveM clock_out.',
                        'priority' => 'automatic',
                        'data'     => $absensi
                    ];
                }

                // KASUS 2: Request ini adalah Clock In baru (duty FiveM baru)
                // Jika activeSession adalah sesi FiveM yang sama dan baru saja clock-in (dalam 5 menit)
                if ($activeSession->source === 'fivem' && Carbon::parse($activeSession->clock_in)->diffInMinutes(Carbon::parse($clockIn)) < 5) {
                    $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $activeSession->clock_in, null, null);
                    return [
                        'success'  => true,
                        'message'  => 'Player sudah terdata clock in.',
                        'priority' => 'automatic',
                        'data'     => $absensi
                    ];
                }

                // Tutup sesi aktif sebelumnya (baik manual maupun sesi FiveM lama)
                $manualClockOutTime = Carbon::parse($clockIn);
                $manualClockInTime = Carbon::parse($activeSession->clock_in);

                if ($manualClockOutTime->gt($manualClockInTime)) {
                    $activeSession->clock_out = $manualClockOutTime;
                    $activeSession->session_duration = $this->calculateDurationSeconds($activeSession->clock_in, $activeSession->clock_out);
                    $activeSession->total_hours      = max(1, floor($activeSession->session_duration / 60));
                    $activeSession->is_active        = false;
                    $existingNotes = $activeSession->notes ?? '';
                    $activeSession->notes = trim($existingNotes . " (Auto-closed by new FiveM Duty)");
                    $activeSession->save();

                    if ($activeSession->isCrossDay()) {
                        try {
                            $activeSession->splitCrossDaySession();
                        } catch (\Exception $e) {
                            Log::error('Failed to split cross-day in handleAttendanceConflict new login', [
                                'attendance_id' => $activeSession->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } else {
                    $activeSession->update(['is_active' => false]);
                }

                // Update user status
                try {
                    $user->update(['status' => 'offline']);
                } catch (\Exception $e) {
                    Log::warning('Failed to update user status', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Simpan data FiveM sebagai sesi BARU
                $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
                $this->createManualAttendanceRecord($user, $absensi);

                return [
                    'success' => true,
                    'message' => 'Sesi sebelumnya ditutup. Absensi FiveM baru dimulai.',
                    'priority' => 'automatic',
                    'updated_record' => $absensi
                ];

            case 'overlapping_session':
                // Simpan data otomatis
                $absensi = $this->saveAutomaticAttendance($playerId, $playerName, $clockIn, $clockOut, $timeOnDuty);
                $absensi->update(['notes' => 'Tercatat otomatis via FiveM']);

                // Pastikan record attendance juga dibuat/diupdate dan displit jika cross-day
                $this->createManualAttendanceRecord($user, $absensi);

                return [
                    'success' => true,
                    'message' => 'Data absensi otomatis berhasil disimpan dan disinkronkan',
                    'priority' => 'automatic',
                    'data' => $absensi
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

            // Auto-split jika melewati tengah malam (cross-day)
            if ($existingRecord->clock_out && $existingRecord->isCrossDay()) {
                try {
                    $existingRecord->splitCrossDaySession();
                } catch (\Exception $e) {
                    Log::error('Failed to split cross-day in createManualAttendanceRecord (existing)', [
                        'attendance_id' => $existingRecord->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($absensi->clock_out) {
                $user->update(['status' => 'offline']);
            } else {
                $user->update(['status' => 'working']);
            }

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

        // Auto-split jika melewati tengah malam (cross-day)
        if ($attendance->clock_out && $attendance->isCrossDay()) {
            try {
                $attendance->splitCrossDaySession();
            } catch (\Exception $e) {
                Log::error('Failed to split cross-day in createManualAttendanceRecord (new)', [
                    'attendance_id' => $attendance->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

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

        if ($user) {
            $identifiers = array_filter(array_unique([
                $user->staff_id,
                $user->citizen_id,
                $user->staff_id ? strtolower($user->staff_id) : null,
                $user->staff_id ? strtoupper($user->staff_id) : null,
                $user->citizen_id ? strtolower($user->citizen_id) : null,
                $user->citizen_id ? strtoupper($user->citizen_id) : null,
                $user->citizen_id ? 'char1:' . strtolower($user->citizen_id) : null,
                $user->citizen_id ? 'citizen:' . strtolower($user->citizen_id) : null,
            ]));

            if (!empty($identifiers)) {
                $automaticAttendance = Absensi::whereIn('player_id', $identifiers)
                    ->whereBetween('clock_in', [$dateFrom, $dateTo])
                    ->orderBy('clock_in', 'desc')
                    ->get();
            }
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
            $duration = method_exists($record, 'calculateTotalHours')
                ? $record->calculateTotalHours()
                : ($record->session_duration ?? 0);
            $durationFormatted = method_exists($record, 'getFormattedDuration')
                ? $record->getFormattedDuration()
                : gmdate('H:i:s', (int)($record->session_duration ?? 0));

            $combined->push([
                'type' => 'manual',
                'id' => $record->id,
                'clock_in' => $record->clock_in,
                'clock_out' => $record->clock_out,
                'duration' => $duration,
                'duration_formatted' => $durationFormatted,
                'source' => 'Manual System',
                'notes' => $record->notes
            ]);
        }

        // Add automatic attendance
        foreach ($automatic as $record) {
            $durationSeconds = method_exists($record, 'getDurationInSeconds')
                ? $record->getDurationInSeconds()
                : (isset($record->time_on_duty) ? \App\Helpers\TimeHelper::timeToSeconds($record->time_on_duty) : 0);
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
        $remainingMinutes = fmod($minutes, 60);
        $seconds = round(($remainingMinutes - floor($remainingMinutes)) * 60);

        return sprintf('%02d:%02d:%02d', $hours, floor($remainingMinutes), (int)$seconds);
    }
}
