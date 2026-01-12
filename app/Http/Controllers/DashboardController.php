<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalForm;
use App\Models\Attendance;
use App\Models\User;
use App\Helpers\AttendanceHeatmapHelper;
use App\Helpers\AttendanceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin bisa melihat semua, staff biasa hanya melihat rumah sakit mereka
        $isAdmin = $user->isAdmin();
        $userHospital = $isAdmin ? null : $user->getHospital();

        // Get weekly leaderboard data - filter berdasarkan rumah sakit
        $leaderboard = $this->getWeeklyLeaderboard($isAdmin, $userHospital);

        // Appointment types definition
        $appointmentTypes = [
            'penyakit_dalam',
            'spesialis_anak',
            'spesialis_bedah',
            'spesialis_mata',
            'spesialis_saraf',
            'spesialis_urologi',
            'spesialis_tht',
            'spesialis_ortopedi',
            'janji_temu'
        ];

        // Get recent forms query
        $recentFormsQuery = MedicalForm::with('processedBy');

        // Get recent appointments query
        $recentAppointmentsQuery = MedicalForm::with('processedBy');

        // Get stats query
        $statsQuery = MedicalForm::query();

        // user role filtering
        $userRole = $user->role->name ?? '';

        // Role-based filtering logic
        if ($isAdmin) {
            // Admin: Standard View (All Forms split from Appointments)
            $recentFormsQuery->whereNotIn('form_type', $appointmentTypes);
            $recentAppointmentsQuery->whereIn('form_type', $appointmentTypes);
            // Stats: All included

        } elseif ($userRole === 'trainee') {
            // Trainee: Sees NOTHING (Only Clock In/Out & Leaderboard)
            $recentFormsQuery->whereRaw('1 = 0');
            $recentAppointmentsQuery->whereRaw('1 = 0');
            $statsQuery->whereRaw('1 = 0');

        } elseif ($userRole === 'co_ass') {
            // Co-Ass: Only 3 form types, NO appointments
            $allowedForms = ['surat_kesehatan', 'tes_psikologi', 'surat_psikolog'];

            $recentFormsQuery->whereIn('form_type', $allowedForms);

            // Co-Ass sees no appointments
            $recentAppointmentsQuery->whereRaw('1 = 0'); // Force empty

            $statsQuery->whereIn('form_type', $allowedForms);

        } elseif (in_array($userRole, ['dokter_umum', 'dokter_spesialis'])) {
            // Doctor: Only Operasi Plastik in "Forms", but SHOW appointments in "Appointments"

            // 1. Recent Forms: Only 'operasi_plastik'
            $recentFormsQuery->where('form_type', 'operasi_plastik');

            // 2. Appointments: show all appointment types
            $recentAppointmentsQuery->whereIn('form_type', $appointmentTypes);

            // 3. Stats: Include Operasi Plastik AND Appointments
            $statsAllowed = array_merge(['operasi_plastik'], $appointmentTypes);
            $statsQuery->whereIn('form_type', $statsAllowed);

        } else {
            // Others (Perawat, Staff Manager, etc.):
            // Forms: Standard (Non-appointments)
            // Appointments: HIDDEN (User request: "janji temu cuman muncul di dokter")

            $recentFormsQuery->whereNotIn('form_type', $appointmentTypes);
            $recentAppointmentsQuery->whereRaw('1 = 0'); // Force empty
            $statsQuery->whereNotIn('form_type', $appointmentTypes);
        }

        // Apply Hospital Filter (if not admin)
        if (!$isAdmin) {
            $recentFormsQuery->where('hospital', $userHospital);
            $recentAppointmentsQuery->where('hospital', $userHospital);
            $statsQuery->where('hospital', $userHospital);
        }

        // Execute Queries
        $recentForms = $recentFormsQuery->orderBy('created_at', 'desc')->limit(5)->get();
        $recentAppointments = $recentAppointmentsQuery->orderBy('created_at', 'desc')->limit(5)->get();

        $stats = [
            'pending_forms' => (clone $statsQuery)->pending()->count(),
            'approved_forms' => (clone $statsQuery)->approved()->count(),
            'rejected_forms' => (clone $statsQuery)->rejected()->count(),
            'total_forms_today' => (clone $statsQuery)->whereDate('created_at', today())->count(),
        ];

        // Get user's attendance sessions for today
        // Only include sessions that actually started today (work_date = today)
        $todaySessions = Attendance::getTodaySessions($user->id);

        $activeSession = Attendance::getActiveSession($user->id, today());
        $anyActiveSession = Attendance::getAnyActiveSession($user->id);
        $todayTotalHours = Attendance::getDailyTotalHours($user->id, today());

        // Get attendance heatmap data
        $year = request('year', now()->year);
        $heatmapData = AttendanceHeatmapHelper::generateHeatmapData($user->id, $year);
        $heatmapStats = AttendanceHeatmapHelper::getStatistics($heatmapData);

        // Get weekly and accumulated hours for summary card
        $weeklyStats = AttendanceHelper::getUserWeeklyStats($user->id);
        $totalEmsHours = AttendanceHelper::getUserTotalHours($user->id);

        // If current user has no attendance data, show demo data or find a user with data
        if ($heatmapData['work_days'] == 0) {
            $userWithData = User::whereHas('attendances')->first();
            if ($userWithData) {
                $heatmapData = AttendanceHeatmapHelper::generateHeatmapData($userWithData->id, $year);
                $heatmapStats = AttendanceHeatmapHelper::getStatistics($heatmapData);
            }
        }

        return view('staff.dashboard', compact(
            'leaderboard',
            'recentForms',
            'recentAppointments',
            'stats',
            'todaySessions',
            'activeSession',
            'anyActiveSession',
            'todayTotalHours',
            'heatmapData',
            'heatmapStats',
            'weeklyStats',
            'totalEmsHours'
        ));
    }

    public function forms(Request $request)
    {
        $user = Auth::user();

        // Admin bisa melihat semua, staff biasa hanya melihat rumah sakit mereka
        $isAdmin = $user->isAdmin();
        $userHospital = $isAdmin ? null : $user->getHospital();

        // Ambil parameter filter
        $search = $request->get('search');
        $status = $request->get('status');
        $type = $request->get('type');
        $category = $request->get('category');

        // Appointment types definition
        $appointmentTypes = [
            'penyakit_dalam',
            'spesialis_anak',
            'spesialis_bedah',
            'spesialis_mata',
            'spesialis_saraf',
            'spesialis_urologi',
            'spesialis_tht',
            'spesialis_ortopedi',
            'janji_temu'
        ];

        // Query dasar - admin melihat semua, staff hanya rumah sakit mereka
        $query = MedicalForm::with('processedBy');

        if (!$isAdmin) {
            $query->where('hospital', $userHospital);
        }

        // ROLE-BASED FILTERING (Mirrors DashboardController::index)
        $userRole = $user->role->name ?? '';

        if ($isAdmin) {
            // Admin sees all, no filter needed here
        } elseif ($userRole === 'trainee') {
            // Trainee: Sees NOTHING
            $query->whereRaw('1 = 0');
        } elseif ($userRole === 'co_ass') {
            // Co-Ass: Only 3 form types, NO appointments
            $allowedForms = ['surat_kesehatan', 'tes_psikologi', 'surat_psikolog'];
            $query->whereIn('form_type', $allowedForms);

        } elseif (in_array($userRole, ['dokter_umum', 'dokter_spesialis'])) {
            // Doctor: "Operasi Plastik" AND Appointments
            $allowedForms = array_merge(['operasi_plastik'], $appointmentTypes);
            $query->whereIn('form_type', $allowedForms);

        } else {
            // Others (Perawat, etc.): Standard forms, NO Appointments
            $query->whereNotIn('form_type', $appointmentTypes);
        }

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('character_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status && $status !== '') {
            $query->where('status', $status);
        }

        // Filter jenis form
        if ($type && $type !== '') {
            if ($type === 'janji_temu') {
                // Filter untuk semua jenis janji temu
                $query->whereIn('form_type', ['penyakit_dalam', 'spesialis_anak', 'spesialis_bedah', 'spesialis_mata', 'spesialis_saraf', 'spesialis_urologi', 'spesialis_tht', 'spesialis_ortopedi']);
            } else {
                $query->where('form_type', $type);
            }
        }

        // Filter kategori
        if ($category && $category !== '') {
            if ($category === 'janji_temu') {
                $query->whereIn('form_type', ['penyakit_dalam', 'spesialis_anak', 'spesialis_bedah', 'spesialis_mata', 'spesialis_saraf', 'spesialis_urologi', 'spesialis_tht', 'spesialis_ortopedi']);
            } elseif ($category === 'konsultasi') {
                $query->whereIn('form_type', ['konsultasi_medis', 'laporan_kecelakaan', 'permintaan_ambulans']);
            } elseif ($category === 'pemeriksaan') {
                $query->whereIn('form_type', ['surat_kesehatan', 'operasi_plastik', 'tes_psikologi', 'surat_psikolog']);
            } elseif ($category === 'karakter_kill') {
                $query->where('form_type', 'pendaftaran_karakter');
            }
        }

        // Ambil data dengan paginasi
        $forms = $query->orderBy('created_at', 'desc')->paginate(20);

        // Hitung statistik secara terpisah untuk efisiensi
        $statsQuery = MedicalForm::query();
        if (!$isAdmin) {
            $statsQuery->where('hospital', $userHospital);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
            'today' => (clone $statsQuery)->whereDate('created_at', today())->count(),
        ];

        $user = Auth::user()->load('role');

        return view('staff.forms', compact('forms', 'stats', 'user'));
    }

    public function formDetail($id)
    {
        $user = Auth::user()->load('role');

        // Admin bisa melihat semua, staff biasa hanya melihat rumah sakit mereka
        $isAdmin = $user->isAdmin();

        $form = MedicalForm::with('processedBy');

        if (!$isAdmin) {
            $userHospital = $user->getHospital();
            $form->where('hospital', $userHospital);
        }

        $form = $form->findOrFail($id);

        return view('staff.form-detail', compact('form', 'user'));
    }

    public function approveForm(Request $request, $id)
    {
        $user = Auth::user()->load('role');

        // Admin bisa approve semua, staff biasa hanya approve rumah sakit mereka
        $isAdmin = $user->isAdmin();

        $form = MedicalForm::query();

        if (!$isAdmin) {
            $userHospital = $user->getHospital();
            $form->where('hospital', $userHospital);
        }

        $form = $form->findOrFail($id);

        // Validasi level role berdasarkan jenis form
        $userLevel = $user->role->level ?? 0;
        $formType = $form->form_type;

        // Surat kesehatan dan surat psikolog: minimal Co-ass (level 2) ke atas
        if (in_array($formType, ['surat_kesehatan', 'tes_psikologi', 'surat_psikolog'])) {
            if ($userLevel < 2) {
                return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui formulir ini. Minimal level Co-ass (level 2) diperlukan untuk surat kesehatan dan surat psikolog.');
            }
        }

        // Surat keterangan oplas (operasi plastik): minimal dokter umum (level 3) ke atas
        if ($formType === 'operasi_plastik') {
            if ($userLevel < 3) {
                return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui formulir ini. Minimal level Dokter Umum (level 3) diperlukan untuk surat keterangan operasi plastik.');
            }
        }

        $form->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Formulir berhasil disetujui.');
    }

    public function rejectForm(Request $request, $id)
    {
        $user = Auth::user();

        // Admin bisa reject semua, staff biasa hanya reject rumah sakit mereka
        $isAdmin = $user->isAdmin();

        $form = MedicalForm::query();

        if (!$isAdmin) {
            $userHospital = $user->getHospital();
            $form->where('hospital', $userHospital);
        }

        $form = $form->findOrFail($id);

        $form->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Formulir berhasil ditolak.');
    }

    public function approveTestimoni(Request $request, $id)
    {
        $form = MedicalForm::findOrFail($id);

        if (!$form->testimoni) {
            return back()->with('error', 'Formulir ini tidak memiliki testimoni.');
        }

        $form->update([
            'testimoni_approved' => true,
        ]);

        return back()->with('success', 'Testimoni berhasil disetujui dan akan ditampilkan di halaman beranda.');
    }

    public function attendance()
    {
        $user = Auth::user();

        // Get user's attendance history (grouped by date)
        $attendanceHistory = Attendance::where('user_id', $user->id)
            ->orderBy('work_date', 'desc')
            ->orderBy('session_number')
            ->paginate(20);

        // Get today's sessions (only sessions that started today)
        $todaySessions = Attendance::getTodaySessions($user->id);

        $activeSession = Attendance::getActiveSession($user->id, today());
        $anyActiveSession = Attendance::getAnyActiveSession($user->id);
        $todayTotalHours = Attendance::getDailyTotalHours($user->id, today());

        // Get weekly stats
        $weeklyStats = $this->getUserWeeklyStats($user->id);

        return view('staff.attendance', compact('attendanceHistory', 'todaySessions', 'activeSession', 'anyActiveSession', 'todayTotalHours', 'weeklyStats'));
    }

    public function clockIn(Request $request)
    {
        // Validate input
        $request->validate([
            'session_type' => 'nullable|string|in:work,break,meeting,overtime',
            'notes' => 'nullable|string|max:1000',
            'scheduled_duty_minutes' => 'required|integer|min:1|max:300' // Required: 1 min to 300 minutes (5 hours)
        ], [
            'scheduled_duty_minutes.max' => 'Waktu clock in tidak boleh lebih dari 300 menit (5 jam).',
            'scheduled_duty_minutes.min' => 'Waktu clock in minimal 1 menit.',
            'scheduled_duty_minutes.required' => 'Waktu clock in wajib diisi.',
            'scheduled_duty_minutes.integer' => 'Waktu clock in harus berupa angka.',
        ]);

        // Additional validation: Ensure it's not more than 300 minutes
        $scheduledMinutes = (int) $request->scheduled_duty_minutes;
        if ($scheduledMinutes > 300) {
            return back()->with('error', 'Waktu clock in tidak boleh lebih dari 300 menit (5 jam).')->withInput();
        }

        $user = Auth::user();

        // Use database transaction for data consistency
        DB::beginTransaction();

        try {
            // Cek apakah ada sesi aktif dari hari ini atau hari-hari sebelumnya.
            // Sesuai keinginan Anda, sesi harus berjalan terus sampai di-clock out secara manual.
            // Oleh karena itu, pengguna tidak boleh bisa clock in lagi jika masih ada sesi yang aktif.
            $anyActiveSession = Attendance::getAnyActiveSession($user->id);
            if ($anyActiveSession) {
                DB::rollBack();

                // Build informative error message
                $clockInDate = $anyActiveSession->clock_in->format('d/m/Y H:i');
                $workDate = $anyActiveSession->work_date->format('d/m/Y');
                $currentDuration = $anyActiveSession->getFormattedDuration();
                $isCrossDayActive = $anyActiveSession->clock_in->toDateString() !== Carbon::today('Asia/Jakarta')->toDateString();

                $errorMessage = sprintf(
                    'Anda masih memiliki sesi aktif yang belum di-clock out.%s Clock In: %s (Work Date: %s). Durasi saat ini: %s. Silakan clock out terlebih dahulu.',
                    $isCrossDayActive ? ' [CROSS-DAY SESSION!]' : '',
                    $clockInDate,
                    $workDate,
                    $currentDuration
                );

                return back()->with('error', $errorMessage);
            }

            // Get current time once to ensure consistency
            $currentTime = Carbon::now('Asia/Jakarta');
            $currentDate = Carbon::today('Asia/Jakarta');

            // Get next session number for today
            $sessionNumber = Attendance::getNextSessionNumber($user->id, $currentDate);
            $sessionType = $request->session_type ?? 'work';

            // Prepare attendance data
            $attendanceData = [
                'user_id' => $user->id,
                'clock_in' => $currentTime,
                'work_date' => $currentDate,
                'notes' => $request->notes,
                'session_number' => $sessionNumber,
                'session_type' => $sessionType,
                'is_active' => true
            ];

            // Handle scheduled duty minutes (required timer feature)
            $scheduledMinutes = (int) $request->scheduled_duty_minutes;
            $scheduledEndTime = $currentTime->copy()->addMinutes($scheduledMinutes);

            $attendanceData['scheduled_duty_minutes'] = $scheduledMinutes;
            $attendanceData['scheduled_end_time'] = $scheduledEndTime;

            $attendance = Attendance::create($attendanceData);

            // Update user status based on session type
            // If meeting, set status to 'meeting', otherwise set to 'working'
            $userStatus = ($sessionType === 'meeting') ? 'meeting' : 'working';
            $user->update(['status' => $userStatus]);

            DB::commit();

            // Log successful clock in
            \Log::info('Clock in successful', [
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'clock_in' => $attendance->clock_in->toDateTimeString(),
                'work_date' => $attendance->work_date->toDateString(),
                'session_type' => $sessionType,
                'session_number' => $sessionNumber
            ]);

            // Build success message with session info
            $message = sprintf(
                'Clock in berhasil pada %s. Sesi #%d (%s) - Work Date: %s',
                $currentTime->format('d/m/Y H:i'),
                $sessionNumber,
                ucfirst($sessionType),
                $currentDate->format('d/m/Y')
            );

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Clock in failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat clock in. Silakan coba lagi.');
        }
    }

    public function clockOut(Request $request)
    {
        // Validate input
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $currentDate = Carbon::today('Asia/Jakarta');

        // First check for active session today
        $attendance = Attendance::getActiveSession($user->id, $currentDate);

        // If no active session today, check for any active session (from previous days)
        if (!$attendance) {
            $attendance = Attendance::getAnyActiveSession($user->id);
        }

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan clock in.');
        }

        // Use database transaction for data consistency
        DB::beginTransaction();

        try {
            // Update notes if provided (before closing session)
            if ($request->filled('notes')) {
                $currentNotes = trim($attendance->notes ?? '');
                $newNotes = trim($request->notes);
                $attendance->notes = $currentNotes ? $currentNotes . "\n" . $newNotes : $newNotes;
                $attendance->save();
            }

            // Close the session (this will set clock_out and calculate duration)
            $closeResult = $attendance->closeSession();

            if (!$closeResult) {
                throw new \Exception('Failed to close session');
            }

            // Reset user status to 'working' (default) when clocking out
            $user->update(['status' => 'working']);

            DB::commit();

            // Get session info for success message
            $isCrossDay = $attendance->isCrossDay();
            $duration = $attendance->getFormattedDuration();
            $durationHours = $attendance->getDurationInHours();

            // Log successful clock out
            \Log::info('Clock out successful', [
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'clock_in' => $attendance->clock_in->toDateTimeString(),
                'clock_out' => $attendance->clock_out->toDateTimeString(),
                'duration_seconds' => $attendance->session_duration,
                'duration_formatted' => $duration,
                'is_cross_day' => $isCrossDay
            ]);

            // Build success message
            $message = sprintf(
                'Clock out berhasil. Durasi kerja: %s (%.2f jam)%s',
                $duration,
                $durationHours,
                $isCrossDay ? ' - Cross-Day Session ✓' : ''
            );

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Clock out failed', [
                'attendance_id' => $attendance->id ?? null,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat clock out: ' . $e->getMessage());
        }
    }

    public function reports()
    {
        $user = Auth::user();

        // Admin bisa melihat semua, staff biasa hanya melihat rumah sakit mereka
        $isAdmin = $user->isAdmin();
        $userHospital = $isAdmin ? null : $user->getHospital();

        // Get weekly leaderboard data for charts - filter berdasarkan rumah sakit
        $leaderboardData = $this->getWeeklyLeaderboard($isAdmin, $userHospital);

        // Get form statistics
        $formStats = [
            'daily' => MedicalForm::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'by_type' => MedicalForm::selectRaw('form_type, COUNT(*) as count')
                ->groupBy('form_type')
                ->get(),
            'by_status' => MedicalForm::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
        ];

        return view('staff.reports', compact('leaderboardData', 'formStats'));
    }

    private function getWeeklyLeaderboard($isAdmin = false, $userHospital = null)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // Query dasar untuk user yang punya attendance
        $usersQuery = User::whereHas('attendances', function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                ->whereIn('session_type', ['work', 'meeting'])
                ->whereNotNull('session_duration')
                ->where('session_duration', '>', 0);
        });

        // Filter berdasarkan rumah sakit jika bukan admin
        if (!$isAdmin && $userHospital) {
            // Filter user berdasarkan rumah sakit mereka
            // Alta: user yang bukan Roxwood
            // Roxwood: user yang merupakan Roxwood
            if ($userHospital === 'alta') {
                // Exclude user Roxwood (yang namanya mengandung RH, roxwood, dll)
                $usersQuery->where(function ($q) {
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
                // Roxwood: user yang namanya mengandung RH, roxwood, dll
                $usersQuery->where(function ($q) {
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

        $users = $usersQuery
            ->withCount([
                'attendances' => function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                        ->whereIn('session_type', ['work', 'meeting'])
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
            ->limit(10)
            ->get();

        // Add unique work days count and total juara 1 count for each user
        foreach ($users as $user) {
            $uniqueWorkDays = Attendance::where('user_id', $user->id)
                ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                ->whereIn('session_type', ['work', 'meeting'])
                ->whereNotNull('session_duration')
                ->where('session_duration', '>', 0)
                ->distinct('work_date')
                ->count('work_date');

            $user->unique_work_days = $uniqueWorkDays;

            // Get total juara 1 count (with hospital filter if applicable)
            $user->total_juara_1_count = \App\Helpers\AttendanceHelper::getTotalJuara1Count($user->id, $userHospital);
        }

        return $users;
    }

    private function getUserWeeklyStats($userId)
    {
        return Attendance::where('user_id', $userId)
            ->whereBetween('work_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('session_type', ['work', 'meeting'])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->selectRaw('
                COUNT(DISTINCT work_date) as total_days,
                SUM(session_duration) as total_hours,
                AVG(session_duration) as avg_hours_per_day
            ')
            ->first();
    }
}
