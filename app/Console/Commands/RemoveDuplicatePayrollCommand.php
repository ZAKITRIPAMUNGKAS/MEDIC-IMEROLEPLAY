<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payroll;
use App\Models\PayrollNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RemoveDuplicatePayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:remove-duplicates 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force deletion even if payroll is paid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate payroll records, keeping only the latest one for each user and period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔍 Starting duplicate payroll removal process...');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be deleted');
        }

        // Find duplicates: same user_id, period_start, and period_end
        // Group by these fields and find duplicates
        $duplicates = Payroll::select('user_id', 'period_start', 'period_end', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id', 'period_start', 'period_end')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate payroll records found.');
            return 0;
        }

        $this->info("📋 Found {$duplicates->count()} duplicate groups");

        $totalDeleted = 0;
        $totalKept = 0;
        $errors = [];
        $deletedDetails = [];

        DB::beginTransaction();
        try {
            foreach ($duplicates as $duplicate) {
                // Get all payrolls for this user and period
                $payrolls = Payroll::where('user_id', $duplicate->user_id)
                    ->where('period_start', $duplicate->period_start)
                    ->where('period_end', $duplicate->period_end)
                    ->orderBy('created_at', 'desc') // Order by created_at descending (newest first)
                    ->orderBy('id', 'desc') // Also order by id as secondary sort
                    ->get();

                if ($payrolls->count() <= 1) {
                    continue; // Skip if only one record (shouldn't happen, but just in case)
                }

                // Keep the first one (newest), delete the rest
                $keepPayroll = $payrolls->first();
                $deletePayrolls = $payrolls->skip(1);

                $this->info("👤 User ID: {$duplicate->user_id}, Period: {$duplicate->period_start} to {$duplicate->period_end}");
                $this->info("   Keeping: Payroll ID {$keepPayroll->id} (created: {$keepPayroll->created_at})");
                $this->info("   Status: {$keepPayroll->status}, Salary: {$keepPayroll->formatted_salary}");

                foreach ($deletePayrolls as $payroll) {
                    // Check if payroll is paid
                    if ($payroll->isPaid() && !$force) {
                        $this->warn("   ⚠️  Skipping Payroll ID {$payroll->id} - already paid (use --force to delete)");
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("   [DRY RUN] Would delete: Payroll ID {$payroll->id} (created: {$payroll->created_at})");
                        $deletedDetails[] = [
                            'id' => $payroll->id,
                            'user_id' => $payroll->user_id,
                            'user_name' => $payroll->user->name ?? 'N/A',
                            'period' => "{$payroll->period_start} to {$payroll->period_end}",
                            'status' => $payroll->status,
                            'created_at' => $payroll->created_at,
                        ];
                        $totalDeleted++;
                    } else {
                        try {
                            // Delete related notifications first
                            $payroll->notifications()->delete();
                            
                            // Delete the payroll
                            $payroll->delete();
                            
                            $this->line("   ✅ Deleted: Payroll ID {$payroll->id} (created: {$payroll->created_at})");
                            $deletedDetails[] = [
                                'id' => $payroll->id,
                                'user_id' => $payroll->user_id,
                                'user_name' => $payroll->user->name ?? 'N/A',
                                'period' => "{$payroll->period_start} to {$payroll->period_end}",
                                'status' => $payroll->status,
                                'created_at' => $payroll->created_at,
                            ];
                            $totalDeleted++;
                        } catch (\Exception $e) {
                            $error = "Error deleting Payroll ID {$payroll->id}: " . $e->getMessage();
                            $errors[] = $error;
                            $this->error("   ❌ {$error}");
                        }
                    }
                }
                $totalKept++;
                $this->newLine();
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
            $this->info("✅ Kept: {$totalKept} payroll records (latest for each duplicate group)");
            $this->info("🗑️  Deleted: {$totalDeleted} duplicate payroll records");
            
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
                    ['ID', 'User ID', 'User Name', 'Period', 'Status', 'Created At'],
                    array_map(function($detail) {
                        return [
                            $detail['id'],
                            $detail['user_id'],
                            $detail['user_name'],
                            $detail['period'],
                            $detail['status'],
                            $detail['created_at'],
                        ];
                    }, $deletedDetails)
                );
            }

            if ($dryRun) {
                $this->warn('⚠️  This was a DRY RUN. Run without --dry-run to actually delete the records.');
            } else {
                $this->info('🎉 Duplicate removal completed!');
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Transaction failed: ' . $e->getMessage());
            return 1;
        }
    }
}

