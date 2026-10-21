<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\Admin\StaffManagementController;

// REAL-TIME: Heartbeat — update last_seen_at akun yang sedang aktif
Route::post('/ping-online', function () {
    try {
        if (auth()->check()) {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', auth()->id())
                ->update(['last_seen_at' => now()]);
        }
        return response()->json(['ok' => true]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false]);
    }
})->middleware(['web']);

// REAL-TIME: API daftar user ID yang sedang online (untuk polling JS)
Route::get('/api-online-users', function () {
    try {
        $threshold = now()->subMinutes(5);

        $onlineByActivity = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotNull('role_id')
            ->where('last_seen_at', '>=', $threshold)
            ->pluck('id')
            ->toArray();

        $onlineByClock = \Illuminate\Support\Facades\DB::table('attendances')
            ->whereNull('clock_out')
            ->pluck('user_id')
            ->toArray();

        $allOnline = array_values(array_unique(array_merge($onlineByActivity, $onlineByClock)));

        return response()->json(['online_ids' => $allOnline]);
    } catch (\Throwable $e) {
        return response()->json(['online_ids' => []]);
    }
})->middleware(['web']);



// New Modern Auth Portal Route
Route::get('/portal-auth', function () {
    $allowedRoles = ['trainee', 'perawat', 'co_ass', 'dokter_umum', 'dokter_spesialis'];
    $roles = \App\Models\StaffRole::whereIn('name', $allowedRoles)->orderBy('level', 'asc')->get();
    return view('auth.portal', compact('roles'));
})->name('portal.auth');

// Default login route (redirect to staff login)
Route::get('/login', function () {
    return redirect()->route('staff.login');
})->name('login');



// CSRF Token refresh endpoint
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.token');

// Scheduler trigger endpoint (untuk auto checkout duty timer)
// Bisa dipanggil oleh external cron service atau webhook
// IMPORTANT: Endpoint ini bisa dipanggil tanpa authentication untuk memastikan auto checkout berjalan
Route::get('/cron/check-expired-sessions', function () {
    try {
        $token = request()->get('token') ?? request()->header('X-Scheduler-Token');
        $expectedToken = config('app.scheduler_token') ?? env('SCHEDULER_TOKEN');

        // Reject if token is missing or incorrect
        if (empty($expectedToken) || !hash_equals((string)$expectedToken, (string)$token)) {
            return response()->json(['error' => 'Unauthorized: Invalid scheduler token'], 401);
        }

        \Artisan::call('attendance:check-expired-sessions');
        $output = \Artisan::output();

        \Log::info('Scheduler triggered via web endpoint', [
            'output' => $output,
            'timestamp' => now()->toDateTimeString(),
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Command executed successfully',
            'output' => $output,
            'timestamp' => now()->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        \Log::error('Scheduler trigger failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Command execution failed',
            'error' => $e->getMessage()
        ], 500);
    }
})->name('cron.check-expired-sessions');

// Public routes
Route::get('/', [PublicController::class, 'index'])->name('public.index');

// Recruitment Paramedic Alta Hospital (Public Flow)
Route::get('/recruitment', [\App\Http\Controllers\Public\RecruitmentController::class, 'index'])->name('public.recruitment');
Route::get('/recruitment/status', [\App\Http\Controllers\Public\RecruitmentController::class, 'statusCheck'])->name('public.recruitment.status');
Route::post('/recruitment/set-credentials', [\App\Http\Controllers\Public\RecruitmentController::class, 'setCredentials'])->name('public.recruitment.set-credentials');
Route::post('/recruitment/submit', [\App\Http\Controllers\Public\RecruitmentController::class, 'store'])->name('public.recruitment.submit');
Route::get('/recruitment/success/{id}', [\App\Http\Controllers\Public\RecruitmentController::class, 'success'])->name('public.recruitment.success');
Route::get('/pendaftaran-medis', function () {
    return redirect()->route('public.recruitment');
})->name('public.pendaftaran-medis');

Route::get('/form/{type?}', [PublicController::class, 'showForm'])->name('public.form');
Route::post('/form/submit', [PublicController::class, 'submitForm'])->name('public.form.submit');
Route::post('/appointment/create', [PublicController::class, 'createAppointment'])->name('public.appointment.create');
Route::get('/appointment/success/{id}', [PublicController::class, 'appointmentSuccess'])->name('public.appointment.success');

// Public Feedback / Keluhan & Pengaduan Warga Routes
Route::get('/feedback', [PublicController::class, 'showFeedbackForm'])->name('feedback.form');
Route::post('/feedback', [PublicController::class, 'submitFeedback'])->name('feedback.submit');
Route::get('/feedback/success', [PublicController::class, 'feedbackSuccess'])->name('feedback.success');
Route::get('/keluhan', [PublicController::class, 'showFeedbackForm'])->name('public.keluhan');

// User Chat & Stickers Routes (Authentication Required)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', function () {
        return view('chat.index');
    })->name('chat.page');

    Route::get('/livechat', function () {
        return view('chat.livechat');
    })->name('chat.livechat');

    // GIPHY & Custom Stickers API Routes
    Route::get('/api/stickers/trending', [\App\Http\Controllers\StickerController::class, 'trending']);
    Route::get('/api/stickers/search', [\App\Http\Controllers\StickerController::class, 'search']);
    Route::get('/api/stickers/categories', [\App\Http\Controllers\StickerController::class, 'categories']);
    Route::get('/api/stickers/packs', [\App\Http\Controllers\StickerController::class, 'packs']);
    Route::get('/api/stickers/favorites', [\App\Http\Controllers\StickerController::class, 'getFavorites']);
    Route::post('/api/stickers/favorites', [\App\Http\Controllers\StickerController::class, 'toggleFavorite']);
    Route::get('/api/stickers/recents', [\App\Http\Controllers\StickerController::class, 'getRecents']);
    Route::get('/api/stickers/{id}', [\App\Http\Controllers\StickerController::class, 'show']);
});

