<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class TestClockInContinuation extends Command
{
    protected $signature = 'test:clockin-continuation {user_id=1}';
    protected $description = 'Test clock in continuation feature by creating a session from yesterday';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User dengan ID {$userId} tidak ditemukan!");
            return;
        }

        // Hapus sesi aktif yang ada
        Attendance::where('user_id', $userId)
            ->where('is_active', true)
            ->delete();

        // Buat sesi aktif dari kemarin
        $yesterday = Carbon::yesterday();
        $attendance = Attendance::create([
            'user_id' => $userId,
            'clock_in' => $yesterday->setTime(23, 30, 0),
            'work_date' => $yesterday->toDateString(),
            'session_number' => 1,
            'session_type' => 'work',
            'is_active' => true,
            'notes' => 'Test session from yesterday'
        ]);

        $this->info("✅ Sesi test berhasil dibuat!");
        $this->info("👤 User: {$user->name}");
        $this->info("📅 Tanggal: {$yesterday->format('d/m/Y')}");
        $this->info("⏰ Clock In: {$attendance->clock_in->format('H:i:s')}");
        $this->info("🔗 Status: Aktif (belum clock out)");
        $this->info("");
        $this->info("🌐 Sekarang buka dashboard untuk melihat peringatan oranye!");
        $this->info("🔗 URL: " . url('/staff/dashboard'));
    }
}
