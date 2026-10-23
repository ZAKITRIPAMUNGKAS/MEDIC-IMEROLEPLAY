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

        // Block generation if there are pending meeting requests for this period
        $pendingMeetingsCount = \App\Models\MeetingRequest::countPendingInPeriod(
            $periodStart->toDateString(),
            $periodEnd->toDateString(),
            $userId
        );

        if ($pendingMeetingsCount > 0) {
            $this->error("❌ Gagal generate gaji: Ditemukan {$pendingMeetingsCount} pengajuan meeting (Meeting Request) yang masih berstatus pending untuk periode ini. Harap ACC atau tolak semua meeting request terlebih dahulu.");
            return 1;
        }

        // Auto-heal attendances where clock_out is not null but is_active is true
        Attendance::whereNotNull('clock_out')
            ->where('is_active', true)
            ->where(function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                  ->orWhere(function ($sub) use ($periodStart, $periodEnd) {
                      $sub->whereDate('clock_in', '>=', $periodStart->toDateString())
                          ->whereDate('clock_in', '<=', $periodEnd->toDateString());
                  });
            })
            ->update(['is_active' => false]);

        // Get users to process
        $users = User::where(function($query) {
                $query->where('is_active', true)
                      ->orWhereNotNull('role_id')
                      ->orWhereNotNull('medic_role_id');
            })
            ->where(function($query) {
                $query->whereHas('role')
                      ->orWhereHas('medicRole')
                      ->orWhereNotNull('medic_role_id')
                      ->orWhereNotNull('role_id');
            })
            ->when($userId, function($query) use ($userId) {
                return $query->where('id', $userId);
            })
            ->get();

        // Also fetch any users who worked in this period even if inactive or role is unusual
        $attendanceUserIds = Attendance::where(function ($sub) use ($periodStart, $periodEnd) {
                $sub->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                    ->orWhere(function ($cin) use ($periodStart, $periodEnd) {
                        $cin->whereDate('clock_in', '>=', $periodStart->toDateString())
                            ->whereDate('clock_in', '<=', $periodEnd->toDateString());
                    });
            })
            ->where(function ($st) {
                $st->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                   ->orWhereNull('session_type');
            })
            ->where(function ($act) {
                $act->where('is_active', false)
                    ->orWhereNotNull('clock_out');
            })
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->when($userId, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->distinct()
            ->pluck('user_id');

        if ($attendanceUserIds->isNotEmpty()) {
            $extraUsers = User::whereIn('id', $attendanceUserIds)
                ->when($userId, function($query) use ($userId) {
                    return $query->where('id', $userId);
                })
                ->get();
            $users = $users->merge($extraUsers)->unique('id')->sortBy('name');
        }

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
                    ->where(function ($q) use ($periodStart, $periodEnd) {
                        $q->where(function ($exact) use ($periodStart, $periodEnd) {
                            $exact->where('period_start', $periodStart->toDateString())
                                  ->where('period_end', $periodEnd->toDateString());
                        })
                        ->orWhere('period_start', $periodStart->toDateString())
                        ->orWhereBetween('period_start', [
                            $periodStart->copy()->startOfWeek()->toDateString(),
                            $periodStart->copy()->endOfWeek()->toDateString()
                        ]);
                    })
                    ->orderByRaw("CASE WHEN status = 'paid' THEN 0 ELSE 1 END")
                    ->first();

                if ($existingPayroll && !$force) {
                    $this->warn("⏭️  Skipping {$user->name} - payroll already exists");
                    $skippedCount++;
                    continue;
                }

                // Get attendance data
                $attendances = Attendance::where('user_id', $user->id)
                    ->where(function ($sub) use ($periodStart, $periodEnd) {
                        $sub->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                            ->orWhere(function ($cin) use ($periodStart, $periodEnd) {
                                $cin->whereDate('clock_in', '>=', $periodStart->toDateString())
                                    ->whereDate('clock_in', '<=', $periodEnd->toDateString());
                            });
                    })
                    ->where(function ($st) {
                        $st->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                           ->orWhereNull('session_type');
                    })
                    ->where(function ($act) {
                        $act->where('is_active', false)
                            ->orWhereNotNull('clock_out');
                    })
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
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
                if (!$roleName && $user->effective_medic_role) {
                    $roleName = $user->effective_medic_role->name;
                }
                if (!$roleName && $user->medicRole) {
                    $roleName = $user->medicRole->name;
                }

                $customSalary = $user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);

                if ($baseSalary <= 0 && $user->effective_medic_role && $user->effective_medic_role->name !== $roleName) {
                    $altRole = $user->effective_medic_role->name;
                    $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                    if ($altBase > 0) {
                        $roleName = $altRole;
                        $baseSalary = $altBase;
                    }
                }

                $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

                // Fallback if calculated salary is 0 or less but hours exist
                if ($calculatedSalary <= 0 && $totalHours > 0) {
                    $fallbackBase = PayrollHelper::getBaseSalary(null, $customSalary);
                    $calculatedSalary = PayrollHelper::computeWeeklySalary(null, $totalSeconds, $customSalary);
                    if ($baseSalary <= 0) {
                        $baseSalary = $fallbackBase;
                    }
                }

                // Skip if calculated salary is still 0 or less
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