// Form success route (used after submit)
Route::get('/form/success/{id}', [PublicController::class, 'formSuccess'])->name('public.form.success');
Route::post('/form/testimoni/{id}', [PublicController::class, 'submitTestimoni'])->name('public.form.testimoni');
// Struktural EMS route (includes both EMS and Roxwood Hospital tables)
Route::get('/struktural-ems', [PublicController::class, 'strukturalEmsDb'])->name('public.struktural-ems');
// Doctor Practice Schedule route
Route::get('/jadwal-praktek', [PublicController::class, 'doctorSchedule'])->name('public.doctor-schedule');
// WHO Ishihara Color Blindness Test route
Route::get('/tes-buta-warna', [PublicController::class, 'tesButaWarna'])->name('public.tes-buta-warna');
// Shortcut named routes for popular forms
Route::get('/cek-kesehatan', function () {
    return redirect()->route('public.form', ['type' => 'surat_kesehatan']);
})->name('public.cek-kesehatan');
Route::get('/operasi-plastik', function () {
    return redirect()->route('public.form', ['type' => 'operasi_plastik']);
})->name('public.operasi-plastik');
Route::get('/tes-psikologi', function () {
    return redirect()->route('public.form', 'tes_psikologi');
})->name('public.tes-psikologi');
Route::get('/surat-psikolog', function () {
    return redirect()->route('public.form', 'surat_psikolog');
})->name('public.surat-psikolog');
Route::get('/poli-umum', function () {
    return redirect()->route('public.form', ['type' => 'janji_temu', 'poli' => 'Poli Umum']);
})->name('public.poli-umum');
Route::get('/janji-temu', function () {
    return redirect()->route('public.form', ['type' => 'janji_temu']);
})->name('public.janji-temu');
Route::get('/pendaftaran-karakter', function () {
    return redirect()->route('public.form', ['type' => 'pendaftaran_karakter']);
})->name('public.pendaftaran-karakter');
// Sitemap route for SEO
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('public.sitemap');

// AI Settings Shortcut / Redirect to Admin
Route::get('/ai-settings', function () {
    return redirect()->route('admin.ai-settings.index');
});

// Staff routes
// Routes for guests (not logged in) - Displays login/register forms and processes them
Route::middleware(['guest'])->group(function () {
    Route::get('/staff/login', [StaffController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/staff/login', [StaffController::class, 'login'])->name('staff.login.post');
    Route::get('/staff/register', [StaffController::class, 'showRegisterForm'])->name('staff.register');
    Route::post('/staff/register', [StaffController::class, 'register'])->name('staff.register.post');
});

Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/staff/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
    Route::post('/staff/logout', [StaffController::class, 'logout'])->name('staff.logout');

    // AI Chat Assistant (available to all staff)
    Route::post('/staff/ai-chat', [\App\Http\Controllers\Staff\AiChatController::class, 'send'])->name('staff.ai-chat');
    Route::get('/staff/ai-chat/models', [\App\Http\Controllers\Staff\AiChatController::class, 'getModels'])->name('staff.ai-chat.models');

    // Attendance routes
    Route::post('/staff/attendance/clock-in', [DashboardController::class, 'clockIn'])->name('staff.attendance.clock-in');
    Route::post('/staff/attendance/clock-out', [DashboardController::class, 'clockOut'])->name('staff.attendance.clock-out');

    // Profile update
    Route::get('/staff/profile', [StaffController::class, 'showProfile'])->name('staff.profile');
    Route::post('/staff/profile', [StaffController::class, 'updateProfile'])->name('staff.profile.update');
    Route::post('/staff/profile/update-email', [StaffController::class, 'updateEmail'])->name('staff.profile.update-email');

    // Penilaian & Evaluasi Manajer (Anonim)
    Route::get('/staff/manager-evaluations', [\App\Http\Controllers\Staff\ManagerEvaluationController::class, 'index'])->name('staff.manager-evaluations.index');
    Route::post('/staff/manager-evaluations', [\App\Http\Controllers\Staff\ManagerEvaluationController::class, 'store'])->name('staff.manager-evaluations.store');

    // Forms routes (list, detail, approve, reject)
    Route::get('/staff/forms', [DashboardController::class, 'forms'])->name('staff.forms');
    Route::get('/staff/forms/{id}', [DashboardController::class, 'formDetail'])->name('staff.forms.show');
    Route::post('/staff/forms/{id}/approve', [DashboardController::class, 'approveForm'])->name('staff.forms.approve');
    Route::post('/staff/forms/{id}/reject', [DashboardController::class, 'rejectForm'])->name('staff.forms.reject');
    Route::post('/staff/forms/{id}/cancel', [DashboardController::class, 'cancelForm'])->name('staff.forms.cancel');
    Route::post('/staff/forms/{id}/undo', [DashboardController::class, 'undoProcessForm'])->name('staff.forms.undo');
    Route::post('/staff/forms/{id}/testimoni/approve', [DashboardController::class, 'approveTestimoni'])->name('staff.forms.testimoni.approve');

    // Staff payroll routes
    Route::get('/staff/payroll', [\App\Http\Controllers\Staff\PayrollController::class, 'index'])->name('staff.payroll.index');
    Route::get('/staff/payroll/{id}', [\App\Http\Controllers\Staff\PayrollController::class, 'show'])->name('staff.payroll.show')->where('id', '[0-9]+');
    Route::get('/staff/payroll/stats', [\App\Http\Controllers\Staff\PayrollController::class, 'getStats'])->name('staff.payroll.stats');
    Route::post('/staff/payroll/notifications/{notification}/mark-read', [\App\Http\Controllers\Staff\PayrollController::class, 'markNotificationAsRead'])->name('staff.payroll.notifications.mark-read');

    // Wrapped routes (Year in Review)
    Route::get('/wrapped/{year}', [\App\Http\Controllers\WrappedController::class, 'show'])->name('wrapped.show');
    Route::post('/wrapped/dismiss', [\App\Http\Controllers\WrappedController::class, 'dismiss'])->name('wrapped.dismiss');
    Route::post('/wrapped/record', [\App\Http\Controllers\WrappedController::class, 'recordView'])->name('wrapped.record');

    // Meeting Request routes for staff
    Route::get('/staff/meeting-requests', [\App\Http\Controllers\MeetingRequestController::class, 'index'])->name('staff.meeting-requests.index');
    Route::get('/staff/meeting-requests/create', [\App\Http\Controllers\MeetingRequestController::class, 'create'])->name('staff.meeting-requests.create');
    Route::post('/staff/meeting-requests', [\App\Http\Controllers\MeetingRequestController::class, 'store'])->name('staff.meeting-requests.store');

    // Voting routes for staff
    Route::get('/staff/voting', [VotingController::class, 'index'])->name('staff.voting.index');
    Route::get('/staff/voting/{id}', [VotingController::class, 'show'])->name('staff.voting.show');
    Route::post('/staff/voting/{id}/vote', [VotingController::class, 'vote'])->name('staff.voting.vote');

    // Operations routes for staff
    Route::get('/staff/operations', [\App\Http\Controllers\Staff\OperationRecordController::class, 'index'])->name('staff.operations.index');
    Route::get('/staff/operations/create', [\App\Http\Controllers\Staff\OperationRecordController::class, 'create'])->name('staff.operations.create');
    Route::post('/staff/operations', [\App\Http\Controllers\Staff\OperationRecordController::class, 'store'])->name('staff.operations.store');
    Route::get('/staff/operations/{id}/edit', [\App\Http\Controllers\Staff\OperationRecordController::class, 'edit'])->name('staff.operations.edit');
    Route::put('/staff/operations/{id}', [\App\Http\Controllers\Staff\OperationRecordController::class, 'update'])->name('staff.operations.update');
    Route::get('/staff/operations/{id}', [\App\Http\Controllers\Staff\OperationRecordController::class, 'show'])->name('staff.operations.show');
    
    // API endpoint for searching members & patient history
    Route::get('/api/members/search', [\App\Http\Controllers\Staff\OperationRecordController::class, 'searchMembers'])->name('api.members.search');
    Route::get('/api/patient/search', [\App\Http\Controllers\Staff\OperationRecordController::class, 'searchPatient'])->name('api.patient.search');


    // Members Directory & Profiles
    Route::get('/staff/members', [\App\Http\Controllers\Staff\MemberController::class, 'index'])->name('staff.members.index');
    Route::get('/staff/certificates', [\App\Http\Controllers\Staff\MemberController::class, 'certificates'])->name('staff.certificates.index');
    Route::get('/staff/members/{user}', [\App\Http\Controllers\Staff\MemberController::class, 'show'])->name('staff.members.show');

    // Benefit Anggota EMS (Oplas Gratis 1x Sebulan)
    Route::get('/staff/benefits/oplas', [\App\Http\Controllers\Staff\MemberBenefitController::class, 'index'])->name('staff.benefits.oplas');
    Route::post('/staff/benefits/oplas/claim', [\App\Http\Controllers\Staff\MemberBenefitController::class, 'claimOplas'])->name('staff.benefits.oplas.claim');


    // Private Messages (Direct Messaging)
    Route::get('/staff/messages', function () {
        return view('staff.messages.index');
    })->name('staff.messages.index');
});

