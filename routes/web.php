<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\StaffManagementController;

// Test Route
Route::get('/test', function () {
    return 'Aplikasi berjalan dengan baik!';
});

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

// Hosting diagnostic and fix routes (temporary - remove after fixing)
Route::get('/hosting-check', function () {
    return redirect('/check-hosting.php');
})->name('hosting.check');

Route::get('/hosting-fix', function () {
    return redirect('/hosting-fix.php');
})->name('hosting.fix');

// Clear cache route for debugging
Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    return 'Cache cleared successfully!';
});

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
        // Optional: Add simple security token check (bisa diaktifkan jika perlu)
        $token = request()->get('token');
        $expectedToken = env('SCHEDULER_TOKEN', 'your-secret-token-here');

        // Uncomment baris berikut jika ingin menambahkan security token
        // if ($token !== $expectedToken) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

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
Route::get('/form/{type?}', [PublicController::class, 'showForm'])->name('public.form');
Route::post('/form/submit', [PublicController::class, 'submitForm'])->name('public.form.submit');
Route::post('/appointment/create', [PublicController::class, 'createAppointment'])->name('public.appointment.create');
Route::get('/appointment/success/{id}', [PublicController::class, 'appointmentSuccess'])->name('public.appointment.success');

// User Chat & Feedback Routes (Authentication Required)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', function () {
        return view('chat.index');
    })->name('chat.page');

    Route::get('/livechat', function () {
        return view('chat.livechat');
    })->name('chat.livechat');

    Route::get('/feedback', [PublicController::class, 'showFeedbackForm'])->name('feedback.form');
    Route::post('/feedback', [PublicController::class, 'submitFeedback'])->name('feedback.submit');
    Route::get('/feedback/success', [PublicController::class, 'feedbackSuccess'])->name('feedback.success');
});

