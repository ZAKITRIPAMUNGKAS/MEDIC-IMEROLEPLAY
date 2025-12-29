<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Helpers\PayrollHelper;
use Carbon\Carbon;

class GeneratePayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:generate 
                            {--period=week : Period to generate payroll for (week, month)}
                            {--start-date= : Start date (Y-m-d format)}
                            {--end-date= : End date (Y-m-d format)}
                            {--user-id= : Specific user ID to generate payroll for}
                            {--force : Force regenerate existing payrolls}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate payroll for staff based on attendance data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting payroll generation...');

        // Determine period
        $period = $this->option('period');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $userId = $this->option('user-id');
        $force = $this->option('force');

        // Calculate dates
        if ($startDate && $endDate) {
            $periodStart = Carbon::parse($startDate);
            $periodEnd = Carbon::parse($endDate);
        } else {
            switch ($period) {
                case 'week':
                    $periodStart = now()->startOfWeek();
                    $periodEnd = now()->endOfWeek();
                    break;
                case 'month':
                    $periodStart = now()->startOfMonth();
                    $periodEnd = now()->endOfMonth();
                    break;
                default:
                    $this->error('Invalid period. Use: week, month');
                    return 1;
            }
        }

        $this->info("📅 Period: {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");

        // Get users to process
        $users = User::where('is_active', true)
            ->whereHas('role')
            ->when($userId, function($query) use ($userId) {
                return $query->where('id', $userId);
            })
            ->get();

        if ($users->isEmpty()) {
            $this->warn('⚠️  No active users found with roles.');
            return 0;
        }

        $this->info("👥 Processing {$users->count()} users...");

        $generatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                // Check if payroll already exists
                $existingPayroll = Payroll::where('user_id', $user->id)
                    ->where('period_start', $periodStart->format('Y-m-d'))
                    ->where('period_end', $periodEnd->format('Y-m-d'))
                    ->first();

                if ($existingPayroll && !$force) {
                    $this->warn("⏭️  Skipping {$user->name} - payroll already exists");
                    $skippedCount++;
                    continue;
                }

                // Get attendance data
                $attendances = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', [$periodStart, $periodEnd])
                    ->get();

                if ($attendances->isEmpty()) {
                    $this->warn("⚠️  No attendance data for {$user->name} in this period");
                    $skippedCount++;
                    continue;
                }

                // Calculate salary
                $totalSeconds = $attendances->sum('session_duration');
                $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
                $roleName = optional($user->role)->name;
                $baseSalary = PayrollHelper::getBaseSalary($roleName);
                $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds);

                // Skip if calculated salary is 0 or less
                if ($calculatedSalary <= 0) {
                    $this->warn("⏭️  Skipping {$user->name} - salary is 0");
                    $skippedCount++;
                    continue;
                }

                // Create or update payroll
                $payrollData = [
                    'user_id' => $user->id,
                    'period_start' => $periodStart->format('Y-m-d'),
                    'period_end' => $periodEnd->format('Y-m-d'),
                    'total_hours' => $totalHours,
                    'base_salary' => $baseSalary,
                    'calculated_salary' => $calculatedSalary,
                    'status' => 'pending',
                    'notes' => "Generated via command on " . now()->format('Y-m-d H:i:s'),
                ];

                if ($existingPayroll && $force) {
                    $existingPayroll->update($payrollData);
                    $this->info("🔄 Updated payroll for {$user->name}: {$calculatedSalary} ({$totalHours}h)");
                } else {
                    Payroll::create($payrollData);
                    $this->info("✅ Generated payroll for {$user->name}: {$calculatedSalary} ({$totalHours}h)");
                }

                $generatedCount++;

            } catch (\Exception $e) {
                $error = "Error processing {$user->name}: " . $e->getMessage();
                $errors[] = $error;
                $this->error("❌ {$error}");
            }
        }

        // Summary
        $this->newLine();
        $this->info('📊 Summary:');
        $this->info("✅ Generated: {$generatedCount}");
        $this->info("⏭️  Skipped: {$skippedCount}");
        
        if (!empty($errors)) {
            $this->error("❌ Errors: " . count($errors));
            foreach ($errors as $error) {
                $this->error("   - {$error}");
            }
        }

        $this->info('🎉 Payroll generation completed!');
        return 0;
    }
}
