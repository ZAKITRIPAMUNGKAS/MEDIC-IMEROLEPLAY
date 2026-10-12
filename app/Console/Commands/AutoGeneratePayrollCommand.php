<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\PayrollNotification;
use App\Helpers\PayrollHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoGeneratePayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:auto-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate payroll for last week (runs every Monday)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting automatic payroll generation for last week...');

        // Generate for last week (Monday to Sunday)
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $this->info("📅 Period: {$lastWeekStart->format('Y-m-d')} to {$lastWeekEnd->format('Y-m-d')}");

        // Block auto-generation if there are pending meeting requests for last week
        $pendingMeetingsCount = \App\Models\MeetingRequest::countPendingInPeriod(
            $lastWeekStart->toDateString(),
            $lastWeekEnd->toDateString()
        );

        if ($pendingMeetingsCount > 0) {
            $this->warn("⚠️  Auto-generate dibatalkan: Terdapat {$pendingMeetingsCount} pengajuan meeting (Meeting Request) yang masih berstatus pending untuk periode {$lastWeekStart->format('Y-m-d')} s/d {$lastWeekEnd->format('Y-m-d')}. Semua meeting request harus di-ACC atau ditolak terlebih dahulu.");
            return 1;
        }

        // Get users to process - ordered by name
        $users = User::where('is_active', true)
            ->whereHas('role')
            ->orderBy('name')
            ->get();

        if ($users->isEmpty()) {
            $this->warn('⚠️  No active users found with roles.');
            return 0;
        }

        $this->info("👥 Processing {$users->count()} users...");

        $generatedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                try {
                    // Check if payroll already exists for this user in this week
                    $existingPayroll = Payroll::where('user_id', $user->id)
                        ->where(function ($q) use ($lastWeekStart, $lastWeekEnd) {
                            $q->where(function ($exact) use ($lastWeekStart, $lastWeekEnd) {
                                $exact->where('period_start', $lastWeekStart->toDateString())
                                      ->where('period_end', $lastWeekEnd->toDateString());
                            })
                            ->orWhere('period_start', $lastWeekStart->toDateString())
                            ->orWhereBetween('period_start', [
                                $lastWeekStart->copy()->startOfWeek()->toDateString(),
                                $lastWeekStart->copy()->endOfWeek()->toDateString()
                            ]);
                        })
                        ->orderByRaw("CASE WHEN status = 'paid' THEN 0 ELSE 1 END")
                        ->first();

                    // Get attendance data for the period
                    $attendances = Attendance::where('user_id', $user->id)
                        ->whereBetween('work_date', [
                            $lastWeekStart->toDateString(), 
                            $lastWeekEnd->toDateString()
                        ])
                        ->whereIn('session_type', ['work', 'meeting'])
                        ->whereNotNull('session_duration')
                        ->where('session_duration', '>', 0)
                        ->where('is_active', false) // Only completed sessions
                        ->get();

                    if ($attendances->isEmpty()) {
                        $this->warn("⚠️  No attendance data for {$user->name} in this period");
                        $skippedCount++;
                        continue;
                    }

                    // Calculate total hours and salary
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

                    // Skip if calculated salary is 0 or less
                    if ($calculatedSalary <= 0) {
                        $this->warn("⏭️  Skipping {$user->name} - salary is 0");
                        $skippedCount++;
                        continue;
                    }

                    // Prepare payroll data
                    $payrollData = [
                        'user_id' => $user->id,
                        'period_start' => $lastWeekStart->format('Y-m-d'),
                        'period_end' => $lastWeekEnd->format('Y-m-d'),
                        'total_hours' => $totalHours,
                        'base_salary' => $baseSalary,
                        'calculated_salary' => $calculatedSalary,
                        'notes' => "Auto-generated on " . now()->format('Y-m-d H:i:s'),
                    ];

                    if ($existingPayroll) {
                        // Check if payroll is already paid before update
                        $wasPaid = $existingPayroll->isPaid();
                        
                        // Update existing payroll
                        if (!$wasPaid) {
                            $payrollData['status'] = 'pending';
                            $existingPayroll->update($payrollData);
                            $payroll = $existingPayroll->fresh();
                            
                            // Send notification for updated payroll
                            $this->sendSalaryPendingNotification($payroll, true);
                            
                            $this->info("🔄 Updated payroll for {$user->name}: {$calculatedSalary} ({$totalHours}h)");
                            $updatedCount++;
                        } else {
                            $this->warn("⏭️  Skipping {$user->name} - payroll already paid");
                            $skippedCount++;
                        }
                    } else {
                        // Create new payroll record
                        $payrollData['status'] = 'pending';
                        $payroll = Payroll::create($payrollData);
                        
                        // Send notification
                        $this->sendSalaryPendingNotification($payroll, false);
                        
                        $this->info("✅ Generated payroll for {$user->name}: {$calculatedSalary} ({$totalHours}h)");
                        $generatedCount++;
                    }

                } catch (\Exception $e) {
                    $error = "Error processing {$user->name}: " . $e->getMessage();
                    $errors[] = $error;
                    $this->error("❌ {$error}");
                }
            }

            DB::commit();

            // Summary
            $this->newLine();
            $this->info('📊 Summary:');
            $this->info("✅ Generated: {$generatedCount}");
            if ($updatedCount > 0) {
                $this->info("🔄 Updated: {$updatedCount}");
            }
            $this->info("⏭️  Skipped: {$skippedCount}");
            
            if (!empty($errors)) {
                $this->error("❌ Errors: " . count($errors));
                foreach ($errors as $error) {
                    $this->error("   - {$error}");
                }
            }

            $this->info('🎉 Automatic payroll generation completed!');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Transaction failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Send salary pending notification when payroll is generated or updated
     */
    private function sendSalaryPendingNotification(Payroll $payroll, $isUpdate = false)
    {
        try {
            $message = $isUpdate 
                ? "Gaji Anda untuk periode {$payroll->period_description} telah di-update sebesar {$payroll->formatted_salary}. Status: Pending"
                : "Gaji Anda untuk periode {$payroll->period_description} telah di-generate sebesar {$payroll->formatted_salary}. Status: Pending";

            // Check if pending notification already exists for this payroll
            $existing = PayrollNotification::where('payroll_id', $payroll->id)
                ->where('notification_type', 'salary_pending')
                ->latest()
                ->first();

            if ($existing) {
                $existingAmount = isset($existing->metadata['amount']) ? (float)$existing->metadata['amount'] : null;
                $currentAmount = (float)$payroll->calculated_salary;

                // If identical amount and message, skip creating redundant notification
                if ($existingAmount === $currentAmount && $existing->message === $message) {
                    return;
                }

                // If amount changed or message updated, update existing notification
                $existing->update([
                    'message' => $message,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'metadata' => [
                        'amount' => $payroll->calculated_salary,
                        'period' => $payroll->period_description,
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                        'is_update' => true,
                        'is_auto_generated' => true,
                    ],
                ]);
                return;
            }

            // Create notification record if none exists
            $notification = PayrollNotification::create([
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'notification_type' => 'salary_pending',
                'message' => $message,
                'metadata' => [
                    'amount' => $payroll->calculated_salary,
                    'period' => $payroll->period_description,
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'is_update' => $isUpdate,
                    'is_auto_generated' => true,
                ],
            ]);

            // Mark notification as sent
            $notification->markAsSent();

        } catch (\Exception $e) {
            \Log::error('Failed to send salary pending notification', [
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

