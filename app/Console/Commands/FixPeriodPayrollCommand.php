<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Helpers\PayrollHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixPeriodPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:fix-october-swap 
                            {--user= : Nama atau email user tertentu (default: Al Squarepants)}
                            {--dry-run : Mode simulasi tanpa menyimpan perubahan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki data gaji Al Squarepants / staf yang tertukar antara periode 28 Sep - 04 Oct dan 05 Oct - 11 Oct';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $userQuery = $this->option('user') ?: 'Squarepants';

        $this->info("🔍 Mencari staf dengan kata kunci: {$userQuery}...");

        $user = User::where('name', 'like', "%{$userQuery}%")
            ->orWhere('email', 'like', "%{$userQuery}%")
            ->first();

        if (!$user) {
            $this->error("❌ Staf tidak ditemukan untuk kata kunci: {$userQuery}");
            return 1;
        }

        $this->info("👤 Staf Ditemukan: [ID {$user->id}] {$user->name} ({$user->email})");

        if ($dryRun) {
            $this->warn("⚠️  MODE DRY RUN AKTIF - Perubahan TIDAK akan disimpan ke database.");
        }

        DB::beginTransaction();
        try {
            // 1. CARI GAJI YANG SAAT INI BERADA DI 28 Sep - 04 Oct (TETAPI MEMILIKI JAM KERJA ~15.87 JAM / 15:52 DARI MINGGU BERIKUTNYA)
            $misplacedPayroll = Payroll::where('user_id', $user->id)
                ->where('period_start', '2026-09-28')
                ->where('period_end', '2026-10-04')
                ->where('total_hours', '>=', 15) // Jam kerja 15:52:12
                ->first();

            if (!$misplacedPayroll) {
                // Coba cari record paid milik user yang berada di 28 Sep
                $misplacedPayroll = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-09-28')
                    ->where('status', 'paid')
                    ->first();
            }

            if ($misplacedPayroll) {
                $this->info("📌 Ditemukan data gaji (ID {$misplacedPayroll->id}) berstatus '{$misplacedPayroll->status}' dengan jam {$misplacedPayroll->total_hours}h senilai \${$misplacedPayroll->calculated_salary} yang saat ini tercatat di periode 28 Sep - 04 Oct.");
                $this->info("➡️  Memindahkan data gaji ini ke periode aslinya: 05 Oct 2026 s/d 11 Oct 2026...");

                if (!$dryRun) {
                    $misplacedPayroll->update([
                        'period_start' => '2026-10-05',
                        'period_end' => '2026-10-11',
                        'notes' => ($misplacedPayroll->notes ? $misplacedPayroll->notes . " | " : "") . "Period corrected to 05-11 Oct on " . now()->format('Y-m-d H:i:s'),
                    ]);
                }
                $this->info("✅ Berhasil memindahkan data gaji ID {$misplacedPayroll->id} ke periode 05-11 Oct 2026.");
            } else {
                $this->warn("ℹ️ Tidak ditemukan data gaji di 28 Sep - 04 Oct yang perlu dipindahkan.");
            }

            // 2. CEK DAN BUATKAN GAJI UNTUK PERIODE 28 Sep 2026 - 04 Oct 2026 DARI ABSENSI ASLI
            $startWeek1 = Carbon::parse('2026-09-28');
            $endWeek1 = Carbon::parse('2026-10-04');

            $existingWeek1Payroll = Payroll::where('user_id', $user->id)
                ->where('period_start', $startWeek1->toDateString())
                ->where('period_end', $endWeek1->toDateString())
                ->first();

            if ($existingWeek1Payroll) {
                $this->info("ℹ️ Sudah ada data gaji untuk periode 28 Sep - 04 Oct (ID {$existingWeek1Payroll->id}, Status: {$existingWeek1Payroll->status}, Jam: {$existingWeek1Payroll->total_hours}h).");
            } else {
                $this->info("🔍 Mengambil data absensi asli untuk periode 28 Sep 2026 s/d 04 Oct 2026...");
                $attendancesWeek1 = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', [
                        $startWeek1->toDateString(),
                        $endWeek1->toDateString()
                    ])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false)
                    ->get();

                if ($attendancesWeek1->isEmpty()) {
                    $this->warn("⚠️ Tidak ditemukan data absensi untuk periode 28 Sep - 04 Oct.");
                } else {
                    $totalSeconds = $attendancesWeek1->sum('session_duration');
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
                    $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

                    $this->info("📊 Rekap Absensi 28 Sep - 04 Oct:");
                    $this->line("   - Jumlah sesi absensi: {$attendancesWeek1->count()} sesi");
                    $this->line("   - Total durasi: {$totalHours} jam");
                    $this->line("   - Role: {$roleName}");
                    $this->line("   - Base salary: \${$baseSalary}");
                    $this->line("   - Gaji terhitung: \${$calculatedSalary}");

                    if (!$dryRun) {
                        $newPayroll = Payroll::create([
                            'user_id' => $user->id,
                            'period_start' => $startWeek1->toDateString(),
                            'period_end' => $endWeek1->toDateString(),
                            'total_hours' => $totalHours,
                            'base_salary' => $baseSalary,
                            'calculated_salary' => $calculatedSalary,
                            'status' => 'pending',
                            'notes' => "Generated for missing period 28 Sep - 04 Oct on " . now()->format('Y-m-d H:i:s'),
                        ]);
                        $this->info("🎉 Berhasil membuat data gaji baru ID {$newPayroll->id} untuk periode 28 Sep - 04 Oct (Status: Pending)!");
                    } else {
                        $this->info("🎉 [DRY RUN] Akan dibuat data gaji baru untuk periode 28 Sep - 04 Oct senilai \${$calculatedSalary}!");
                    }
                }
            }

            if (!$dryRun) {
                DB::commit();
                $this->info("\n✨ Selesai! Data gaji berhasil disinkronkan dan diperbaiki.");
            } else {
                DB::rollBack();
                $this->info("\n[DRY RUN Selesai] Tidak ada data yang diubah.");
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Terjadi kesalahan: " . $e->getMessage());
            return 1;
        }
    }
}
