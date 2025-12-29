<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Illuminate\Console\Command;

class FixAttendanceDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:fix 
                            {--cross-day : Fix cross-day sessions}
                            {--inconsistent : Fix inconsistent duration data}
                            {--all : Fix all issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix attendance data issues (cross-day sessions, inconsistent duration, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting Attendance Data Fix...');
        $this->newLine();
        
        $fixAll = $this->option('all');
        $totalFixed = 0;
        
        // Fix inconsistent data
        if ($this->option('inconsistent') || $fixAll) {
            $this->info('📊 Fixing inconsistent duration data...');
            $fixed = Attendance::fixInconsistentData();
            $this->info("✅ Fixed {$fixed} inconsistent records");
            $totalFixed += $fixed;
            $this->newLine();
        }
        
        // Fix cross-day sessions
        if ($this->option('cross-day') || $fixAll) {
            $this->info('📅 Fixing cross-day sessions...');
            $fixed = Attendance::fixCrossDaySessions();
            $this->info("✅ Fixed {$fixed} cross-day sessions");
            $totalFixed += $fixed;
            $this->newLine();
        }
        
        if ($totalFixed === 0) {
            $this->warn('No issues found or no options specified.');
            $this->info('Use --all to fix all issues, or specify individual options:');
            $this->info('  --inconsistent : Fix duration calculation issues');
            $this->info('  --cross-day    : Fix sessions that cross midnight');
        } else {
            $this->info("🎉 Total records fixed: {$totalFixed}");
        }
        
        return 0;
    }
}

