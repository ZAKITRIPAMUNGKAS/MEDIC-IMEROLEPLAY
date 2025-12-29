<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class SplitCrossBoundarySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:split-cross-boundary 
                            {--dry-run : Show what would be split without actually doing it}
                            {--day-only : Only split cross-day sessions}
                            {--week-only : Only split cross-week sessions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Split cross-day and cross-week attendance sessions into separate daily/weekly sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $dayOnly = $this->option('day-only');
        $weekOnly = $this->option('week-only');

        $this->info('🔍 Analyzing cross-boundary sessions...');

        // Find cross-day sessions
        if (!$weekOnly) {
            $crossDaySessions = Attendance::whereNotNull('clock_out')
                ->whereRaw('DATE(clock_in) != DATE(clock_out)')
                ->where('is_active', false)
                ->get();

            $this->info("📅 Found {$crossDaySessions->count()} cross-day sessions:");
            
            foreach ($crossDaySessions as $session) {
                $info = $session->getCrossDayInfo();
                if ($info) {
                    $firstDayHours = round($info['first_day_minutes'] / 60, 2);
                    $secondDayHours = round($info['second_day_minutes'] / 60, 2);
                    
                    $this->line("  - {$session->user->name}: {$firstDayHours}h on {$info['first_day']} + {$secondDayHours}h on {$info['second_day']}");
                }
            }
        }

        // Find cross-week sessions
        if (!$dayOnly) {
            $crossWeekSessions = Attendance::whereNotNull('clock_out')
                ->where('is_active', false)
                ->get()
                ->filter(function($record) {
                    return $record->isCrossWeek();
                });

            $this->info("📆 Found {$crossWeekSessions->count()} cross-week sessions:");
            
            foreach ($crossWeekSessions as $session) {
                $info = $session->getCrossWeekInfo();
                if ($info) {
                    $this->line("  - {$session->user->name}: " . count($info['weeks']) . " weeks");
                    foreach ($info['weeks'] as $week) {
                        $this->line("    Week {$week['week_start']}: {$week['duration_hours']}h");
                    }
                }
            }
        }

        if ($dryRun) {
            $this->warn('🔍 DRY RUN - No changes will be made');
            return;
        }

        // Confirm before proceeding
        if (!$this->confirm('Do you want to proceed with splitting these sessions?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('🔄 Starting session splitting...');

        // Split sessions
        if (!$weekOnly) {
            $this->info('📅 Splitting cross-day sessions...');
            $dayFixed = Attendance::fixCrossDaySessions();
            $this->info("✅ Split {$dayFixed} cross-day sessions");
        }

        if (!$dayOnly) {
            $this->info('📆 Splitting cross-week sessions...');
            $weekFixed = Attendance::fixCrossWeekSessions();
            $this->info("✅ Split {$weekFixed} cross-week sessions");
        }

        $this->info('🎉 Session splitting completed!');
        
        // Show updated leaderboard
        $this->info('📊 Updated weekly leaderboard:');
        $this->showUpdatedLeaderboard();
    }

    private function showUpdatedLeaderboard()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $leaderboard = \App\Models\User::whereHas('attendances', function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                  ->where('session_type', 'work')
                  ->whereNotNull('session_duration')
                  ->where('session_duration', '>', 0);
        })
        ->withSum(['attendances' => function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                  ->where('session_type', 'work')
                  ->whereNotNull('session_duration')
                  ->where('session_duration', '>', 0);
        }], 'session_duration')
        ->orderBy('attendances_sum_session_duration', 'desc')
        ->limit(5)
        ->get(['id', 'name']);

        foreach ($leaderboard as $index => $user) {
            $formatted = \App\Helpers\TimeHelper::formatDuration($user->attendances_sum_session_duration);
            $this->line("  " . ($index + 1) . ". {$user->name}: {$formatted}");
        }
    }
}

