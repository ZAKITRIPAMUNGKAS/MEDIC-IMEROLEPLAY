<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use Carbon\Carbon;

class AttendanceMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:monitor 
                            {--long-duty=8 : Hours threshold for long duty alert}
                            {--daily-summary : Send daily summary}
                            {--weekly-report : Send weekly report}
                            {--check-all : Run all monitoring checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor attendance and send notifications';

    public function __construct()
    {
        // NotificationService removed for better performance
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting attendance monitoring...');

        if ($this->option('check-all') || $this->option('long-duty')) {
            $this->checkLongDutyPlayers();
        }

        if ($this->option('check-all') || $this->option('daily-summary')) {
            $this->generateDailySummary();
        }

        if ($this->option('check-all') || $this->option('weekly-report')) {
            $this->generateWeeklyReport();
        }

        $this->info('Attendance monitoring completed.');
    }

    /**
     * Check for long duty players
     */
    private function checkLongDutyPlayers()
    {
        $threshold = $this->option('long-duty');
        $this->info("Checking for players on duty longer than {$threshold} hours...");

        // NotificationService removed - skip check
        $this->info("Long duty check skipped (NotificationService removed).");
    }

    /**
     * Generate daily summary
     */
    private function generateDailySummary()
    {
        $this->info('Generating daily summary...');
        
        // NotificationService removed - skip daily summary
        $this->info('Daily summary skipped (NotificationService removed).');
    }

    /**
     * Generate weekly report
     */
    private function generateWeeklyReport()
    {
        $this->info('Generating weekly report...');
        
        // NotificationService removed - skip weekly report
        $this->info('Weekly report skipped (NotificationService removed).');
    }
}
