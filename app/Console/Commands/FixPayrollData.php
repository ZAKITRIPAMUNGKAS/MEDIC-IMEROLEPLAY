<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Helpers\PayrollHelper;
use Illuminate\Support\Facades\DB;

class FixPayrollData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:payroll-data {--dry-run : Only show what would be done without changing data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing payrolls for Jan 5-11 and ensure RH staff hospital assignment.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Payroll Data Fix...");
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn("[DRY RUN] No changes will be saved to database.");
        }

        // 1. Fix Axel Williams Hospital
        $this->fixHospitalAssignments($dryRun);

        // 2. Generate Missing Payrolls for Jan 5-11
        $this->generateMissingPayrolls($dryRun);

        $this->info("Fix complete.");
    }

    private function fixHospitalAssignments($dryRun)
    {
        $this->line("\n--- 1. Fixing RH Hospital Assignments ---");

        // Target specific RH users known to have issues
        // Main target: RH - Axel Williams (ID 468 locally, might differ on prod)
        // Search by name
        $users = User::where('name', 'like', 'RH -%')
            ->orWhere('name', 'like', '%Axel%Williams%')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            // Check if name implies RH but hospital is not roxwood
            // Valid RH names often start with "RH -" or are known staff

            // Logic: If Name contains "RH -" OR is "Axel Williams", set to Roxwood
            $isTarget = str_contains($user->name, 'RH -') || str_contains($user->name, 'Axel Williams');

            if ($isTarget && $user->hospital !== 'roxwood') {
                $this->info("Fixing User: [{$user->id}] {$user->name} ({$user->hospital} -> roxwood)");
                if (!$dryRun) {
                    $user->hospital = 'roxwood';
                    $user->save();
                }
                $count++;
            }
        }

        if ($count == 0) {
            $this->line("No incorrect hospital assignments found.");
        } else {
            $this->info("Updated $count users to Roxwood.");
        }
    }

    private function generateMissingPayrolls($dryRun)
    {
        $startDate = '2026-01-05';
        $endDate = '2026-01-11';

        $this->line("\n--- 2. Checking Missing Payrolls ({$startDate} - {$endDate}) ---");

        // Get users with attendance but no payroll
        $usersWithAttendance = Attendance::whereBetween('work_date', [$startDate, $endDate])
            ->whereIn('session_type', ['work', 'meeting'])
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->distinct()
            ->pluck('user_id');

        $this->line("Found " . $usersWithAttendance->count() . " users with attendance.");

        $fixedCount = 0;

        foreach ($usersWithAttendance as $userId) {
            // Check if payroll exists
            $exists = Payroll::where('user_id', $userId)
                ->where('period_start', $startDate)
                ->exists();

            if ($exists)
                continue;

            $user = User::find($userId);
            if (!$user)
                continue;

            $this->info("Found MISSING payroll for: [{$user->id}] {$user->name}");

            // Calculate details
            $totalSeconds = Attendance::where('user_id', $userId)
                ->whereBetween('work_date', [$startDate, $endDate])
                ->whereIn('session_type', ['work', 'meeting'])
                ->where('session_duration', '>', 0)
                ->where('is_active', false)
                ->sum('session_duration');

            $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
            $roleName = $user->role ? $user->role->name : 'Staff';
            $customSalary = $user->custom_salary ?? 0;
            $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
            $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

            $this->line("   -> Hours: {$totalHours}, Salary: {$calculatedSalary}");

            if (!$dryRun) {
                try {
                    Payroll::create([
                        'user_id' => $user->id,
                        'period_start' => $startDate,
                        'period_end' => $endDate,
                        'total_hours' => $totalHours,
                        'base_salary' => $baseSalary,
                        'calculated_salary' => $calculatedSalary,
                        'status' => 'pending',
                        'notes' => "Auto-generated by artisan fix:payroll-data"
                    ]);
                    $fixedCount++;
                } catch (\Exception $e) {
                    $this->error("   -> Failed: " . $e->getMessage());
                }
            } else {
                $fixedCount++; // Count as found/fixed for dry run report
            }
        }

        $this->info("Generated $fixedCount missing payroll records.");
    }
}
