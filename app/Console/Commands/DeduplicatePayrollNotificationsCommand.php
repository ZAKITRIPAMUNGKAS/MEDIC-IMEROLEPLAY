<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PayrollNotification;
use Illuminate\Support\Facades\DB;

class DeduplicatePayrollNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:deduplicate-notifications 
                            {--dry-run : Preview duplicate notifications without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate payroll notifications, keeping only the latest notification per payroll and type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('🔍 Starting payroll notifications deduplication...');
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be deleted.');
        }

        // Find duplicate groups: same payroll_id and notification_type
        $duplicates = DB::table('payroll_notifications')
            ->select('payroll_id', 'notification_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('payroll_id')
            ->groupBy('payroll_id', 'notification_type')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ Tidak ada data notifikasi gaji duplikat.');
            return 0;
        }

        $this->info("📋 Ditemukan {$duplicates->count()} kelompok notifikasi dengan duplikat.");

        $totalDeleted = 0;
        $totalKept = 0;

        DB::beginTransaction();
        try {
            foreach ($duplicates as $duplicate) {
                // Get all notifications for this payroll_id and notification_type, newest first
                $notifications = PayrollNotification::where('payroll_id', $duplicate->payroll_id)
                    ->where('notification_type', $duplicate->notification_type)
                    ->orderByDesc('sent_at')
                    ->orderByDesc('id')
                    ->get();

                if ($notifications->count() <= 1) {
                    continue;
                }

                // Keep the newest notification
                $keep = $notifications->first();
                $toDelete = $notifications->skip(1);

                $totalKept++;

                foreach ($toDelete as $notif) {
                    $this->line("   [Hapus] ID: {$notif->id} | Payroll #{$notif->payroll_id} | User #{$notif->user_id} | Type: {$notif->notification_type} | Sent: {$notif->sent_at}");
                    if (!$dryRun) {
                        $notif->delete();
                    }
                    $totalDeleted++;
                }
            }

            if ($dryRun) {
                DB::rollBack();
                $this->newLine();
                $this->info("🔍 [DRY RUN SELESAI] Akan menghapus {$totalDeleted} notifikasi duplikat dan mempertahankan {$totalKept} notifikasi terbaru.");
            } else {
                DB::commit();
                $this->newLine();
                $this->info("🎉 [SELESAI] Berhasil menghapus {$totalDeleted} notifikasi duplikat! {$totalKept} notifikasi terbaru dipertahankan.");
            }

            return 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('❌ Terjadi kesalahan saat menghapus notifikasi duplikat: ' . $e->getMessage());
            return 1;
        }
    }
}
