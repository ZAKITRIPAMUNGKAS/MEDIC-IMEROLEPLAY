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
    protected $description = 'Remove duplicate payroll records, keeping only the best/standard record for each user and week';

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

        // Fetch all payrolls and group by user_id and week start
        $allPayrolls = Payroll::with('user')->get();
        
        $grouped = $allPayrolls->groupBy(function ($payroll) {
            $weekStart = Carbon::parse($payroll->period_start)->startOfWeek()->format('Y-m-d');
            return $payroll->user_id . '_' . $weekStart;
        });

        $duplicateGroups = $grouped->filter(function ($group) {
            return $group->count() > 1;
        });

        if ($duplicateGroups->isEmpty()) {
            $this->info('✅ No duplicate payroll records found.');
            return 0;
        }

        $this->info("📋 Found {$duplicateGroups->count()} duplicate week groups");

        $totalDeleted = 0;
        $totalKept = 0;
        $errors = [];
        $deletedDetails = [];

        DB::beginTransaction();
        try {
            foreach ($duplicateGroups as $key => $payrolls) {
                $user = $payrolls->first()->user;
                $userName = $user->name ?? 'User #' . $payrolls->first()->user_id;

                // Sort payrolls to pick the primary one to keep:
                // 1. Paid status comes first (never discard already paid payroll)
                // 2. Standard week bounds (Monday to Sunday) preferred
                // 3. Newest ID
                $sorted = $payrolls->sort(function ($a, $b) {
                    if ($a->isPaid() && !$b->isPaid()) return -1;
                    if (!$a->isPaid() && $b->isPaid()) return 1;

                    $weekStart = Carbon::parse($a->period_start)->startOfWeek()->toDateString();
                    $weekEnd = Carbon::parse($a->period_start)->endOfWeek()->toDateString();

                    $aStandard = (Carbon::parse($a->period_start)->toDateString() === $weekStart && Carbon::parse($a->period_end)->toDateString() === $weekEnd);
                    $bStandard = (Carbon::parse($b->period_start)->toDateString() === $weekStart && Carbon::parse($b->period_end)->toDateString() === $weekEnd);

                    if ($aStandard && !$bStandard) return -1;
                    if (!$aStandard && $bStandard) return 1;

                    return $b->id <=> $a->id;
                })->values();

                $keepPayroll = $sorted->first();
                $deletePayrolls = $sorted->slice(1);

                $weekStartStr = Carbon::parse($keepPayroll->period_start)->startOfWeek()->format('d M Y');
                $weekEndStr = Carbon::parse($keepPayroll->period_start)->endOfWeek()->format('d M Y');

                $this->info("👤 {$userName} (Minggu: {$weekStartStr} - {$weekEndStr}):");
                $this->info("   ✅ Menjaga: ID #{$keepPayroll->id} | Periode: {$keepPayroll->period_start} s/d {$keepPayroll->period_end} | Status: {$keepPayroll->status} | {$keepPayroll->formatted_salary}");

                // Normalize period_end if it was non-standard (e.g. 2026-10-05 instead of Sunday 2026-10-04)
                $correctEnd = Carbon::parse($keepPayroll->period_start)->endOfWeek()->toDateString();
                if (Carbon::parse($keepPayroll->period_end)->toDateString() !== $correctEnd && !$keepPayroll->isPaid()) {
                    if (!$dryRun) {
                        $keepPayroll->update(['period_end' => $correctEnd]);
                    }
                    $this->line("   ℹ️  Normalisasi tanggal akhir ID #{$keepPayroll->id} menjadi {$correctEnd}");
                }

                foreach ($deletePayrolls as $payroll) {
                    // Check if payroll is paid
                    if ($payroll->isPaid() && !$force) {
                        $this->warn("   ⚠️  Melewati ID #{$payroll->id} - sudah dibayar (gunakan --force untuk menghapus)");
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("   [DRY RUN] Akan menghapus duplikat: ID #{$payroll->id} | Periode: {$payroll->period_start} s/d {$payroll->period_end} | Status: {$payroll->status}");
                        $deletedDetails[] = [
                            'id' => $payroll->id,
                            'user_name' => $userName,
                            'period' => "{$payroll->period_start} to {$payroll->period_end}",
                            'status' => $payroll->status,
                        ];
                        $totalDeleted++;
                    } else {
                        try {
                            // Delete related notifications first
                            $payroll->notifications()->delete();
                            
                            // Delete the payroll
                            $payroll->delete();
                            
                            $this->line("   🗑️  Dihapus duplikat: ID #{$payroll->id} | Periode: {$payroll->period_start} s/d {$payroll->period_end} | Status: {$payroll->status}");
                            $deletedDetails[] = [
                                'id' => $payroll->id,
                                'user_name' => $userName,
                                'period' => "{$payroll->period_start} to {$payroll->period_end}",
                                'status' => $payroll->status,
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
                $this->warn('⚠️  DRY RUN - Transaksi dibatalkan, tidak ada data yang dihapus');
            } else {
                DB::commit();
            }

            // Summary
            $this->newLine();
            $this->info('📊 Ringkasan Pembersihan:');
            $this->info("✅ Duplikat Dihapus: {$totalDeleted}");
            $this->info("💾 Data Dipertahankan: {$totalKept}");

            if (!empty($errors)) {
                $this->error("❌ Errors: " . count($errors));
                foreach ($errors as $error) {
                    $this->error("   - {$error}");
                }
            }

            $this->info('🎉 Proses pembersihan duplikat selesai!');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Terjadi kesalahan: ' . $e->getMessage());
            return 1;
        }
    }
}
