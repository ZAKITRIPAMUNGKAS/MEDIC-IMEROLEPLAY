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
                            {--user= : Nama/email staf tertentu (kosongkan atau isi "all" untuk memproses SEMUA staf)}
                            {--dry-run : Mode simulasi tanpa menyimpan perubahan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki data gaji seluruh staf yang tertukar atau hilang antara periode 28 Sep - 04 Oct dan 05 Oct - 11 Oct';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $userOption = trim((string) $this->option('user'));

        if (empty($userOption) || strtolower($userOption) === 'all') {
            $this->info("👥 Memproses SEMUA staf aktif yang memiliki role/absensi...");
            $users = User::where('is_active', true)->whereHas('role')->orderBy('name')->get();
        } else {
            $this->info("🔍 Mencari staf dengan kata kunci: '{$userOption}'...");
            $users = User::where(function ($q) use ($userOption) {
                $q->where('name', 'like', "%{$userOption}%")
                  ->orWhere('email', 'like', "%{$userOption}%");
            })->orderBy('name')->get();

            if ($users->isEmpty()) {
                $this->error("❌ Staf tidak ditemukan untuk kata kunci: {$userOption}");
                return 1;
            }
        }

        $this->info("📋 Total staf yang akan diperiksa: {$users->count()} staf.");

        if ($dryRun) {
            $this->warn("⚠️  MODE DRY RUN AKTIF - Perubahan TIDAK akan disimpan ke database.");
        }

        $swappedCount = 0;
        $createdWeek1Count = 0;
        $createdWeek2Count = 0;

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                // 1. Ambil absensi minggu 1 (28 Sep - 04 Oct)
                $attWeek1 = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', ['2026-09-28', '2026-10-04'])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false)
                    ->get();
                $secWeek1 = $attWeek1->sum('session_duration');
                $hoursWeek1 = PayrollHelper::convertSecondsToHours($secWeek1);

                // 2. Ambil absensi minggu 2 (05 Oct - 11 Oct)
                $attWeek2 = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', ['2026-10-05', '2026-10-11'])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false)
                    ->get();
                $secWeek2 = $attWeek2->sum('session_duration');
                $hoursWeek2 = PayrollHelper::convertSecondsToHours($secWeek2);

                // 3. Cek apakah ada record gaji yang saat ini tercatat di 28 Sep 2026
                $payWeek1 = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-09-28')
                    ->first();

                // Deteksi apakah record di 28 Sep ini sebenarnya milik minggu 05-11 Okt (Tertukar)
                $isSwapped = false;
                if ($payWeek1 && $hoursWeek2 > 0) {
                    // Kasus A: Jam kerja di slip 28 Sep sangat mirip dengan absensi 05-11 Okt (selisih < 0.2 jam)
                    // dan berbeda dengan jam kerja asli minggu 28 Sep
                    $diffWithWeek2 = abs((float)$payWeek1->total_hours - (float)$hoursWeek2);
                    $diffWithWeek1 = abs((float)$payWeek1->total_hours - (float)$hoursWeek1);

                    if ($diffWithWeek2 < 0.2 && ($diffWithWeek1 > 0.5 || $hoursWeek1 == 0)) {
                        $isSwapped = true;
                    }

                    // Kasus B: Slip berstatus 'paid' pada tanggal 8 Okt ke atas, dan ada jam kerja di minggu 05-11 Okt
                    if (!$isSwapped && $payWeek1->status === 'paid' && $payWeek1->paid_at) {
                        $paidDate = Carbon::parse($payWeek1->paid_at);
                        if ($paidDate->greaterThanOrEqualTo(Carbon::parse('2026-10-08'))) {
                            $isSwapped = true;
                        }
                    }
                }

                if ($isSwapped) {
                    $this->warn("🔄 [Tertukar] {$user->name}: Record gaji ID {$payWeek1->id} (Jam: {$payWeek1->total_hours}h, Status: {$payWeek1->status}) saat ini berada di 28 Sep, padahal miliknya minggu 05-11 Okt.");
                    
                    if (!$dryRun) {
                        $payWeek1->update([
                            'period_start' => '2026-10-05',
                            'period_end' => '2026-10-11',
                            'notes' => ($payWeek1->notes ? $payWeek1->notes . " | " : "") . "Period corrected to 05-11 Oct on " . now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                    $swappedCount++;
                }

                // Siapkan data role & base salary
                $roleName = optional($user->role)->name;
                if (!$roleName && $user->effective_medic_role) {
                    $roleName = $user->effective_medic_role->name;
                }
                if (!$roleName && $user->medicRole) {
                    $roleName = $user->medicRole->name;
                }
                $customSalary = $user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);

                // 4. Periksa apakah user memiliki slip gaji untuk Minggu 1 (28 Sep - 04 Oct)
                $hasWeek1 = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-09-28')
                    ->exists();

                if (!$hasWeek1 && $hoursWeek1 > 0) {
                    $calcSal1 = PayrollHelper::computeWeeklySalary($roleName, $secWeek1, $customSalary);
                    if ($calcSal1 > 0) {
                        $this->line(" ➕ [Gaji Baru 28 Sep - 04 Oct] {$user->name} | Jam: {$hoursWeek1}h | Nominal: Rp " . number_format($calcSal1, 0, ',', '.'));
                        
                        if (!$dryRun) {
                            Payroll::create([
                                'user_id' => $user->id,
                                'period_start' => '2026-09-28',
                                'period_end' => '2026-10-04',
                                'total_hours' => $hoursWeek1,
                                'base_salary' => $baseSalary,
                                'calculated_salary' => $calcSal1,
                                'status' => 'pending',
                                'notes' => "Generated for missing period 28 Sep - 04 Oct on " . now()->format('Y-m-d H:i:s'),
                            ]);
                        }
                        $createdWeek1Count++;
                    }
                }

                // 5. Periksa apakah user memiliki slip gaji untuk Minggu 2 (05 Oct - 11 Oct)
                $hasWeek2 = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-10-05')
                    ->exists();

                if (!$hasWeek2 && $hoursWeek2 > 0) {
                    $calcSal2 = PayrollHelper::computeWeeklySalary($roleName, $secWeek2, $customSalary);
                    if ($calcSal2 > 0) {
                        $this->line(" ➕ [Gaji Baru 05 Oct - 11 Oct] {$user->name} | Jam: {$hoursWeek2}h | Nominal: Rp " . number_format($calcSal2, 0, ',', '.'));
                        
                        if (!$dryRun) {
                            Payroll::create([
                                'user_id' => $user->id,
                                'period_start' => '2026-10-05',
                                'period_end' => '2026-10-11',
                                'total_hours' => $hoursWeek2,
                                'base_salary' => $baseSalary,
                                'calculated_salary' => $calcSal2,
                                'status' => 'pending',
                                'notes' => "Generated for missing period 05-11 Oct on " . now()->format('Y-m-d H:i:s'),
                            ]);
                        }
                        $createdWeek2Count++;
                    }
                }
            }

            if (!$dryRun) {
                DB::commit();
                $this->info("\n================ HASIL SINKRONISASI ================");
                $this->info("✅ Gaji tertukar yang dikembalikan ke 05-11 Oct : {$swappedCount} staf");
                $this->info("✅ Gaji baru dibuatkan untuk 28 Sep - 04 Oct   : {$createdWeek1Count} staf");
                $this->info("✅ Gaji baru dibuatkan untuk 05 Oct - 11 Oct   : {$createdWeek2Count} staf");
                $this->info("====================================================\n");
            } else {
                DB::rollBack();
                $this->info("\n[DRY RUN SELESAI] Terdeteksi {$swappedCount} data tertukar, {$createdWeek1Count} data minggu 1 yang hilang, {$createdWeek2Count} data minggu 2 yang hilang.");
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Terjadi kesalahan: " . $e->getMessage());
            return 1;
        }
    }
}
