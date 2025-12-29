<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class FixAttendanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:fix {--cross-day : Fix cross-day sessions} {--inconsistent : Fix inconsistent data} {--all : Fix all issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix attendance data issues (inconsistent durations, cross-day sessions)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Memulai perbaikan data attendance...');

        $fixedInconsistent = 0;
        $fixedCrossDay = 0;
        $errors = 0;

        // Fix inconsistent data
        if ($this->option('all') || $this->option('inconsistent')) {
            $this->info('Memperbaiki data yang tidak konsisten...');
            $result = Attendance::fixInconsistentData();
            $fixedInconsistent = $result['fixed'];
            $errors += $result['errors'];
            $this->info("Data tidak konsisten diperbaiki: {$fixedInconsistent} record");
        }

        // Fix cross-day sessions
        if ($this->option('all') || $this->option('cross-day')) {
            $this->info('Memperbaiki sesi lintas hari...');
            $result = Attendance::fixCrossDaySessions();
            $fixedCrossDay = $result['fixed'];
            $errors += $result['errors'];
            $this->info("Sesi lintas hari diperbaiki: {$fixedCrossDay} session");
        }

        $this->info('Perbaikan selesai!');
        $this->info("Total data diperbaiki: " . ($fixedInconsistent + $fixedCrossDay));
        $this->info("Total error: {$errors}");

        return 0;
    }
}
