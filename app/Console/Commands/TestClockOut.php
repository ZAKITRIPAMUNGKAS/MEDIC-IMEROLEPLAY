<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\User;

class TestClockOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:clockout {user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test clock out functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User dengan ID {$userId} tidak ditemukan!");
            return;
        }

        // Cari sesi aktif
        $activeSession = Attendance::getAnyActiveSession($userId);
        
        if (!$activeSession) {
            $this->error("Tidak ada sesi aktif untuk user {$user->name}!");
            return;
        }

        $this->info("Sesi aktif ditemukan:");
        $this->info("ID: {$activeSession->id}");
        $this->info("Clock In: {$activeSession->clock_in}");
        $this->info("Work Date: {$activeSession->work_date}");
        $this->info("Is Active: " . ($activeSession->is_active ? 'Yes' : 'No'));

        // Simulasi clock out
        $this->info("\nMelakukan clock out...");
        
        $activeSession->update([
            'clock_out' => now(),
            'notes' => $activeSession->notes . "\n" . "Test clock out dari command"
        ]);
        
        $result = $activeSession->closeSession();
        
        $this->info("Clock out result: " . ($result ? 'Success' : 'Failed'));
        
        // Refresh data
        $activeSession->refresh();
        
        $this->info("\nData setelah clock out:");
        $this->info("Clock Out: {$activeSession->clock_out}");
        $this->info("Total Hours: {$activeSession->total_hours}");
        $this->info("Session Duration: {$activeSession->session_duration}");
        $this->info("Is Active: " . ($activeSession->is_active ? 'Yes' : 'No'));
    }
}