// Admin routes
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('permission:view_reports')->name('dashboard');

    Route::get('/staff/export', [\App\Http\Controllers\Admin\StaffManagementController::class, 'export'])
        ->middleware('permission:manage_users')->name('staff.export');
    Route::resource('staff', StaffManagementController::class)
        ->parameters(['staff' => 'user'])->middleware('permission:manage_users');
    Route::post('/staff/{user}/toggle-active', [\App\Http\Controllers\Admin\StaffManagementController::class, 'toggleActive'])
        ->middleware('permission:manage_users')->name('staff.toggle-active');
    Route::post('/staff/{user}/reset-password', [\App\Http\Controllers\Admin\StaffManagementController::class, 'resetPassword'])
        ->middleware('permission:manage_users')->name('staff.reset-password');

    // Doctor Schedule management (Dokter Spesialis & level >= 4 ke atas)
    Route::resource('doctor-schedules', \App\Http\Controllers\Admin\DoctorScheduleController::class)
        ->middleware('permission:manage_doctor_schedules|manage_users');
    Route::post('/doctor-schedules/{doctor_schedule}/toggle-active', [\App\Http\Controllers\Admin\DoctorScheduleController::class, 'toggleActive'])
        ->middleware('permission:manage_doctor_schedules|manage_users')->name('doctor-schedules.toggle-active');

    // Admin Operations deletion
    Route::delete('/operations/{id}', [\App\Http\Controllers\Admin\OperationRecordController::class, 'destroy'])
        ->middleware('permission:manage_users')->name('operations.destroy');

    // Admin Sticker Management
    Route::get('/stickers', [\App\Http\Controllers\Admin\StickerManagementController::class, 'index'])->name('stickers.index');
    Route::post('/stickers/pack', [\App\Http\Controllers\Admin\StickerManagementController::class, 'storePack'])->name('stickers.store-pack');
    Route::put('/stickers/pack/{id}', [\App\Http\Controllers\Admin\StickerManagementController::class, 'updatePack'])->name('stickers.update-pack');
    Route::delete('/stickers/pack/{id}', [\App\Http\Controllers\Admin\StickerManagementController::class, 'destroyPack'])->name('stickers.destroy-pack');
    Route::post('/stickers/pack/{id}/upload', [\App\Http\Controllers\Admin\StickerManagementController::class, 'uploadStickers'])->name('stickers.upload');
    Route::delete('/stickers/{id}', [\App\Http\Controllers\Admin\StickerManagementController::class, 'destroySticker'])->name('stickers.destroy-sticker');
    Route::post('/stickers/toggle-giphy', [\App\Http\Controllers\Admin\StickerManagementController::class, 'toggleGiphy'])->name('stickers.toggle-giphy');
    // Route::resource('medical-forms', \App\Http\Controllers\Admin\MedicalFormController::class)->middleware('permission:manage_forms');
    // Route::resource('staff-roles', \App\Http\Controllers\Admin\StaffRoleController::class)->middleware('permission:manage_settings');
    // Attendance reports
    Route::get('/attendance-reports', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])
        ->middleware('permission:view_reports|view_attendance_reports')->name('attendance-reports.index');
    Route::get('/attendance-reports/stats', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'getStats'])
        ->middleware('permission:view_reports|view_attendance_reports')->name('attendance-reports.stats');
    Route::post('/attendance-reports/force-checkout', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'forceCheckOut'])
        ->middleware('permission:force_checkout')->name('attendance-reports.force-checkout');
    Route::post('/attendance-reports/manual', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'storeManualAttendance'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.manual');
    Route::put('/attendance-reports/{id}', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'updateAttendance'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.update');
    Route::delete('/attendance-reports/{id}', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'deleteAttendance'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.delete');

    // Meeting Request routes (Permission-based)
    Route::get('/meeting-requests', [\App\Http\Controllers\MeetingRequestController::class, 'adminIndex'])
        ->middleware('permission:manage_meeting_requests')->name('meeting-requests.index');
    Route::post('/meeting-requests/{id}/approve', [\App\Http\Controllers\MeetingRequestController::class, 'approve'])
        ->middleware('permission:manage_meeting_requests')->name('meeting-requests.approve');
    Route::post('/meeting-requests/{id}/reject', [\App\Http\Controllers\MeetingRequestController::class, 'reject'])
        ->middleware('permission:manage_meeting_requests')->name('meeting-requests.reject');
    Route::post('/meeting-requests/{id}/undo', [\App\Http\Controllers\MeetingRequestController::class, 'undoProcess'])
        ->middleware('permission:manage_meeting_requests')->name('meeting-requests.undo');

    // Payroll & Salary Management - ADMIN ONLY
    // Payroll & Salary Management - ADMIN ONLY
    Route::get('/payroll', [\App\Http\Controllers\Admin\PayrollController::class, 'index'])
        ->middleware('permission:manage_payroll')->name('payroll.index');
    Route::get('/payroll/export', [\App\Http\Controllers\Admin\PayrollController::class, 'export'])
        ->middleware('permission:manage_payroll')->name('payroll.export');
    Route::post('/payroll/generate', [\App\Http\Controllers\Admin\PayrollController::class, 'generate'])
        ->middleware('permission:manage_payroll')->name('payroll.generate');
    Route::post('/payroll/remove-duplicates', [\App\Http\Controllers\Admin\PayrollController::class, 'removeDuplicates'])
        ->middleware('permission:manage_payroll')->name('payroll.remove-duplicates');
    Route::post('/payroll/bulk-mark-paid', [\App\Http\Controllers\Admin\PayrollController::class, 'bulkMarkAsPaid'])
        ->middleware('permission:manage_payroll')->name('payroll.bulk-mark-paid');
    Route::get('/payroll/{payroll}', [\App\Http\Controllers\Admin\PayrollController::class, 'show'])
        ->middleware('permission:manage_payroll')->name('payroll.show');
    Route::post('/payroll/{payroll}/mark-paid', [\App\Http\Controllers\Admin\PayrollController::class, 'markAsPaid'])
        ->middleware('permission:manage_payroll')->name('payroll.mark-paid');
    Route::post('/payroll/{payroll}/undo', [\App\Http\Controllers\Admin\PayrollController::class, 'undoPayment'])
        ->middleware('permission:manage_payroll')->name('payroll.undo');
    Route::post('/payroll/{payroll}/cancel', [\App\Http\Controllers\Admin\PayrollController::class, 'cancel'])
        ->middleware('permission:manage_payroll')->name('payroll.cancel');
    Route::delete('/payroll/{payroll}', [\App\Http\Controllers\Admin\PayrollController::class, 'destroy'])
        ->middleware('permission:manage_payroll')->name('payroll.destroy');
    Route::post('/payroll/{payroll}/regenerate', [\App\Http\Controllers\Admin\PayrollController::class, 'regeneratePayroll'])
        ->middleware('permission:manage_payroll')->name('payroll.regenerate');
    Route::post('/payroll/regenerate-week', [\App\Http\Controllers\Admin\PayrollController::class, 'regenerateWeek'])
        ->middleware('permission:manage_payroll')->name('payroll.regenerate-week');
    Route::match(['get', 'post'], '/payroll/fix-october-swap', [\App\Http\Controllers\Admin\PayrollController::class, 'fixOctoberSwap'])
        ->middleware('permission:manage_payroll')->name('payroll.fix-october-swap');

    // Salary settings management
    Route::resource('salary-settings', \App\Http\Controllers\Admin\SalarySettingController::class)
        ->middleware('permission:manage_salary_settings');
    Route::post('/salary-settings/bulk-create', [\App\Http\Controllers\Admin\SalarySettingController::class, 'bulkCreate'])
        ->middleware('permission:manage_salary_settings')->name('salary-settings.bulk-create');
    Route::post('/salary-settings/{salarySetting}/toggle-status', [\App\Http\Controllers\Admin\SalarySettingController::class, 'toggleStatus'])
        ->middleware('permission:manage_salary_settings')->name('salary-settings.toggle-status');

    // Salary Reimbursement Tracking
    Route::get('/reimbursements', [\App\Http\Controllers\Admin\SalaryReimbursementController::class, 'index'])
        ->middleware('permission:manage_reimbursements')->name('reimbursements.index');
    Route::post('/reimbursements/calculate', [\App\Http\Controllers\Admin\SalaryReimbursementController::class, 'calculatePeriod'])
        ->middleware('permission:manage_reimbursements')->name('reimbursements.calculate');
    Route::get('/reimbursements/{reimbursement}', [\App\Http\Controllers\Admin\SalaryReimbursementController::class, 'show'])
        ->middleware('permission:manage_reimbursements')->name('reimbursements.show');
    Route::post('/reimbursements/{reimbursement}/reimburse', [\App\Http\Controllers\Admin\SalaryReimbursementController::class, 'markAsReimbursed'])
        ->middleware('permission:manage_reimbursements')->name('reimbursements.reimburse');

    // Duty Tracking & Ranking (Admin only)
    Route::get('/duty-tracking', [\App\Http\Controllers\Admin\DutyTrackingController::class, 'index'])
        ->middleware('admin')->name('duty-tracking.index');
    Route::get('/duty-tracking/export-weekly', [\App\Http\Controllers\Admin\DutyTrackingController::class, 'exportWeekly'])
        ->middleware('admin')->name('duty-tracking.export-weekly');
    Route::get('/duty-tracking/{user}', [\App\Http\Controllers\Admin\DutyTrackingController::class, 'show'])
        ->middleware('admin')->name('duty-tracking.show');

    // Manual trigger: Force run auto-checkout for all expired duty sessions (Admin only)
    Route::post('/duty-tracking/trigger-auto-checkout', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        try {
            \Artisan::call('attendance:check-expired-sessions');
            $output = \Artisan::output();
            \Log::info('[ADMIN] Manual auto-checkout trigger executed', [
                'admin_id' => auth()->id(),
                'output'   => $output,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Auto-checkout berhasil dijalankan.',
                'output'  => nl2br(e(trim($output))),
            ]);
        } catch (\Exception $e) {
            \Log::error('[ADMIN] Manual auto-checkout trigger failed', [
                'admin_id' => auth()->id(),
                'error'    => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan auto-checkout: ' . $e->getMessage(),
            ], 500);
        }
    })->middleware('admin')->name('duty-tracking.trigger-auto-checkout');

    // Staf Tidak Aktif Duty (Staff Manager ke atas)
    Route::get('/inactive-staff', [\App\Http\Controllers\Admin\InactiveStaffController::class, 'index'])
        ->name('inactive-staff.index');

    // Sub-Jabatan / Divisi (Executive ke atas, Khusus Alta Hospital)
    Route::middleware('alta_only')->prefix('sub-roles')->name('sub-roles.')->group(function () {
        Route::get('/',                  [\App\Http\Controllers\Admin\SubRoleController::class, 'index'])->name('index');
        Route::get('/assign',            [\App\Http\Controllers\Admin\SubRoleController::class, 'assignForm'])->name('assign');
        Route::post('/assign',           [\App\Http\Controllers\Admin\SubRoleController::class, 'assignStore'])->name('assign.store');
        Route::post('/assign-bulk',      [\App\Http\Controllers\Admin\SubRoleController::class, 'assignBulk'])->name('assign-bulk');
        Route::get('/{subRole}/members', [\App\Http\Controllers\Admin\SubRoleController::class, 'members'])->name('members');
        Route::post('/{subRole}/toggle-active', [\App\Http\Controllers\Admin\SubRoleController::class, 'toggleActive'])->name('toggle-active');
    });

    // Structural/Organizational Management (Admin only - no specific permission check yet)
    Route::resource('structural', \App\Http\Controllers\Admin\StructuralManagementController::class)
        ->middleware('admin');
    Route::post('/structural/reorder', [\App\Http\Controllers\Admin\StructuralManagementController::class, 'reorder'])
        ->middleware('admin')->name('structural.reorder');

    // Organizational Structure Management (for public struktural-ems page)
    Route::resource('organizational-structure', \App\Http\Controllers\Admin\OrganizationalStructureController::class)
        ->middleware('admin');
    Route::post('/organizational-structure/{id}/activate', [\App\Http\Controllers\Admin\OrganizationalStructureController::class, 'activate'])
        ->middleware('admin')->name('organizational-structure.activate');
    // Simplified name editing
    Route::get('/organizational-structure/{id}/edit-names', [\App\Http\Controllers\Admin\OrganizationalStructureController::class, 'editNames'])
        ->middleware('admin')->name('organizational-structure.edit-names');
    Route::put('/organizational-structure/{id}/update-names', [\App\Http\Controllers\Admin\OrganizationalStructureController::class, 'updateNames'])
        ->middleware('admin')->name('organizational-structure.update-names');



    // Live Chat (Permission-based access)
    Route::get('/chat', function () {
        return view('admin.chat.index');
    })->middleware('permission:access_live_chat')->name('chat.index');

    // Feedback Management (Permission-based access with Hospital Isolation)
    Route::get('/feedback', function () {
        $user = Auth::user();
        $query = \App\Models\Feedback::query();

        if (!$user->isAdmin()) {
            if ($user->isRoxwood()) {
                $query->where('hospital', 'roxwood');
            } else {
                $query->where(function($q) {
                    $q->where('hospital', 'alta')
                      ->orWhere('hospital', 'Alta Hospital')
                      ->orWhere('hospital', 'Alta Street Hospital')
                      ->orWhereNull('hospital');
                });
            }
        }

        $totalFeedback = (clone $query)->count();
        $newFeedback = (clone $query)->where('status', 'new')->count();
        $kritikCount = (clone $query)->where('type', 'laporan')->count();
        $saranCount = (clone $query)->where('type', 'masukan')->count();

        return view('admin.feedback.index', compact('totalFeedback', 'newFeedback', 'kritikCount', 'saranCount'));
    })->middleware('permission:access_feedback')->name('feedback.index');


    // Role Permission Management (Admin Only)
    Route::get('/roles/permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'index'])
        ->middleware('admin')
        ->name('roles.permissions');

    Route::post('/roles/{role}/toggle-permission', [App\Http\Controllers\Admin\RolePermissionController::class, 'togglePermission'])
        ->middleware('admin')
        ->name('roles.toggle-permission');


    Route::post('/users/{user}/toggle-chat-permission', [App\Http\Controllers\Admin\RolePermissionController::class, 'toggleUserChatPermission'])
        ->middleware('admin')
        ->name('users.toggle-chat-permission');

    // Telegram Bot Settings
    Route::get('/telegram', [\App\Http\Controllers\Admin\TelegramSettingController::class, 'index'])
        ->middleware('admin')
        ->name('telegram.index');
    Route::put('/telegram', [\App\Http\Controllers\Admin\TelegramSettingController::class, 'update'])
        ->middleware('admin')
        ->name('telegram.update');
    Route::post('/telegram/test', [\App\Http\Controllers\Admin\TelegramSettingController::class, 'test'])
        ->middleware('admin')
        ->name('telegram.test');

    // AI (Google Gemini) Settings
    Route::get('/ai-settings', [\App\Http\Controllers\Admin\AiSettingController::class, 'index'])
        ->middleware('admin')
        ->name('ai-settings.index');
    Route::put('/ai-settings', [\App\Http\Controllers\Admin\AiSettingController::class, 'update'])
        ->middleware('admin')
        ->name('ai-settings.update');
    Route::post('/ai-settings/test', [\App\Http\Controllers\Admin\AiSettingController::class, 'test'])
        ->middleware('admin')
        ->name('ai-settings.test');

    // Voting Management (Admin / High Command)
    Route::get('/voting', [VotingController::class, 'adminIndex'])->middleware('permission:manage_users')->name('voting.index');
    Route::get('/voting/create', [VotingController::class, 'create'])->middleware('permission:manage_users')->name('voting.create');
    Route::post('/voting', [VotingController::class, 'store'])->middleware('permission:manage_users')->name('voting.store');
    Route::get('/voting/{id}/edit', [VotingController::class, 'edit'])->middleware('permission:manage_users')->name('voting.edit');
    Route::put('/voting/{id}', [VotingController::class, 'update'])->middleware('permission:manage_users')->name('voting.update');
    Route::post('/voting/{id}/toggle-status', [VotingController::class, 'toggleStatus'])->middleware('permission:manage_users')->name('voting.toggle-status');
    Route::delete('/voting/{id}', [VotingController::class, 'destroy'])->middleware('permission:manage_users')->name('voting.destroy');
});

// ── Pratinjau Langsung Gambar Sertifikat Resmi (SVG) — Dapat Dilihat Seluruh Medic (Alta & Roxwood) ──
Route::middleware(['auth'])->group(function () {
    Route::get('/portal/certifications/{certification}/image', [\App\Http\Controllers\Portal\CertificateImageController::class, 'show'])->name('portal.cert.image');
    Route::get('/certifications/{certification}/image', [\App\Http\Controllers\Portal\CertificateImageController::class, 'show'])->name('cert.image');
});

// ═══════════════════════════════════════════════════════════════════════════
// PORTAL MANAJEMEN ALTA HOSPITAL — Cuti, Resign, Sertifikasi, Stase, Promosi, IE, Interview
// Fitur ini khusus anggota Alta Hospital (bukan Roxwood).
// ═══════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'alta_only'])->prefix('portal')->name('portal.')->group(function () {

    // ── Pengajuan Cuti (Semua Anggota) ───────────────────────────────────────
    Route::prefix('leave')->name('leave.')->group(function () {
        Route::get('/',           [\App\Http\Controllers\Portal\LeaveRequestController::class, 'index'])->name('index');
        Route::get('/public-list',[\App\Http\Controllers\Portal\LeaveRequestController::class, 'publicList'])->name('public-list');
        Route::get('/create',     [\App\Http\Controllers\Portal\LeaveRequestController::class, 'create'])->name('create');
        Route::post('/',          [\App\Http\Controllers\Portal\LeaveRequestController::class, 'store'])->name('store');
        Route::get('/{leave}',    [\App\Http\Controllers\Portal\LeaveRequestController::class, 'show'])->name('show');
        // Manager ke atas: manage all
        Route::get('/manage/all', [\App\Http\Controllers\Portal\LeaveRequestController::class, 'manage'])->name('manage');
        Route::post('/{leave}/approve', [\App\Http\Controllers\Portal\LeaveRequestController::class, 'approve'])->name('approve');
        Route::post('/{leave}/reject',  [\App\Http\Controllers\Portal\LeaveRequestController::class, 'reject'])->name('reject');
    });

    // ── Pengajuan Resign (Semua Anggota; PND & IE untuk approval) ─────────────
    Route::prefix('resignation')->name('resignation.')->group(function () {
        Route::get('/',        [\App\Http\Controllers\Portal\ResignationController::class, 'index'])->name('index');
        Route::get('/create',  [\App\Http\Controllers\Portal\ResignationController::class, 'create'])->name('create');
        Route::post('/',       [\App\Http\Controllers\Portal\ResignationController::class, 'store'])->name('store');
        Route::post('/cancel-own', [\App\Http\Controllers\Portal\ResignationController::class, 'cancelOwn'])->name('cancel-own');
        // Anggota: Upload Bukti Resign (4 Berkas Wajib)
        Route::post('/upload-proof', [\App\Http\Controllers\Portal\ResignationController::class, 'uploadProof'])->name('upload-proof');
        // PND
        Route::get('/manage/pnd',                       [\App\Http\Controllers\Portal\ResignationController::class, 'managePnd'])->name('manage.pnd');
        Route::post('/{resignation}/pnd-approve',        [\App\Http\Controllers\Portal\ResignationController::class, 'pndApprove'])->name('pnd-approve');
        Route::post('/{resignation}/pnd-reject',        [\App\Http\Controllers\Portal\ResignationController::class, 'pndReject'])->name('pnd-reject');
        Route::post('/{resignation}/pnd-cancel',        [\App\Http\Controllers\Portal\ResignationController::class, 'pndCancel'])->name('pnd-cancel');
        // IE
        Route::get('/manage/ie',                        [\App\Http\Controllers\Portal\ResignationController::class, 'manageIe'])->name('manage.ie');
        Route::get('/ptdh-calculate',                   [\App\Http\Controllers\Portal\ResignationController::class, 'ptdhCalculate'])->name('ptdh-calculate');
        Route::post('/ptdh-store',                      [\App\Http\Controllers\Portal\ResignationController::class, 'ptdhStore'])->name('ptdh-store');
        Route::post('/{resignation}/ie-verify-payment', [\App\Http\Controllers\Portal\ResignationController::class, 'ieVerifyPayment'])->name('ie-verify');
        Route::post('/{resignation}/ie-request-revision',[\App\Http\Controllers\Portal\ResignationController::class, 'ieRequestProofRevision'])->name('ie-request-revision');
        Route::post('/{resignation}/ie-final-confirm',  [\App\Http\Controllers\Portal\ResignationController::class, 'ieFinalConfirm'])->name('ie-final-confirm');
        Route::post('/{resignation}/ie-cancel',         [\App\Http\Controllers\Portal\ResignationController::class, 'ieCancel'])->name('ie-cancel');
        // Log Resign / Arsip Administrasi
        Route::get('/logs',                             [\App\Http\Controllers\Portal\ResignationController::class, 'logs'])->name('logs');
        Route::get('/{resignation}', [\App\Http\Controllers\Portal\ResignationController::class, 'show'])->where('resignation', '[0-9]+')->name('show');
    });

    // ── Pengajuan Sertifikat Kendaraan (Anggota) ──────────────────────────────
    Route::prefix('vehicle-cert')->name('vehicle-cert.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Portal\VehicleCertApplicationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Portal\VehicleCertApplicationController::class, 'store'])->name('store');
        Route::post('/{application}/cancel', [\App\Http\Controllers\Portal\VehicleCertApplicationController::class, 'cancel'])->name('cancel');
    });

    // ── Pengajuan Sertifikat Operasi (Anggota) ────────────────────────────────
    Route::prefix('operation-cert')->name('operation-cert.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Portal\OperationCertApplicationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Portal\OperationCertApplicationController::class, 'store'])->name('store');
        Route::post('/{application}/cancel', [\App\Http\Controllers\Portal\OperationCertApplicationController::class, 'cancel'])->name('cancel');
    });

    // ── GA: Sertifikasi Kendaraan ─────────────────────────────────────────────
    Route::prefix('ga')->name('ga.')->group(function () {
        Route::get('/',                          [\App\Http\Controllers\Portal\GaCertificationController::class, 'index'])->name('index');
        Route::get('/create',                    [\App\Http\Controllers\Portal\GaCertificationController::class, 'create'])->name('create');
        Route::post('/',                         [\App\Http\Controllers\Portal\GaCertificationController::class, 'store'])->name('store');
        Route::post('/{certification}/revoke',   [\App\Http\Controllers\Portal\GaCertificationController::class, 'revoke'])->name('revoke');
        Route::delete('/{certification}',        [\App\Http\Controllers\Portal\GaCertificationController::class, 'destroy'])->name('destroy');
        Route::post('/applications/{application}/approve', [\App\Http\Controllers\Portal\GaCertificationController::class, 'approveApplication'])->name('application.approve');
        Route::post('/applications/{application}/reject',  [\App\Http\Controllers\Portal\GaCertificationController::class, 'rejectApplication'])->name('application.reject');
    });

    // ── MSL: Sertifikasi Visum & Stase ────────────────────────────────────────
    Route::prefix('msl')->name('msl.')->group(function () {
        Route::get('/',                           [\App\Http\Controllers\Portal\MslCertificationController::class, 'index'])->name('index');
        Route::post('/visum',                     [\App\Http\Controllers\Portal\MslCertificationController::class, 'storeVisum'])->name('visum.store');
        Route::post('/{certification}/revoke',    [\App\Http\Controllers\Portal\MslCertificationController::class, 'revoke'])->name('revoke');
        Route::delete('/{certification}',         [\App\Http\Controllers\Portal\MslCertificationController::class, 'destroy'])->name('destroy');
        // Manajemen Konsulen Stase (Daftar & Cabut Konsulen)
        Route::post('/konsulen/assign',           [\App\Http\Controllers\Portal\MslCertificationController::class, 'assignKonsulen'])->name('konsulen.assign');
        Route::post('/konsulen/{user}/revoke',    [\App\Http\Controllers\Portal\MslCertificationController::class, 'revokeKonsulen'])->name('konsulen.revoke');
        // Kelola Stase & Penerbitan Sertifikat Stase
        Route::get('/stase',                      [\App\Http\Controllers\Portal\MslCertificationController::class, 'staseIndex'])->name('stase.index');
        Route::post('/stase/{stase}/approve',     [\App\Http\Controllers\Portal\MslCertificationController::class, 'staseApprove'])->name('stase.approve');
        Route::post('/stase/{stase}/reject',      [\App\Http\Controllers\Portal\MslCertificationController::class, 'staseReject'])->name('stase.reject');
        Route::post('/stase/{stase}/complete',    [\App\Http\Controllers\Portal\MslCertificationController::class, 'staseComplete'])->name('stase.complete');
    });

    // ── IE: Fitur & Kebutuhan Sistem Lengkap ──────────────────────────────────
    Route::prefix('ie')->name('ie.')->group(function () {
        // Kontrak Medis
        Route::get('/',                                   [\App\Http\Controllers\Portal\IeContractController::class, 'index'])->name('index');
        Route::post('/',                                  [\App\Http\Controllers\Portal\IeContractController::class, 'store'])->name('store');
        Route::post('/{certification}/revoke',            [\App\Http\Controllers\Portal\IeContractController::class, 'revoke'])->name('revoke');
        // Manajemen Jabatan Medis & Manajerial
        Route::get('/roles',                              [\App\Http\Controllers\Portal\IeManagementController::class, 'rolesIndex'])->name('roles.index');
        Route::post('/roles/{user}/update',               [\App\Http\Controllers\Portal\IeManagementController::class, 'updateRole'])->name('roles.update');
        // Pemutihan Duty & Pengecualian Cuti
        Route::get('/pemutihan',                          [\App\Http\Controllers\Portal\IeManagementController::class, 'pemutihanIndex'])->name('pemutihan.index');
        Route::post('/pemutihan/{user}/toggle-exemption', [\App\Http\Controllers\Portal\IeManagementController::class, 'toggleExemption'])->name('pemutihan.toggle-exemption');
    });

    // ── Recruitment Medis: Buka/Tutup & Verifikasi Berkas (PND & IE) ──────────
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        Route::get('/manage',                         [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'index'])->name('index');
        Route::post('/toggle',                        [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'toggle'])->name('toggle');
        Route::post('/sync-batches',                  [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'syncBatches'])->name('sync-batches');
        Route::post('/clear',                         [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'clearCandidates'])->name('clear');
        Route::get('/{application}',                  [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'show'])->name('show');
        Route::post('/{application}/status',          [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'updateStatus'])->name('status');
        Route::post('/{application}/convert',         [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'convertCandidate'])->name('convert');
        Route::delete('/{application}',               [\App\Http\Controllers\Portal\RecruitmentManagementController::class, 'destroy'])->name('destroy');
    });

    // ── Role Interview: Wawancara Calon Medis & Pengajuan Role ─────────────────
    Route::prefix('interview')->name('interview.')->group(function () {
        Route::get('/',                                                   [\App\Http\Controllers\Portal\InterviewController::class, 'index'])->name('index');
        Route::get('/apply',                                              [\App\Http\Controllers\Portal\InterviewController::class, 'showApplicationForm'])->name('apply-form');
        Route::post('/apply',                                             [\App\Http\Controllers\Portal\InterviewController::class, 'submitApplication'])->name('apply');
        Route::post('/applications/{application}/approve',                [\App\Http\Controllers\Portal\InterviewController::class, 'approveApplication'])->name('approve-application');
        Route::post('/applications/{application}/reject',                 [\App\Http\Controllers\Portal\InterviewController::class, 'rejectApplication'])->name('reject-application');
        Route::get('/{candidate}/form',                                   [\App\Http\Controllers\Portal\InterviewController::class, 'showForm'])->name('form');
        Route::post('/{candidate}/store',                                 [\App\Http\Controllers\Portal\InterviewController::class, 'storeEvaluation'])->name('store');
        Route::post('/assign-interviewer',                                [\App\Http\Controllers\Portal\InterviewController::class, 'assignInterviewer'])->name('assign');
        Route::post('/{user}/revoke-interviewer',                          [\App\Http\Controllers\Portal\InterviewController::class, 'revokeInterviewer'])->name('revoke');
    });

    // ── Pengajuan Stase (Anggota & Konsulen) ──────────────────────────────────
    Route::prefix('stase')->name('stase.')->group(function () {
        Route::get('/',                           [\App\Http\Controllers\Portal\StaseController::class, 'index'])->name('index');
        Route::get('/create',                     [\App\Http\Controllers\Portal\StaseController::class, 'create'])->name('create');
        Route::post('/',                          [\App\Http\Controllers\Portal\StaseController::class, 'store'])->name('store');
        Route::get('/konsulen/approvals',         [\App\Http\Controllers\Portal\StaseController::class, 'myApprovals'])->name('konsulen.index');
        Route::post('/{stase}/konsulen-approve',  [\App\Http\Controllers\Portal\StaseController::class, 'konsulenApprove'])->name('konsulen.approve');
        Route::post('/{stase}/konsulen-reject',   [\App\Http\Controllers\Portal\StaseController::class, 'konsulenReject'])->name('konsulen.reject');
    });

    // ── PND: Pengajuan Operasi & Sertifikat Operasi ──────────────────────────
    Route::prefix('pnd')->name('pnd.')->group(function () {
        // Anggota
        Route::get('/operations/my',             [\App\Http\Controllers\Portal\PndOperationController::class, 'myOperations'])->name('my-operations');
        Route::get('/operations/create',         [\App\Http\Controllers\Portal\PndOperationController::class, 'createOperation'])->name('operation.create');
        Route::post('/operations',               [\App\Http\Controllers\Portal\PndOperationController::class, 'storeOperation'])->name('operation.store');
        // PND Staff
        Route::get('/operations',                [\App\Http\Controllers\Portal\PndOperationController::class, 'index'])->name('operations');
        Route::post('/operations/{opRequest}/approve', [\App\Http\Controllers\Portal\PndOperationController::class, 'approve'])->name('operation.approve');
        Route::post('/operations/{opRequest}/reject',  [\App\Http\Controllers\Portal\PndOperationController::class, 'reject'])->name('operation.reject');
        // Sertifikat Operasi
        Route::get('/certs',                           [\App\Http\Controllers\Portal\PndOperationController::class, 'certIndex'])->name('cert-index');
        Route::post('/certs',                          [\App\Http\Controllers\Portal\PndOperationController::class, 'certStore'])->name('cert-store');
        Route::delete('/certs/{certification}',        [\App\Http\Controllers\Portal\PndOperationController::class, 'certDestroy'])->name('cert-destroy');
        Route::post('/cert-applications/{application}/approve', [\App\Http\Controllers\Portal\PndOperationController::class, 'approveApplication'])->name('cert-application.approve');
        Route::post('/cert-applications/{application}/reject',  [\App\Http\Controllers\Portal\PndOperationController::class, 'rejectApplication'])->name('cert-application.reject');

        // Kelola Formulir Pendaftaran Pelatihan (Operasi, Surat Menyurat, Visum Hidup)
        Route::get('/training',                         [\App\Http\Controllers\Portal\PndTrainingController::class, 'index'])->name('training.index');
        Route::post('/training/{application}/status',   [\App\Http\Controllers\Portal\PndTrainingController::class, 'updateStatus'])->name('training.status');
        Route::post('/training/bulk',                   [\App\Http\Controllers\Portal\PndTrainingController::class, 'bulkAction'])->name('training.bulk');
        Route::delete('/training/{application}',        [\App\Http\Controllers\Portal\PndTrainingController::class, 'destroy'])->name('training.destroy');
        Route::get('/training/export-discord',          [\App\Http\Controllers\Portal\PndTrainingController::class, 'exportDiscord'])->name('training.export-discord');
    });

    // ── Pendaftaran Pelatihan (Operasi, Surat Menyurat, Visum Hidup) ────────
    Route::prefix('pelatihan')->name('training.')->group(function () {
        Route::get('/',               [\App\Http\Controllers\Portal\TrainingController::class, 'index'])->name('index');
        Route::post('/program/{key}', [\App\Http\Controllers\Portal\TrainingController::class, 'updateProgram'])->name('program.update');
        Route::get('/{type}',         [\App\Http\Controllers\Portal\TrainingController::class, 'showForm'])->name('form');
        Route::post('/{type}',        [\App\Http\Controllers\Portal\TrainingController::class, 'submitForm'])->name('submit');
    });

    // ── Kenaikan Jabatan / Promosi (PND kontrol, Anggota submit) ─────────────
    Route::prefix('promotion')->name('promotion.')->group(function () {
        // Anggota
        Route::get('/',                            [\App\Http\Controllers\Portal\PromotionController::class, 'index'])->name('index');
        Route::get('/create',                      [\App\Http\Controllers\Portal\PromotionController::class, 'create'])->name('create');
        Route::post('/',                           [\App\Http\Controllers\Portal\PromotionController::class, 'store'])->name('store');
        Route::get('/checklist',                   [\App\Http\Controllers\Portal\PromotionController::class, 'checklistApi'])->name('checklist');
        // PND: Kelola Periode
        Route::get('/periods',                     [\App\Http\Controllers\Portal\PromotionController::class, 'periodIndex'])->name('period.index');
        Route::post('/periods/open',               [\App\Http\Controllers\Portal\PromotionController::class, 'openPeriod'])->name('period.open');
        Route::post('/periods/{period}/close',     [\App\Http\Controllers\Portal\PromotionController::class, 'closePeriod'])->name('period.close');
        // PND: Review Pengajuan
        Route::get('/applications',                         [\App\Http\Controllers\Portal\PromotionController::class, 'manageApplications'])->name('applications');
        Route::post('/applications/{application}/review',   [\App\Http\Controllers\Portal\PromotionController::class, 'reviewApplication'])->name('applications.review');
    });
});

