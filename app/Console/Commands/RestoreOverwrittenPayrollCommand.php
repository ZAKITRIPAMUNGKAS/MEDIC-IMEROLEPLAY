<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payroll;
use Carbon\Carbon;

class RestoreOverwrittenPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:restore-overwritten-period {--dry-run : Only preview records without modifying database}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Kembalikan data gaji yang tertimpa ke periode aslinya (28 Sep - 04 Oct)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Memeriksa data gaji yang tertimpa...");
        $dryRun = $this->option('dry-run');

        // Target: Record dengan period_start 2026-10-05 tapi sudah berstatus dibayar pada atau sebelum 07 Oct 2026
        $query = Payroll::where('period_start', '2026-10-05')
            ->where('status', 'paid')
            ->whereDate('paid_at', '<=', '2026-10-07');

        $count = $query->count();

        if ($count === 0) {
            $this->info("✅ Tidak ditemukan data gaji yang tertimpa.");
            return 0;
        }

        $this->warn("⚠️ Ditemukan {$count} data gaji yang terbayar pada 07 Oct tapi berperiode 05 Oct - 11 Oct.");

        if ($dryRun) {
            $this->warn("[DRY RUN] Mode pratinjau aktif. Tidak ada perubahan yang disimpan ke database.");
            return 0;
        }

        $updated = Payroll::where('period_start', '2026-10-05')
            ->where('status', 'paid')
            ->whereDate('paid_at', '<=', '2026-10-07')
            ->update([
                'period_start' => '2026-09-28',
                'period_end' => '2026-10-04',
            ]);

        $this->info("🎉 Berhasil mengembalikan {$updated} data gaji ke periode 28 Sep 2026 - 04 Oct 2026!");
        return 0;
    }
}
