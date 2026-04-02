<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixUserStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:user-status {--dry-run : Preview changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix corrupted user status fields. Resets users who show as working/meeting but are NOT actually clocked in to offline.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('=== DRY RUN MODE - No changes will be made ===');
        }

        $this->info('=== Fix User Status Command ===');
        $this->newLine();

        // 1. Find users with status 'working' but not actually clocked in
        $workingUsers = User::where('status', 'working')->where('is_active', true)->get();
        $workingGhosts = $workingUsers->filter(function ($user) {
            return !$user->isClockedIn();
        });

        $this->info("Users with status 'working': {$workingUsers->count()}");
        $this->info("  Actually clocked in: " . ($workingUsers->count() - $workingGhosts->count()));
        $this->warn("  GHOSTS (not clocked in): {$workingGhosts->count()}");

        // 2. Find users with status 'meeting' but not actually clocked in
        $meetingUsers = User::where('status', 'meeting')->where('is_active', true)->get();
        $meetingGhosts = $meetingUsers->filter(function ($user) {
            return !$user->isClockedIn();
        });

        $this->info("Users with status 'meeting': {$meetingUsers->count()}");
        $this->info("  Actually clocked in: " . ($meetingUsers->count() - $meetingGhosts->count()));
        $this->warn("  GHOSTS (not clocked in): {$meetingGhosts->count()}");

        $totalGhosts = $workingGhosts->count() + $meetingGhosts->count();
        $this->newLine();
        $this->error("TOTAL GHOST USERS TO FIX: {$totalGhosts}");
        $this->newLine();

        if ($totalGhosts === 0) {
            $this->info('No ghost users found. All statuses are correct!');
            return 0;
        }

        if (!$isDryRun) {
            // Reset ghost working users to 'offline'
            $fixedCount = 0;
            foreach ($workingGhosts as $user) {
                $user->update(['status' => 'offline']);
                $fixedCount++;
            }

            // Reset ghost meeting users to 'offline'
            foreach ($meetingGhosts as $user) {
                $user->update(['status' => 'offline']);
                $fixedCount++;
            }

            $this->info("✅ Fixed {$fixedCount} ghost user statuses to 'offline'.");

            Log::info('Fix user status command executed', [
                'working_ghosts_fixed' => $workingGhosts->count(),
                'meeting_ghosts_fixed' => $meetingGhosts->count(),
                'total_fixed' => $fixedCount
            ]);
        } else {
            $this->warn('Would fix the following users:');
            
            if ($workingGhosts->isNotEmpty()) {
                $this->info("--- Working Ghosts ({$workingGhosts->count()}) ---");
                foreach ($workingGhosts->take(20) as $u) {
                    $this->line("  ID:{$u->id} | {$u->name} | Hospital:" . ($u->hospital ?? 'alta'));
                }
                if ($workingGhosts->count() > 20) {
                    $this->line("  ... and " . ($workingGhosts->count() - 20) . " more");
                }
            }

            if ($meetingGhosts->isNotEmpty()) {
                $this->info("--- Meeting Ghosts ({$meetingGhosts->count()}) ---");
                foreach ($meetingGhosts->take(20) as $u) {
                    $this->line("  ID:{$u->id} | {$u->name} | Hospital:" . ($u->hospital ?? 'alta'));
                }
                if ($meetingGhosts->count() > 20) {
                    $this->line("  ... and " . ($meetingGhosts->count() - 20) . " more");
                }
            }
        }

        // 3. Report on currently active sessions for verification
        $this->newLine();
        $this->info('=== VERIFICATION: Currently Active Sessions ===');
        
        $activeSessions = Attendance::where('is_active', true)->with('user')->get();
        $this->info("Active attendance sessions: {$activeSessions->count()}");
        foreach ($activeSessions as $a) {
            $userName = $a->user ? $a->user->name : 'N/A';
            $userStatus = $a->user ? $a->user->status : 'N/A';
            $source = $a->source ?? 'web';
            $this->line("  ID:{$a->id} | {$userName} | Status:{$userStatus} | Source:{$source} | Since: {$a->clock_in}");
        }

        return 0;
    }
}
