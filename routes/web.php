<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\Admin\StaffManagementController;

// Temporary Debug Route for operations create
Route::get('/debug-create', function () {
    try {
        $controller = app()->make(\App\Http\Controllers\Staff\OperationRecordController::class);
        return $controller->create();
    } catch (\Throwable $e) {
        return '❌ ERROR: ' . $e->getMessage() . '<br>📍 File: ' . $e->getFile() . ':' . $e->getLine() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

// Test Route
Route::get('/test', function () {
    return 'Aplikasi berjalan dengan baik!';
});

// TEMPORARY: Run operation tables migration via browser
// DELETE THIS ROUTE AFTER USE!
Route::get('/install-operations', function () {
    $results = [];

    // Table 1: operation_records
    if (!\Illuminate\Support\Facades\Schema::hasTable('operation_records')) {
        \Illuminate\Support\Facades\Schema::create('operation_records', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_waktu');
            $table->string('lokasi');
            $table->enum('jenis_operasi', ['Operasi Minor', 'Operasi Mayor', 'Emergency', 'Bedah Umum', 'Orthopedi', 'Lainnya']);
            $table->string('nama_pasien');
            $table->text('diagnosa');
            $table->text('tindakan_operasi');
            $table->text('hasil_operasi');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        $results[] = '✅ Tabel operation_records berhasil dibuat.';
    } else {
        $results[] = '⚠️ Tabel operation_records sudah ada.';
    }

    // Table 2: operation_record_members
    if (!\Illuminate\Support\Facades\Schema::hasTable('operation_record_members')) {
        \Illuminate\Support\Facades\Schema::create('operation_record_members', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('operation_record_id')->constrained('operation_records')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        $results[] = '✅ Tabel operation_record_members berhasil dibuat.';
    } else {
        $results[] = '⚠️ Tabel operation_record_members sudah ada.';
    }

    // Table 3: operation_photos
    if (!\Illuminate\Support\Facades\Schema::hasTable('operation_photos')) {
        \Illuminate\Support\Facades\Schema::create('operation_photos', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('operation_record_id')->constrained('operation_records')->onDelete('cascade');
            $table->string('file_path');
            $table->timestamps();
        });
        $results[] = '✅ Tabel operation_photos berhasil dibuat.';
    } else {
        $results[] = '⚠️ Tabel operation_photos sudah ada.';
    }

    $results[] = '';
    $results[] = '🎉 Selesai! Silakan hapus route /install-operations dari routes/web.php setelah ini.';

    return implode('<br>', $results);
});

// TEMPORARY: Add dpjp_id column to operation_records
// DELETE THIS ROUTE AFTER USE!
Route::get('/install-dpjp', function () {
    $results = [];

    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('operation_records', 'dpjp_id')) {
            \Illuminate\Support\Facades\Schema::table('operation_records', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('dpjp_id')->nullable()->after('created_by');
                $table->foreign('dpjp_id')->references('id')->on('users')->onDelete('set null');
            });
            $results[] = '✅ Kolom dpjp_id berhasil ditambahkan ke tabel operation_records.';
        } else {
            $results[] = '⚠️ Kolom dpjp_id sudah ada. Tidak ada perubahan.';
        }
    } catch (\Exception $e) {
        $results[] = '❌ Error: ' . $e->getMessage();
    }

    $results[] = '';
    $results[] = '🎉 Selesai! Silakan hapus route /install-dpjp dari routes/web.php setelah ini.';

    return implode('<br>', $results);
});

// TEMPORARY: Add medical_details column to operation_records
// DELETE THIS ROUTE AFTER USE!
Route::get('/install-medical-details', function () {
    $results = [];

    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('operation_records', 'medical_details')) {
            \Illuminate\Support\Facades\Schema::table('operation_records', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->longText('medical_details')->nullable()->after('catatan');
            });
            $results[] = '✅ Kolom medical_details berhasil ditambahkan ke tabel operation_records.';
        } else {
            $results[] = '⚠️ Kolom medical_details sudah ada. Tidak ada perubahan.';
        }
    } catch (\Exception $e) {
        $results[] = '❌ Error: ' . $e->getMessage();
    }

    $results[] = '';
    $results[] = '🎉 Selesai! Silakan hapus route /install-medical-details dari routes/web.php setelah ini.';

    return implode('<br>', $results);
});

