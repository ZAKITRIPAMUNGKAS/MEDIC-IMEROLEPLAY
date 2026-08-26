<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;
use App\Models\StaffRole;

// ================================================================
// PENGATURAN PARAMETER INJECT (Bisa diisi via argumen CLI atau default di sini)
// Cara pakai CLI: php inject_duty.php "T84K5Z77" 99999
// ================================================================
$targetIdentifier = $argv[1] ?? 'T84K5Z77'; // Citizen ID, Nama, atau Email
$targetHours      = isset($argv[2]) ? (int)$argv[2] : 99999; // Jumlah jam target
// ================================================================

// 1. Cari user di database
$user = User::where('citizen_id', $targetIdentifier)
    ->orWhere('staff_id', $targetIdentifier)
    ->orWhere('email', $targetIdentifier)
    ->orWhere('name', 'like', "%{$targetIdentifier}%")
    ->first();

if (!$user) {
    echo "\n[!] User dengan identitas '{$targetIdentifier}' tidak ditemukan di database.\n";
    echo "Daftar user yang tersedia:\n";
    foreach (User::all() as $u) {
        echo " - [ID: {$u->id}] {$u->name} | CitizenID: {$u->citizen_id} | Email: {$u->email}\n";
    }
    exit(1);
}

// 2. Konversi jam ke detik
$targetSeconds = $targetHours * 3600;

// 3. Update attendance
Attendance::where('user_id', $user->id)->delete();

Attendance::create([
    'user_id'          => $user->id,
    'work_date'        => now()->toDateString(),
    'clock_in'         => now()->subHours($targetHours),
    'clock_out'        => now(),
    'session_duration' => $targetSeconds,
    'status'           => 'completed',
]);

echo "\n==================================================\n";
echo "           SUKSES INJECT JAM DUTY                 \n";
echo "==================================================\n";
echo "Target User      : {$user->name}\n";
echo "Citizen ID       : {$user->citizen_id}\n";
echo "Staff ID         : {$user->staff_id}\n";
echo "Target Jam       : {$targetHours} Jam\n";
echo "Total Terbaca    : " . $user->fresh()->getTotalDutyHoursFormatted() . "\n";
echo "Total Detik      : " . number_format($user->fresh()->getTotalDutySeconds(), 0, ',', '.') . " detik\n";
echo "==================================================\n\n";
