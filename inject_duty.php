<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;

$user = User::where('citizen_id', 'T84K5Z77')
    ->orWhere('name', 'like', '%Joseph%')
    ->orWhere('email', 'iferor@mlrp.com')
    ->first();

if (!$user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

// 99999 Jam dalam menit
$targetHours = 99999;
$targetMinutes = $targetHours * 60;
$targetSeconds = $targetHours * 3600; // 359,996,400 detik

// Bersihkan attendance lama dan buat attendance 99999 jam
Attendance::where('user_id', $user->id)->delete();

Attendance::create([
    'user_id'                => $user->id,
    'work_date'              => now()->toDateString(),
    'clock_in'               => now()->subMinutes(5),
    'clock_out'              => now(),
    'scheduled_duty_minutes' => $targetMinutes,
    'session_duration'       => $targetSeconds,
    'session_type'           => 'custom', // 'custom' dikecualikan dari generate gaji (payroll hanya menghitung 'work' & 'meeting')
    'auto_checked_out'       => true,
]);

echo "========================================\n";
echo "STATUS: BERHASIL DISUNTIK KE 99.999 JAM!\n";
echo "========================================\n";
echo "User ID: " . $user->id . "\n";
echo "Nama: " . $user->name . "\n";
echo "Citizen ID: " . $user->citizen_id . "\n";
echo "Staff ID: " . $user->staff_id . "\n";
echo "Session Type: custom (Aman dari perhitungan Payroll/Gaji)\n";
echo "Total Jam Duty: " . $user->fresh()->getTotalDutyHoursFormatted() . "\n";
echo "Total Detik: " . number_format($user->fresh()->getTotalDutySeconds(), 0, ',', '.') . " detik\n";
echo "========================================\n";