// TEMPORARY: Add hospital column to operation_records
// DELETE THIS ROUTE AFTER USE!
Route::get('/install-hospital-column', function () {
    $results = [];

    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('operation_records', 'hospital')) {
            \Illuminate\Support\Facades\Schema::table('operation_records', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('hospital')->nullable()->default('roxwood')->after('jenis_operasi');
            });
            $results[] = '✅ Kolom hospital berhasil ditambahkan ke tabel operation_records.';
        } else {
            $results[] = '⚠️ Kolom hospital sudah ada. Tidak ada perubahan.';
        }
    } catch (\Exception $e) {
        $results[] = '❌ Error: ' . $e->getMessage();
    }

    $results[] = '';
    $results[] = '🎉 Selesai! Silakan hapus route /install-hospital-column dari routes/web.php setelah ini.';

    return implode('<br>', $results);
});

// TEMPORARY: Storage installer & folder creator for shared hosting
Route::get('/fix-storage-link', function () {
    $results = [];
    try {
        // 0. Cek konfigurasi server
        $maxUpload = ini_get('upload_max_filesize');
        $maxPost = ini_get('post_max_size');
        $results[] = "⚙️ <b>Server Config:</b> Max Upload Size: $maxUpload, Max Post Size: $maxPost";
        
        // 1. Cek folder public/uploads/operations/ — tempat foto disimpan
        $uploadsOps = public_path('uploads/operations');
        if (!file_exists($uploadsOps)) {
            @mkdir($uploadsOps, 0777, true);
            $results[] = '✅ Folder public/uploads/operations dibuat.';
        } else {
            $results[] = 'ℹ️ Folder public/uploads/operations sudah ada.';
        }

        // 2. Test write ke folder uploads/operations
        $testFile = $uploadsOps . '/test_' . time() . '.txt';
        @file_put_contents($testFile, 'OK');
        if (file_exists($testFile)) {
            @unlink($testFile);
            $results[] = '🚀 Uji penulisan di uploads/operations BERHASIL! Foto bisa diupload.';
        } else {
            $results[] = '❌ GAGAL menulis ke uploads/operations. Cek permission folder (chmod 755 atau 775).';
        }

        // 3. URL yang akan digunakan
        $sampleUrl = asset('uploads/operations/contoh.jpg');
        $results[] = '🔗 URL foto akan berformat: <code>' . $sampleUrl . '</code>';

        // 4. Hitung foto di database
        $photoCount = \App\Models\OperationPhoto::count();
        $results[] = '';
        $results[] = "📊 Total foto tersimpan di database: <strong>{$photoCount}</strong> record.";
        if ($photoCount > 0) {
            $photos = \App\Models\OperationPhoto::latest()->take(5)->get();
            foreach ($photos as $p) {
                $fileFull = public_path($p->file_path);
                $exists = file_exists($fileFull);
                $icon = $exists ? '✅' : '❌';
                $results[] = "$icon ID#{$p->id}: {$p->file_path} " . ($exists ? '(file ada)' : '(FILE TIDAK ADA!)');
            }
        }

        $results[] = '';
        $results[] = '🎉 Selesai! Upload rekam operasi baru dengan foto untuk uji coba.';
    } catch (\Exception $e) {
        $results[] = '❌ Exception: ' . $e->getMessage();
    }

    return implode('<br>', $results);
});



