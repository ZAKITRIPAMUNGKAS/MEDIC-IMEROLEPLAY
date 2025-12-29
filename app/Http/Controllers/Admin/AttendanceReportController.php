<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    /**
     * Display attendance reports page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = [
            'date_from' => $request->get('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to' => $request->get('date_to', now()->endOfMonth()->format('Y-m-d')),
            'clock_in_only' => $request->get('clock_in_only'),
            'export' => $request->get('export'),
            // support multiple export periods: daily, weekly, monthly
            'period' => $request->get('period', 'daily')
        ];

        // Build query + search by name only (email removed for privacy)
        $query = Attendance::with('user')
            ->whereBetween('work_date', [
                Carbon::parse($filters['date_from'])->startOfDay(),
                Carbon::parse($filters['date_to'])->endOfDay()
            ]);
        if ($request->filled('q')) {
            $q = trim($request->get('q'));
            $query->where(function ($query) use ($q) {
                $query->whereHas('user', function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%");
                })->orWhere('user_id', 'like', "%$q%"); // Fallback untuk user yang sudah dihapus
            });
        }

        // Filter hanya yang masih clock in (untuk mempermudah force check out)
        if ($request->filled('clock_in_only') && $request->get('clock_in_only') == '1') {
            $query->where('is_active', true)
                ->whereNull('clock_out');
        }

        // Get attendances
        try {
            $attendances = $query->orderBy('work_date', 'desc')
                ->orderBy('user_id')
                ->paginate(50)->withQueryString();
        } catch (\Exception $e) {
            \Log::error('Error fetching attendances', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $attendances = collect([]);
        }

        // Calculate summary statistics
        $summary = $this->calculateSummary($attendances, $filters);

        // Get all users for manual entry form
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);


        // Handle export
        if ($filters['export']) {
            return $this->exportToCsv($attendances, $summary, $filters);
        }

        return view('admin.attendance-reports.index', compact(
            'attendances',
            'summary',
            'filters',
            'users'
        ));
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($attendances, $filters)
    {
        // Filter hanya record yang valid untuk perhitungan
        $validAttendances = $attendances->filter(function ($attendance) {
            $duration = $attendance->session_duration ?? ($attendance->total_hours ?? 0) * 60;
            return $duration && $duration > 0;
        });

        $totalDays = $validAttendances->count();
        $totalHours = $validAttendances->sum(function ($attendance) {
            return $attendance->session_duration ?? ($attendance->total_hours ?? 0) * 60;
        });
        $averageHours = $totalDays > 0 ? $totalHours / $totalDays : 0;

        // Group by user
        $userStats = $validAttendances->groupBy('user_id')->map(function ($userAttendances) {
            $totalHours = $userAttendances->sum(function ($attendance) {
                return $attendance->session_duration ?? ($attendance->total_hours ?? 0) * 60;
            });
            $totalDays = $userAttendances->count();
            $averageHours = $totalDays > 0 ? $totalHours / $totalDays : 0;

            $firstAttendance = $userAttendances->first();
            $user = $firstAttendance->user ?? null;

            // Create a fallback user object if user is null
            if (!$user && $firstAttendance) {
                $user = (object) [
                    'id' => $firstAttendance->user_id,
                    'name' => 'User #' . $firstAttendance->user_id
                ];
            }

            return [
                'user' => $user,
                'total_days' => $totalDays,
                'total_hours' => $totalHours,
                'average_hours' => $averageHours,
                'first_attendance' => $userAttendances->min('work_date'),
                'last_attendance' => $userAttendances->max('work_date'),
            ];
        });

        // Daily statistics
        $dailyStats = $validAttendances->groupBy('work_date')->map(function ($dayAttendances) {
            $totalHours = $dayAttendances->sum(function ($attendance) {
                return $attendance->session_duration ?? ($attendance->total_hours ?? 0) * 60;
            });
            $userCount = $dayAttendances->count();
            $averageHours = $userCount > 0 ? $totalHours / $userCount : 0;

            $firstDay = $dayAttendances->first();
            return [
                'date' => $firstDay->work_date ?? null,
                'user_count' => $userCount,
                'total_hours' => $totalHours,
                'average_hours' => $averageHours,
            ];
        });

        return [
            'total_days' => $totalDays,
            'total_hours' => $totalHours,
            'average_hours' => $averageHours,
            'user_stats' => $userStats,
            'daily_stats' => $dailyStats,
            'date_range' => [
                'from' => $filters['date_from'],
                'to' => $filters['date_to']
            ]
        ];
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($attendances, $summary, $filters)
    {
        $period = $filters['period'] ?? 'daily';
        // allow shorthand export=weekly to set period automatically
        if ($filters['export'] === 'weekly') {
            $period = 'weekly';
        }
        $filename = 'laporan_absensi_' . $period . '_' . $filters['date_from'] . '_to_' . $filters['date_to'] . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendances, $summary, $filters, $period) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header based on period
            if ($period === 'daily') {
                fputcsv($file, [
                    'Tanggal',
                    'Nama Staf',
                    'Clock In',
                    'Clock Out',
                    'Total Jam',
                    'Status'
                ]);

                // Data
                foreach ($attendances as $attendance) {
                    fputcsv($file, [
                        $attendance->work_date->format('d/m/Y'),
                        $attendance->user->name ?? ('User #' . $attendance->user_id),
                        $attendance->clock_in ? $attendance->clock_in->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-',
                        $attendance->clock_out ? $attendance->clock_out->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-',
                        TimeHelper::formatDuration($attendance->session_duration ?? ($attendance->total_hours ?? 0) * 60),
                        $attendance->clock_out ? 'Selesai' : 'Belum Selesai'
                    ]);
                }
            } elseif ($period === 'weekly') {
                fputcsv($file, [
                    'Minggu',
                    'Total Hari',
                    'Total Jam',
                    'Rata-rata/Hari',
                    'Staf Aktif',
                    'Nama Staf',
                    'Jam Staf',
                ]);

                // Group by week
                $weeklyData = $attendances->groupBy(function ($attendance) {
                    return $attendance->work_date->startOfWeek()->format('Y-m-d');
                });

                foreach ($weeklyData as $weekStart => $weekAttendances) {
                    $weekEnd = \Carbon\Carbon::parse($weekStart)->endOfWeek();
                    $totalHours = $weekAttendances->sum(function ($item) {
                        return $item->session_duration ?? ($item->total_hours ?? 0) * 60;
                    });
                    $totalDays = $weekAttendances->count();
                    $averageHours = $totalDays > 0 ? $totalHours / $totalDays : 0;
                    $activeStaff = $weekAttendances->pluck('user_id')->unique()->count();

                    // Write week summary row (with general metrics only)
                    fputcsv($file, [
                        \Carbon\Carbon::parse($weekStart)->format('d/m') . ' - ' . $weekEnd->format('d/m/Y'),
                        $totalDays,
                        TimeHelper::formatDuration($totalHours),
                        TimeHelper::formatDuration($averageHours),
                        $activeStaff,
                        '',
                        ''
                    ]);

                    // Then detail rows per staff
                    $byUser = $weekAttendances->groupBy('user_id');
                    foreach ($byUser as $userId => $items) {
                        $u = $items->first()->user;
                        $uTotal = $items->sum(function ($item) {
                            return $item->session_duration ?? ($item->total_hours ?? 0) * 60;
                        });
                        fputcsv($file, [
                            '',
                            '',
                            '',
                            '',
                            '',
                            $u?->name ?? ('User #' . $userId),
                            TimeHelper::formatDuration($uTotal)
                        ]);
                    }
                }
            } elseif ($period === 'monthly') {
                // Group by month
                $monthlyData = $attendances->groupBy(function ($attendance) {
                    return $attendance->work_date->format('Y-m');
                });

                foreach ($monthlyData as $month => $monthAttendances) {
                    $totalHours = $monthAttendances->sum('session_duration');
                    $totalDays = $monthAttendances->count();
                    $averageHours = $totalDays > 0 ? $totalHours / $totalDays : 0;
                    $activeStaff = $monthAttendances->pluck('user_id')->unique()->count();

                    // Group by user for detailed breakdown
                    $byUser = $monthAttendances->groupBy('user_id');

                    // Write monthly summary header
                    fputcsv($file, [
                        'BULAN: ' . \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                        'Total Hari: ' . $totalDays,
                        'Total Jam: ' . TimeHelper::formatDuration($totalHours),
                        'Rata-rata/Hari: ' . TimeHelper::formatDuration($averageHours),
                        'Staf Aktif: ' . $activeStaff
                    ]);

                    // Write staff details header
                    fputcsv($file, ['', 'DETAIL PER STAF:']);
                    fputcsv($file, ['Nama Staf', 'Total Hari', 'Total Jam', 'Rata-rata/Hari']);

                    // Write each staff member's data
                    foreach ($byUser as $userId => $userAttendances) {
                        $user = $userAttendances->first()->user;
                        $userTotalHours = $userAttendances->sum(function ($item) {
                            return $item->session_duration ?? ($item->total_hours ?? 0) * 60;
                        });
                        $userTotalDays = $userAttendances->count();
                        $userAverageHours = $userTotalDays > 0 ? $userTotalHours / $userTotalDays : 0;

                        fputcsv($file, [
                            $user?->name ?? 'User #' . $userId,
                            $userTotalDays,
                            TimeHelper::formatDuration($userTotalHours),
                            TimeHelper::formatDuration($userAverageHours)
                        ]);
                    }

                    // Add separator between months
                    fputcsv($file, []);
                }
            }

            // Summary
            fputcsv($file, []);
            fputcsv($file, ['RINGKASAN']);
            fputcsv($file, ['Total Hari Kerja', $summary['total_days']]);
            fputcsv($file, ['Total Jam Kerja', TimeHelper::formatDuration($summary['total_hours'])]);
            fputcsv($file, ['Rata-rata Jam/Hari', TimeHelper::formatDuration($summary['average_hours'])]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get attendance statistics for dashboard
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, year

        $query = Attendance::with('user');

        switch ($period) {
            case 'week':
                $query->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('work_date', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'year':
                $query->whereBetween('work_date', [now()->startOfYear(), now()->endOfYear()]);
                break;
        }

        $attendances = $query->get();

        $stats = [
            'total_days' => $attendances->count(),
            'total_hours' => $attendances->sum('session_duration'),
            'average_hours' => $attendances->count() > 0 ? $attendances->sum('session_duration') / $attendances->count() : 0,
            'active_users' => $attendances->pluck('user_id')->unique()->count(),
            'period' => $period
        ];

        return response()->json($stats);
    }

    /**
     * Force check out for attendance
     */
    public function forceCheckOut(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id'
        ]);

        try {
            $attendance = Attendance::with('user')->findOrFail($request->attendance_id);

            // Check if attendance already has clock out
            if ($attendance->clock_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi ini sudah memiliki clock out.'
                ], 400);
            }

            // Check if attendance is active
            if (!$attendance->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi ini tidak aktif.'
                ], 400);
            }

            // Force close session
            DB::beginTransaction();

            try {
                // Reload attendance to ensure we have latest data
                $attendance->refresh();

                $result = $attendance->closeSession();

                if (!$result) {
                    DB::rollBack();
                    \Log::error('Force check out failed: closeSession returned false', [
                        'attendance_id' => $attendance->id,
                        'user_id' => $attendance->user_id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal melakukan force check out. Session mungkin sudah ditutup atau terjadi kesalahan.'
                    ], 500);
                }

                // Add note about force check out
                $currentNotes = trim($attendance->notes ?? '');
                $forceNote = sprintf(
                    "\n[FORCE CHECK OUT oleh %s pada %s]",
                    auth()->user()->name,
                    now('Asia/Jakarta')->format('Y-m-d H:i:s')
                );
                $attendance->notes = $currentNotes ? $currentNotes . $forceNote : trim($forceNote);
                $attendance->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Force check out failed with exception', [
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat melakukan force check out: ' . $e->getMessage()
                ], 500);
            }

            // Reload attendance to get latest data after save
            $attendance->refresh();

            \Log::info('Force check out successful', [
                'attendance_id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'forced_by' => auth()->id(),
                'clock_in' => $attendance->clock_in ? $attendance->clock_in->toDateTimeString() : null,
                'clock_out' => $attendance->clock_out ? $attendance->clock_out->toDateTimeString() : null,
                'duration_seconds' => $attendance->session_duration,
                'scheduled_duty_minutes' => $attendance->scheduled_duty_minutes,
                'is_active' => $attendance->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Force check out berhasil dilakukan.',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'clock_out' => $attendance->clock_out->format('Y-m-d H:i:s'),
                    'duration' => $attendance->getFormattedDuration()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Force check out failed', [
                'attendance_id' => $request->attendance_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan force check out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store manual attendance entry by admin
     */
    public function storeManualAttendance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_date' => 'required|date',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
            'session_type' => 'nullable|in:work,study,overtime',
            'notes' => 'nullable|string|max:500'
        ], [
            'user_id.required' => 'Staff harus dipilih',
            'user_id.exists' => 'Staff tidak ditemukan',
            'work_date.required' => 'Tanggal harus diisi',
            'work_date.date' => 'Format tanggal tidak valid',
            'clock_in_time.required' => 'Waktu mulai harus diisi',
            'clock_in_time.date_format' => 'Format waktu mulai harus HH:MM (contoh: 08:00)',
            'clock_out_time.required' => 'Waktu berakhir harus diisi',
            'clock_out_time.date_format' => 'Format waktu berakhir harus HH:MM (contoh: 17:00)',
            'session_type.in' => 'Tipe sesi harus salah satu dari: work, study, overtime'
        ]);

        try {
            DB::beginTransaction();

            // Parse date and times
            $workDate = Carbon::parse($request->work_date);
            $clockIn = Carbon::parse($request->work_date . ' ' . $request->clock_in_time, 'Asia/Jakarta');
            $clockOut = Carbon::parse($request->work_date . ' ' . $request->clock_out_time, 'Asia/Jakarta');

            // Validate: clock_out must be after clock_in
            if ($clockOut->lte($clockIn)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu berakhir harus setelah waktu mulai'
                ], 422);
            }

            // Handle cross-day scenario (e.g., 23:00 - 02:00)
            if ($clockOut->lt($clockIn)) {
                $clockOut->addDay();
            }

            // Calculate duration in seconds
            $durationSeconds = $clockIn->diffInSeconds($clockOut);
            $durationMinutes = floor($durationSeconds / 60);

            // Check for overlapping sessions
            $overlappingSession = Attendance::where('user_id', $request->user_id)
                ->where('work_date', $workDate->format('Y-m-d'))
                ->where(function ($query) use ($clockIn, $clockOut) {
                    // Check if new session overlaps with existing sessions
                    $query->where(function ($q) use ($clockIn, $clockOut) {
                        // New session starts during existing session
                        $q->where('clock_in', '<=', $clockIn)
                            ->where('clock_out', '>', $clockIn);
                    })->orWhere(function ($q) use ($clockIn, $clockOut) {
                        // New session ends during existing session
                        $q->where('clock_in', '<', $clockOut)
                            ->where('clock_out', '>=', $clockOut);
                    })->orWhere(function ($q) use ($clockIn, $clockOut) {
                        // New session contains existing session
                        $q->where('clock_in', '>=', $clockIn)
                            ->where('clock_out', '<=', $clockOut);
                    })->orWhere(function ($q) use ($clockIn, $clockOut) {
                        // Existing session contains new session
                        $q->where('clock_in', '<=', $clockIn)
                            ->where('clock_out', '>=', $clockOut);
                    });
                })
                ->first();

            if ($overlappingSession) {
                $existingIn = $overlappingSession->clock_in->setTimezone('Asia/Jakarta')->format('H:i');
                $existingOut = $overlappingSession->clock_out ? $overlappingSession->clock_out->setTimezone('Asia/Jakarta')->format('H:i') : 'belum clock out';

                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Waktu overlap dengan sesi yang sudah ada ({$existingIn} - {$existingOut}). Pilih waktu yang berbeda."
                ], 422);
            }

            // Get next session number for this date
            $sessionNumber = Attendance::getNextSessionNumber($request->user_id, $workDate);

            // Create note with admin information
            $adminNote = sprintf(
                "[Manual Entry by Admin: %s at %s]",
                auth()->user()->name,
                now('Asia/Jakarta')->format('Y-m-d H:i:s')
            );

            $finalNotes = trim($request->notes ?? '');
            if ($finalNotes) {
                $finalNotes = $adminNote . "\n" . $finalNotes;
            } else {
                $finalNotes = $adminNote;
            }

            // Create attendance record
            $attendance = Attendance::create([
                'user_id' => $request->user_id,
                'work_date' => $workDate,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'session_duration' => $durationSeconds,
                'total_hours' => max(1, $durationMinutes), // Backward compatibility
                'session_number' => $sessionNumber,
                'session_type' => $request->session_type ?? 'work',
                'is_active' => false, // Already completed
                'notes' => $finalNotes
            ]);

            DB::commit();

            \Log::info('Manual attendance created by admin', [
                'attendance_id' => $attendance->id,
                'user_id' => $request->user_id,
                'work_date' => $workDate->format('Y-m-d'),
                'duration_seconds' => $durationSeconds,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jam kerja berhasil ditambahkan',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'work_date' => $workDate->format('d/m/Y'),
                    'clock_in' => $clockIn->format('H:i'),
                    'clock_out' => $clockOut->format('H:i'),
                    'duration' => $attendance->getFormattedDuration()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to create manual attendance', [
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan jam kerja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing attendance record
     */
    public function updateAttendance(Request $request, $id)
    {
        $request->validate([
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
        ], [
            'clock_in_time.required' => 'Waktu mulai harus diisi',
            'clock_in_time.date_format' => 'Format waktu mulai harus HH:MM',
            'clock_out_time.required' => 'Waktu berakhir harus diisi',
            'clock_out_time.date_format' => 'Format waktu berakhir harus HH:MM',
        ]);

        try {
            $attendance = Attendance::findOrFail($id);

            DB::beginTransaction();

            // Parse times with the original work_date
            $workDate = $attendance->work_date;
            $clockIn = Carbon::parse($workDate->format('Y-m-d') . ' ' . $request->clock_in_time, 'Asia/Jakarta');
            $clockOut = Carbon::parse($workDate->format('Y-m-d') . ' ' . $request->clock_out_time, 'Asia/Jakarta');

            // Handle cross-day scenario
            if ($clockOut->lte($clockIn)) {
                $clockOut->addDay();
            }

            // Calculate new duration
            $durationSeconds = $clockIn->diffInSeconds($clockOut);
            $durationMinutes = floor($durationSeconds / 60);

            // Add edit note
            $editNote = sprintf(
                "\n[Diedit oleh Admin: %s pada %s - Durasi diubah]",
                auth()->user()->name,
                now('Asia/Jakarta')->format('Y-m-d H:i:s')
            );

            // Update attendance
            $attendance->update([
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'session_duration' => $durationSeconds,
                'total_hours' => $durationMinutes,
                'notes' => ($attendance->notes ?? '') . $editNote
            ]);

            DB::commit();

            \Log::info('Attendance updated by admin', [
                'attendance_id' => $id,
                'old_duration' => $attendance->getOriginal('session_duration'),
                'new_duration' => $durationSeconds,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jam kerja berhasil diubah',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'clock_in' => $clockIn->format('H:i'),
                    'clock_out' => $clockOut->format('H:i'),
                    'duration' => $attendance->fresh()->getFormattedDuration()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to update attendance', [
                'attendance_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an attendance record
     */
    public function deleteAttendance($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);

            \Log::info('Attendance deleted by admin', [
                'attendance_id' => $id,
                'user_id' => $attendance->user_id,
                'work_date' => $attendance->work_date,
                'duration' => $attendance->session_duration,
                'deleted_by' => auth()->id()
            ]);

            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data absensi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to delete attendance', [
                'attendance_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

