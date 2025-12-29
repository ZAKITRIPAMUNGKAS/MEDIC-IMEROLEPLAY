<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payroll;
use App\Models\PayrollNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeletePayrollByPeriodCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:delete-by-period 
                            {--start-date= : Start date (Y-m-d format, default: 2025-11-24)}
                            {--end-date= : End date (Y-m-d format, default: 2025-11-30)}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force deletion even if payroll is paid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete payroll records within a specific date range (default: 24 Nov 2025 - 30 Nov 2025)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Default dates: 24 Nov 2025 - 30 Nov 2025
        $startDate = $this->option('start-date') ? Carbon::parse($this->option('start-date')) : Carbon::parse('2025-11-24');
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date')) : Carbon::parse('2025-11-30');

        $this->info('🗑️  Starting payroll deletion by period...');
        $this->info("📅 Period: {$startDate->format('d M Y')} to {$endDate->format('d M Y')}");
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be deleted');
        }

        // Find payrolls within the date range
        // Check both period_start and period_end to catch any overlapping periods
        $payrolls = Payroll::where(function($query) use ($startDate, $endDate) {
                // Payrolls that start within the range
                $query->whereBetween('period_start', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      // Or payrolls that end within the range
                      ->orWhereBetween('period_end', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      // Or payrolls that span across the range
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('period_start', '<=', $startDate->format('Y-m-d'))
                            ->where('period_end', '>=', $endDate->format('Y-m-d'));
                      });
            })
            ->with('user')
            ->orderBy('period_start')
            ->orderBy('user_id')
            ->get();

        if ($payrolls->isEmpty()) {
            $this->info('✅ No payroll records found in the specified period.');
            return 0;
        }

        $this->info("📋 Found {$payrolls->count()} payroll record(s) in the specified period");

        $totalDeleted = 0;
        $skippedPaid = 0;
        $errors = [];
        $deletedDetails = [];

        // Show what will be deleted
        $this->newLine();
        $this->info('📊 Payrolls to be deleted:');
        $this->table(
            ['ID', 'User ID', 'User Name', 'Period Start', 'Period End', 'Status', 'Salary', 'Created At'],
            $payrolls->map(function($payroll) {
                return [
                    $payroll->id,
                    $payroll->user_id,
                    isset($payroll->user->name) ? $payroll->user->name : 'N/A',
                    $payroll->period_start->format('Y-m-d'),
                    $payroll->period_end->format('Y-m-d'),
                    $payroll->status,
                    $payroll->formatted_salary,
                    $payroll->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray()
        );

        if (!$dryRun) {
            if (!$this->confirm('Are you sure you want to delete these payroll records? This action cannot be undone!', false)) {
                $this->warn('❌ Deletion cancelled.');
                return 0;
            }
        }

        DB::beginTransaction();
        try {
            foreach ($payrolls as $payroll) {
                // Check if payroll is paid
                if ($payroll->isPaid() && !$force) {
                    $this->warn("⚠️  Skipping Payroll ID {$payroll->id} - already paid (use --force to delete)");
                    $skippedPaid++;
                    continue;
                }

                if ($dryRun) {
                    $userName = isset($payroll->user->name) ? $payroll->user->name : 'N/A';
                    $this->line("   [DRY RUN] Would delete: Payroll ID {$payroll->id} - {$userName} ({$payroll->period_start->format('Y-m-d')} to {$payroll->period_end->format('Y-m-d')})");
                    $deletedDetails[] = [
                        'id' => $payroll->id,
                        'user_id' => $payroll->user_id,
                        'user_name' => isset($payroll->user->name) ? $payroll->user->name : 'N/A',
                        'period' => "{$payroll->period_start->format('Y-m-d')} to {$payroll->period_end->format('Y-m-d')}",
                        'status' => $payroll->status,
                        'salary' => $payroll->formatted_salary,
                        'created_at' => $payroll->created_at->format('Y-m-d H:i:s'),
                    ];
                    $totalDeleted++;
                } else {
                    try {
                        // Delete related notifications first
                        $notificationCount = $payroll->notifications()->count();
                        $payroll->notifications()->delete();
                        
                        // Delete the payroll
                        $payroll->delete();
                        
                        $userName = isset($payroll->user->name) ? $payroll->user->name : 'N/A';
                        $this->line("   ✅ Deleted: Payroll ID {$payroll->id} - {$userName} ({$payroll->period_start->format('Y-m-d')} to {$payroll->period_end->format('Y-m-d')}) - {$notificationCount} notification(s) deleted");
                        $deletedDetails[] = [
                            'id' => $payroll->id,
                            'user_id' => $payroll->user_id,
                            'user_name' => isset($payroll->user->name) ? $payroll->user->name : 'N/A',
                            'period' => "{$payroll->period_start->format('Y-m-d')} to {$payroll->period_end->format('Y-m-d')}",
                            'status' => $payroll->status,
                            'salary' => $payroll->formatted_salary,
                            'created_at' => $payroll->created_at->format('Y-m-d H:i:s'),
                        ];
                        $totalDeleted++;
                    } catch (\Exception $e) {
                        $error = "Error deleting Payroll ID {$payroll->id}: " . $e->getMessage();
                        $errors[] = $error;
                        $this->error("   ❌ {$error}");
                    }
                }
            }

            if ($dryRun) {
                DB::rollBack();
                $this->warn('⚠️  DRY RUN - Transaction rolled back, no changes made');
            } else {
                DB::commit();
            }

            // Summary
            $this->newLine();
            $this->info('📊 Summary:');
            $this->info("🗑️  Deleted: {$totalDeleted} payroll record(s)");
            if ($skippedPaid > 0) {
                $this->warn("⚠️  Skipped (paid): {$skippedPaid} payroll record(s)");
            }
            
            if (!empty($errors)) {
                $this->error("❌ Errors: " . count($errors));
                foreach ($errors as $error) {
                    $this->error("   - {$error}");
                }
            }

            // Show detailed table if there are deleted records
            if (!empty($deletedDetails)) {
                $this->newLine();
                $this->info('📋 Deleted Records Details:');
                $this->table(
                    ['ID', 'User ID', 'User Name', 'Period', 'Status', 'Salary', 'Created At'],
                    array_map(function($detail) {
                        return [
                            $detail['id'],
                            $detail['user_id'],
                            $detail['user_name'],
                            $detail['period'],
                            $detail['status'],
                            $detail['salary'],
                            $detail['created_at'],
                        ];
                    }, $deletedDetails)
                );
            }

            if ($dryRun) {
                $this->warn('⚠️  This was a DRY RUN. Run without --dry-run to actually delete the records.');
            } else {
                $this->info('🎉 Deletion completed!');
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Transaction failed: ' . $e->getMessage());
            return 1;
        }
    }
}