// TEMPORARY DIAGNOSTIC ROUTE - DELETE AFTER DEBUGGING
Route::get('/check-member-system', function () {
    $results = [];
    try {
        $results[] = '✅ PHP: ' . phpversion();
        $results[] = '✅ Laravel: ' . app()->version();

        // Test member_messages table
        $msgCount = \App\Models\MemberMessage::count();
        $results[] = "✅ Tabel member_messages OK - Total: {$msgCount} pesan.";

        // Test last_seen_at column
        $userCount = \App\Models\User::whereNotNull('last_seen_at')->count();
        $results[] = "✅ Kolom last_seen_at OK - {$userCount} user pernah online.";

        // Test MemberController can be resolved
        $controller = app()->make(\App\Http\Controllers\Staff\MemberController::class);
        $results[] = '✅ MemberController berhasil di-resolve.';

        // Test Livewire component
        $component = app()->make(\App\Livewire\MemberMessages::class);
        $results[] = '✅ MemberMessages Livewire component berhasil di-resolve.';

        // Test routes
        $results[] = '✅ Route staff.members.index: ' . route('staff.members.index');
        $results[] = '✅ Route staff.messages.index: ' . route('staff.messages.index');

        $results[] = '';
        $results[] = '🎉 SEMUA KOMPONEN BERFUNGSI DENGAN BAIK!';
        $results[] = '🗑️ Hapus route /check-member-system dari routes/web.php setelah debug selesai.';
    } catch (\Exception $e) {
        $results[] = '❌ ERROR: ' . $e->getMessage();
        $results[] = '📍 Di: ' . $e->getFile() . ':' . $e->getLine();
    }
    return implode('<br>', $results);
});

// Route web untuk mengecek status migrasi langsung dari browser
Route::get('/migrate-status', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:status');
        return '<pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage();
    }
});

// Route web untuk menjalankan migrasi langsung dari browser
Route::get('/run-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage();
    }
});

// Route otomatis untuk melengkapi kolom & tabel baru di database hosting secara langsung
Route::get('/auto-setup-db', function () {
    $logs = [];
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_at')) {
            \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable();
            });
            $logs[] = '✅ Kolom last_seen_at berhasil ditambahkan ke tabel users.';
        } else {
            $logs[] = 'ℹ️ Kolom last_seen_at sudah ada di tabel users.';
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('member_messages')) {
            \Illuminate\Support\Facades\Schema::create('member_messages', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['sender_id', 'receiver_id']);
                $table->index(['receiver_id', 'is_read']);
            });
            $logs[] = '✅ Tabel member_messages berhasil dibuat.';
        } else {
            $logs[] = 'ℹ️ Tabel member_messages sudah ada.';
        }

        $logs[] = '';
        $logs[] = '🎉 DATABASE SETUP BERHASIL 100%! SILAKAN KEMBALI KE HALAMAN UTAMA / REFRESH WEBSITE ANDA.';
    } catch (\Exception $e) {
        $logs[] = '❌ Error: ' . $e->getMessage();
    }
    return implode('<br>', $logs);
});