// ── Komisi Disiplin (Comdis) & Credit Score (Khusus Alta Hospital) ───────
Route::middleware(['auth', 'alta_only'])->prefix('credit-score')->name('credit-score.')->group(function () {
    Route::get('/',                 [\App\Http\Controllers\CreditScoreController::class, 'index'])->name('index');
    Route::get('/{user}',           [\App\Http\Controllers\CreditScoreController::class, 'show'])->name('show');
    Route::get('/{user}/input',     [\App\Http\Controllers\CreditScoreController::class, 'inputForm'])->name('input');
    Route::post('/{user}/input',    [\App\Http\Controllers\CreditScoreController::class, 'inputStore'])->name('input.store');
    Route::get('/{user}/edit',      [\App\Http\Controllers\CreditScoreController::class, 'editForm'])->name('edit');
    Route::post('/{user}/edit',     [\App\Http\Controllers\CreditScoreController::class, 'updateScore'])->name('update');
    Route::put('/log/{log}',        [\App\Http\Controllers\CreditScoreController::class, 'updateLog'])->name('log.update');
    Route::delete('/log/{log}',     [\App\Http\Controllers\CreditScoreController::class, 'destroyLog'])->name('log.destroy');
});

// Alias jika diakses melalui /portal/credit-score
Route::middleware(['auth', 'alta_only'])->prefix('portal/credit-score')->group(function () {
    Route::get('/',                 [\App\Http\Controllers\CreditScoreController::class, 'index']);
    Route::get('/{user}',           [\App\Http\Controllers\CreditScoreController::class, 'show']);
    Route::get('/{user}/input',     [\App\Http\Controllers\CreditScoreController::class, 'inputForm']);
    Route::post('/{user}/input',    [\App\Http\Controllers\CreditScoreController::class, 'inputStore']);
    Route::get('/{user}/edit',      [\App\Http\Controllers\CreditScoreController::class, 'editForm']);
    Route::post('/{user}/edit',     [\App\Http\Controllers\CreditScoreController::class, 'updateScore']);
    Route::put('/log/{log}',        [\App\Http\Controllers\CreditScoreController::class, 'updateLog']);
    Route::delete('/log/{log}',     [\App\Http\Controllers\CreditScoreController::class, 'destroyLog']);
});

