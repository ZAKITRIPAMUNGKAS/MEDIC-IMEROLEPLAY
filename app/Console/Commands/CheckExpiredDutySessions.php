<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckExpiredDutySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-expired-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and auto checkout expired duty sessions with scheduled timer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired duty sessions...');

        try {
            // Cari semua session aktif yang scheduled_end_time <= now()
            $now = Carbon::now('Asia/Jakarta');
            
            $expiredSessions = Attendance::where('is_active', true)
                ->whereNotNull('scheduled_duty_minutes')
                ->whereNotNull('scheduled_end_time')
                ->where('scheduled_end_time', '<=', $now)
                ->get();

            if ($expiredSessions->isEmpty()) {
                $this->info('No expired duty sessions found.');
                return 0;
            }

            $this->info("Found {$expiredSessions->count()} expired session(s). Processing...");

            $successCount = 0;
            $failCount = 0;

            foreach ($expiredSessions as $attendance) {
                try {
                    DB::beginTransaction();

                    // Auto checkout menggunakan scheduled_duty_minutes sebagai durasi
                    $result = $attendance->autoCloseSession();

                    if ($result) {
                        DB::commit();
                        $successCount++;

                        $this->info("Auto checkout successful for attendance ID: {$attendance->id} (User: {$attendance->user_id}, Scheduled: {$attendance->scheduled_duty_minutes} min)");

                        Log::info('Auto checkout completed via command', [
                            'attendance_id' => $attendance->id,
                            'user_id' => $attendance->user_id,
                            'scheduled_minutes' => $attendance->scheduled_duty_minutes,
                            'command' => 'attendance:check-expired-sessions'
                        ]);
                    } else {
                        DB::rollBack();
                        $failCount++;
                        $this->error("Failed to auto checkout attendance ID: {$attendance->id}");

                        Log::error('Auto checkout failed via command', [
                            'attendance_id' => $attendance->id,
                            'user_id' => $attendance->user_id,
                            'command' => 'attendance:check-expired-sessions'
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $failCount++;
                    
                    $this->error("Exception while processing attendance ID: {$attendance->id} - {$e->getMessage()}");

                    Log::error('Exception in auto checkout command', [
                        'attendance_id' => $attendance->id,
                        'user_id' => $attendance->user_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $this->info("Processing complete. Success: {$successCount}, Failed: {$failCount}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            Log::error('CheckExpiredDutySessions command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