// DEBUG: Cek nilai last_seen_at di database
Route::get('/debug-online', function () {
    $logs = [];
    try {
        $currentUserId = auth()->id();
        $logs[] = '<b>== Debug Status Online ==</b>';
        $logs[] = 'Waktu server: ' . now()->toDateTimeString();
        $logs[] = '';

        // Cek kolom last_seen_at ada atau tidak
        $hasColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_at');
        $logs[] = 'Kolom last_seen_at: ' . ($hasColumn ? '✅ Ada' : '❌ BELUM ADA — jalankan /auto-setup-db dulu!');

        if (!$hasColumn) {
            return implode('<br>', $logs);
        }

        // Tampilkan last_seen_at semua user yang login
        $users = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotNull('role_id')
            ->select('id', 'name', 'last_seen_at')
            ->get();

        $logs[] = '';
        $logs[] = '<b>Daftar last_seen_at semua staff:</b>';
        foreach ($users as $u) {
            $lastSeen = $u->last_seen_at;
            if ($lastSeen) {
                $diff = now()->diffInMinutes(\Carbon\Carbon::parse($lastSeen));
                $status = $diff <= 5 ? '🟢 ONLINE' : '⚫ Offline (' . $diff . ' menit lalu)';
            } else {
                $status = '⚫ Offline (belum pernah aktif)';
            }
            $marker = ($u->id == $currentUserId) ? ' <-- AKUN ANDA' : '';
            $logs[] = "- [{$u->id}] {$u->name}: {$lastSeen} → {$status}{$marker}";
        }

        // Update last_seen_at akun yang sedang login sekarang
        if ($currentUserId) {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $currentUserId)
                ->update(['last_seen_at' => now()]);
            $logs[] = '';
            $logs[] = '✅ last_seen_at akun Anda diperbarui ke: ' . now()->toDateTimeString();
            $logs[] = 'Silakan buka /staff/members dan lihat apakah indikator hijau muncul.';
        }
    } catch (\Exception $e) {
        $logs[] = '❌ Error: ' . $e->getMessage();
    }
    return implode('<br>', $logs);
});

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

// TEMPORARY: Fix jenis_operasi enum to include Konsultasi Spesialisasi
// Run: /fix-operasi-enum — then delete this route
Route::get('/fix-operasi-enum', function () {
    $results = [];
    try {
        // MySQL ALTER TABLE to change enum values
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE operation_records MODIFY COLUMN jenis_operasi ENUM('Operasi Minor','Operasi Mayor','Emergency','Konsultasi Spesialisasi','Lainnya') NOT NULL");
        $results[] = '✅ Berhasil! Kolom jenis_operasi sekarang mendukung semua jenis operasi:';
        $results[] = '&nbsp;&nbsp;✅ Operasi Minor';
        $results[] = '&nbsp;&nbsp;✅ Operasi Mayor';
        $results[] = '&nbsp;&nbsp;✅ Emergency';
        $results[] = '&nbsp;&nbsp;✅ Konsultasi Spesialisasi';
        $results[] = '&nbsp;&nbsp;✅ Lainnya';
        $results[] = '';
        $results[] = '🎉 Sekarang Anda bisa menambahkan rekam operasi jenis apapun!';
        $results[] = '🗑️ Hapus route /fix-operasi-enum dari routes/web.php setelah ini.';
    } catch (\Exception $e) {
        $results[] = '❌ Error: ' . $e->getMessage();
    }
    return implode('<br>', $results);
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
    
    // API endpoint for searching members
    Route::get('/api/members/search', [\App\Http\Controllers\Staff\OperationRecordController::class, 'searchMembers'])->name('api.members.search');

    // Members Directory & Profiles
    Route::get('/staff/members', [\App\Http\Controllers\Staff\MemberController::class, 'index'])->name('staff.members.index');
    Route::get('/staff/members/{user}', [\App\Http\Controllers\Staff\MemberController::class, 'show'])->name('staff.members.show');

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

    // Voting Management (Admin / High Command)
    Route::get('/voting', [VotingController::class, 'adminIndex'])->middleware('permission:manage_users')->name('voting.index');
    Route::get('/voting/create', [VotingController::class, 'create'])->middleware('permission:manage_users')->name('voting.create');
    Route::post('/voting', [VotingController::class, 'store'])->middleware('permission:manage_users')->name('voting.store');
    Route::get('/voting/{id}/edit', [VotingController::class, 'edit'])->middleware('permission:manage_users')->name('voting.edit');
    Route::put('/voting/{id}', [VotingController::class, 'update'])->middleware('permission:manage_users')->name('voting.update');
    Route::post('/voting/{id}/toggle-status', [VotingController::class, 'toggleStatus'])->middleware('permission:manage_users')->name('voting.toggle-status');
    Route::delete('/voting/{id}', [VotingController::class, 'destroy'])->middleware('permission:manage_users')->name('voting.destroy');
});