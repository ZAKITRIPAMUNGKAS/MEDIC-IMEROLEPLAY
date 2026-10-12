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

        // Target: Semua record dengan period_start 2026-10-05 yang berstatus 'paid' (karena periode 5-11 Oct belum ada yang dibayar)
        $query = Payroll::where('period_start', '2026-10-05')
            ->where('status', 'paid');

        $count = $query->count();

        if ($count === 0) {
            $this->info("✅ Tidak ditemukan data gaji berstatus 'paid' pada periode 05 Oct 2026.");
            return 0;
        }

        $this->warn("⚠️ Ditemukan {$count} data gaji berstatus 'paid' pada periode 05 Oct - 11 Oct:");
        
        foreach ($query->with('user')->get() as $p) {
            $paidDate = $p->paid_at ? \Carbon\Carbon::parse($p->paid_at)->format('d M Y H:i') : 'Unknown';
            $this->line(" - [ID {$p->id}] {$p->user->name} | Dibayar: {$paidDate} | Gaji: {$p->calculated_salary}");
        }

        if ($dryRun) {
            $this->warn("[DRY RUN] Mode pratinjau aktif. Tidak ada perubahan yang disimpan ke database.");
            return 0;
        }

        $updated = Payroll::where('period_start', '2026-10-05')
            ->where('status', 'paid')
            ->update([
                'period_start' => '2026-09-28',
                'period_end' => '2026-10-04',
            ]);

        $this->info("🎉 Berhasil mengembalikan seluruh {$updated} data gaji terbayar ke periode 28 Sep 2026 - 04 Oct 2026!");
        return 0;
    }
}
