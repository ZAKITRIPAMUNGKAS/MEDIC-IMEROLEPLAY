<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

// ================================================================
// PENGATURAN PARAMETER INJECT
// Format CLI: php inject_duty.php "T84K5Z77" 99999
// ================================================================
$targetIdentifier = $argv[1] ?? 'T84K5Z77'; // Citizen ID, Staff ID, Nama, atau Email
$targetHours      = isset($argv[2]) ? (float)$argv[2] : 99999; // Jumlah jam target
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

// 2. Konversi jam ke total detik (1 Jam = 3600 Detik)
$remainingSeconds = (float)($targetHours * 3600);

// Hapus riwayat lama user ini
DB::table('attendances')->where('user_id', $user->id)->delete();

// 3. Batas aman INT MySQL adalah ~2.000.000.000 detik (sekitar 500.000 jam per record).
// Jika user menginput jam raksasa (misal 123.456.789 jam), kita pecah otomatis ke beberapa baris chunk
$maxChunkSeconds = 1000000000; // 1 Miliar detik per record (~277.000 jam)
$records = [];
$now = now();

while ($remainingSeconds > 0) {
    $currentChunk = ($remainingSeconds > $maxChunkSeconds) ? $maxChunkSeconds : $remainingSeconds;
    
    $records[] = [
        'user_id'          => $user->id,
        'work_date'        => $now->toDateString(),
        'clock_in'         => $now->toDateTimeString(),
        'clock_out'        => $now->toDateTimeString(),
        'session_duration' => (int)$currentChunk,
        'total_hours'      => (int)floor($currentChunk / 60),
        'is_active'        => 0,
        'created_at'       => $now->toDateTimeString(),
        'updated_at'       => $now->toDateTimeString(),
    ];
    
    $remainingSeconds -= $currentChunk;
    
    // Batch insert setiap 500 rows untuk keamanan memori
    if (count($records) >= 500) {
        DB::table('attendances')->insert($records);
        $records = [];
    }
}

if (!empty($records)) {
    DB::table('attendances')->insert($records);
}

// 4. Bersihkan Cache
\Illuminate\Support\Facades\Cache::flush();

echo "\n==================================================\n";
echo "           SUKSES INJECT JAM DUTY                 \n";
echo "==================================================\n";
echo "Target User      : {$user->name}\n";
echo "Citizen ID       : {$user->citizen_id}\n";
echo "Staff ID         : {$user->staff_id}\n";
echo "Target Jam       : " . number_format($targetHours, 0, ',', '.') . " Jam\n";
echo "Total Terbaca    : " . $user->fresh()->getTotalDutyHoursFormatted() . "\n";
echo "Total Detik      : " . number_format($user->fresh()->getTotalDutySeconds(), 0, ',', '.') . " detik\n";
echo "==================================================\n\n";