// Form success route (used after submit)
Route::get('/form/success/{id}', [PublicController::class, 'formSuccess'])->name('public.form.success');
Route::post('/form/testimoni/{id}', [PublicController::class, 'submitTestimoni'])->name('public.form.testimoni');
// Struktural EMS route (includes both EMS and Roxwood Hospital tables)
Route::get('/struktural-ems', [PublicController::class, 'strukturalEms'])->name('public.struktural-ems');
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
Route::get('/pendaftaran-karakter', function () {
    return redirect()->route('public.form', ['type' => 'pendaftaran_karakter']);
})->name('public.pendaftaran-karakter');
// Sitemap route for SEO
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('public.sitemap');

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

    // Attendance routes
    Route::post('/staff/attendance/clock-in', [DashboardController::class, 'clockIn'])->name('staff.attendance.clock-in');
    Route::post('/staff/attendance/clock-out', [DashboardController::class, 'clockOut'])->name('staff.attendance.clock-out');

    // Profile update
    Route::get('/staff/profile', [StaffController::class, 'showProfile'])->name('staff.profile');
    Route::post('/staff/profile', [StaffController::class, 'updateProfile'])->name('staff.profile.update');
    Route::post('/staff/profile/update-email', [StaffController::class, 'updateEmail'])->name('staff.profile.update-email');

    // Forms routes (list, detail, approve, reject)
    Route::get('/staff/forms', [DashboardController::class, 'forms'])->name('staff.forms');
    Route::get('/staff/forms/{id}', [DashboardController::class, 'formDetail'])->name('staff.forms.show');
    Route::post('/staff/forms/{id}/approve', [DashboardController::class, 'approveForm'])->name('staff.forms.approve');
    Route::post('/staff/forms/{id}/reject', [DashboardController::class, 'rejectForm'])->name('staff.forms.reject');
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
    // Route::resource('medical-forms', \App\Http\Controllers\Admin\MedicalFormController::class)->middleware('permission:manage_forms');
    // Route::resource('staff-roles', \App\Http\Controllers\Admin\StaffRoleController::class)->middleware('permission:manage_settings');
    // Attendance reports
    Route::get('/attendance-reports', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])
        ->middleware('permission:view_reports')->name('attendance-reports.index');
    Route::get('/attendance-reports/stats', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'getStats'])
        ->middleware('permission:view_reports')->name('attendance-reports.stats');
    Route::post('/attendance-reports/force-checkout', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'forceCheckOut'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.force-checkout');
    Route::post('/attendance-reports/manual', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'storeManualAttendance'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.manual');
    Route::put('/attendance-reports/{id}', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'updateAttendance'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.update');
    Route::delete('/attendance-reports/{id}', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'deleteAttendance'])
        ->middleware('permission:manage_attendance_advanced')->name('attendance-reports.delete');

    // Payroll management
    Route::get('/payroll', [\App\Http\Controllers\Admin\PayrollController::class, 'index'])
        ->middleware('permission:manage_payroll')->name('payroll.index');
    Route::get('/payroll/export', [\App\Http\Controllers\Admin\PayrollController::class, 'export'])
        ->middleware('permission:manage_payroll')->name('payroll.export');
    Route::post('/payroll/generate', [\App\Http\Controllers\Admin\PayrollController::class, 'generate'])
        ->middleware('permission:manage_payroll')->name('payroll.generate');
    Route::post('/payroll/remove-duplicates', [\App\Http\Controllers\Admin\PayrollController::class, 'removeDuplicates'])
        ->middleware('permission:manage_payroll')->name('payroll.remove-duplicates');
    Route::get('/payroll/{payroll}', [\App\Http\Controllers\Admin\PayrollController::class, 'show'])
        ->middleware('permission:manage_payroll')->name('payroll.show');
    Route::post('/payroll/{payroll}/mark-paid', [\App\Http\Controllers\Admin\PayrollController::class, 'markAsPaid'])
        ->middleware('permission:manage_payroll')->name('payroll.mark-paid');
    Route::post('/payroll/{payroll}/cancel', [\App\Http\Controllers\Admin\PayrollController::class, 'cancel'])
        ->middleware('permission:manage_payroll')->name('payroll.cancel');
    Route::delete('/payroll/{payroll}', [\App\Http\Controllers\Admin\PayrollController::class, 'destroy'])
        ->middleware('permission:manage_payroll')->name('payroll.destroy');
    Route::post('/payroll/{payroll}/regenerate', [\App\Http\Controllers\Admin\PayrollController::class, 'regeneratePayroll'])
        ->middleware('permission:manage_payroll')->name('payroll.regenerate');
    Route::post('/payroll/regenerate-week', [\App\Http\Controllers\Admin\PayrollController::class, 'regenerateWeek'])
        ->middleware('permission:manage_payroll')->name('payroll.regenerate-week');

    // Salary settings management
    Route::resource('salary-settings', \App\Http\Controllers\Admin\SalarySettingController::class)
        ->middleware('permission:manage_payroll');
    Route::post('/salary-settings/bulk-create', [\App\Http\Controllers\Admin\SalarySettingController::class, 'bulkCreate'])
        ->middleware('permission:manage_payroll')->name('salary-settings.bulk-create');
    Route::post('/salary-settings/{salarySetting}/toggle-status', [\App\Http\Controllers\Admin\SalarySettingController::class, 'toggleStatus'])
        ->middleware('permission:manage_payroll')->name('salary-settings.toggle-status');


    // Live Chat (Permission-based access)
    Route::get('/chat', function () {
        return view('admin.chat.index');
    })->middleware('permission:access_live_chat')->name('chat.index');

    // Feedback Management (Permission-based access)
    Route::get('/feedback', function () {
        $totalFeedback = \App\Models\Feedback::count();
        $newFeedback = \App\Models\Feedback::where('status', 'new')->count();
        $kritikCount = \App\Models\Feedback::where('type', 'laporan')->count();
        $saranCount = \App\Models\Feedback::where('type', 'masukan')->count();

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
});