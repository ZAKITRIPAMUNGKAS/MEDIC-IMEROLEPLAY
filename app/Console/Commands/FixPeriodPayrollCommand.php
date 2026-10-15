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
    protected $description = 'Perbaiki dan pisahkan tuntas data gaji seluruh staf untuk periode 28 Sep - 04 Oct dan 05 Oct - 11 Oct';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $userOption = trim((string) $this->option('user'));

        if (empty($userOption) || strtolower($userOption) === 'all') {
            $this->info("👥 Memproses SEMUA staf aktif...");
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

        $movedToWeek1Count = 0;
        $createdWeek2PendingCount = 0;
        $dedupCount = 0;

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                $roleName = optional($user->role)->name;
                if (!$roleName && $user->effective_medic_role) {
                    $roleName = $user->effective_medic_role->name;
                }
                if (!$roleName && $user->medicRole) {
                    $roleName = $user->medicRole->name;
                }
                $customSalary = $user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);

                // Absensi asli Minggu 1 (28 Sep - 04 Oct)
                $attWeek1 = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', ['2026-09-28', '2026-10-04'])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false)
                    ->get();
                $secWeek1 = $attWeek1->sum('session_duration');
                $hoursWeek1 = PayrollHelper::convertSecondsToHours($secWeek1);
                $calcSal1 = PayrollHelper::computeWeeklySalary($roleName, $secWeek1, $customSalary);

                // Absensi asli Minggu 2 (05 Oct - 11 Oct)
                $attWeek2 = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', ['2026-10-05', '2026-10-11'])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false)
                    ->get();
                $secWeek2 = $attWeek2->sum('session_duration');
                $hoursWeek2 = PayrollHelper::convertSecondsToHours($secWeek2);
                $calcSal2 = PayrollHelper::computeWeeklySalary($roleName, $secWeek2, $customSalary);

                // Ambil semua payroll user di periode 28 Sep s/d 11 Oct
                $payrollsWeek1 = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-09-28')
                    ->get();

                $payrollsWeek2 = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-10-05')
                    ->get();

                // -------------------------------------------------------------
                // KASUS 1: Record di Minggu 2 (05-11 Oct) yang DIBAYAR PADA 07/08 Okt (oleh Ishihara / Tamao)
                // Uang ini dibayar sebelum minggu 05-11 Oct selesai -> SEBENARNYA ADALAH GAJI MINGGU 1 (28 Sep - 04 Oct)!
                // -------------------------------------------------------------
                foreach ($payrollsWeek2 as $p2) {
                    if ($p2->status === 'paid' && $p2->paid_at) {
                        $paidDate = Carbon::parse($p2->paid_at);
                        // Jika dibayar sebelum 09 Oct (misal 07 atau 08 Oct), itu adalah pembayaran minggu 28 Sep
                        if ($paidDate->lessThan(Carbon::parse('2026-10-09 00:00:00'))) {
                            $paidByName = $p2->paidBy->name ?? 'Admin';
                            $this->warn("🔄 [Koreksi Periode] {$user->name}: Slip gaji ID {$p2->id} dibayar pada {$paidDate->format('d M Y H:i')} oleh {$paidByName}.");
                            $this->line("   -> Slip ini sebenarnya adalah pembayaran Minggu 1 (28 Sep - 04 Oct).");

                            // Cek apakah user sudah punya slip di minggu 1
                            $existingW1 = Payroll::where('user_id', $user->id)
                                ->where('period_start', '2026-09-28')
                                ->where('id', '!=', $p2->id)
                                ->first();

                            if ($existingW1) {
                                if ($existingW1->status === 'pending') {
                                    // Hapus pending yang duplikat di minggu 1, pindahkan record paid ini
                                    if (!$dryRun) {
                                        $existingW1->notifications()->delete();
                                        $existingW1->delete();
                                    }
                                } elseif ($existingW1->status === 'paid') {
                                    // Sudah ada paid di minggu 1, hapus yang duplikat
                                    if (!$dryRun) {
                                        $p2->notifications()->delete();
                                        $p2->delete();
                                    }
                                    $dedupCount++;
                                    continue;
                                }
                            }

                            // Pindahkan record p2 ke minggu 1 (28 Sep - 04 Oct) dengan nominal dan jam minggu 1
                            if (!$dryRun) {
                                $p2->update([
                                    'period_start' => '2026-09-28',
                                    'period_end' => '2026-10-04',
                                    'total_hours' => $hoursWeek1 > 0 ? $hoursWeek1 : $p2->total_hours,
                                    'calculated_salary' => $calcSal1 > 0 ? $calcSal1 : $p2->calculated_salary,
                                    'notes' => ($p2->notes ? $p2->notes . " | " : "") . "Moved back to rightful period 28 Sep - 04 Oct on " . now()->format('Y-m-d H:i:s'),
                                ]);
                            }
                            $movedToWeek1Count++;

                            // Sekarang minggu 2 (05-11 Oct) untuk user ini harus dibuatkan slip PENDING baru (jika ada jam kerja di minggu 2)
                            $hasRemainingWeek2 = Payroll::where('user_id', $user->id)
                                ->where('period_start', '2026-10-05')
                                ->where('id', '!=', $p2->id)
                                ->exists();

                            if (!$hasRemainingWeek2 && $hoursWeek2 > 0 && $calcSal2 > 0) {
                                $this->info(" ➕ [Buat Slip Pending 05-11 Oct] {$user->name} | Jam: {$hoursWeek2}h | Gaji: Rp " . number_format($calcSal2, 0, ',', '.') . " (Status: Pending)");
                                if (!$dryRun) {
                                    Payroll::create([
                                        'user_id' => $user->id,
                                        'period_start' => '2026-10-05',
                                        'period_end' => '2026-10-11',
                                        'total_hours' => $hoursWeek2,
                                        'base_salary' => $baseSalary,
                                        'calculated_salary' => $calcSal2,
                                        'status' => 'pending',
                                        'notes' => "Generated pending salary for 05-11 Oct on " . now()->format('Y-m-d H:i:s'),
                                    ]);
                                }
                                $createdWeek2PendingCount++;
                            }
                        }
                    }
                }

                // -------------------------------------------------------------
                // KASUS 2: PEMBERSIHAN DUPLIKAT DI MINGGU YANG SAMA (05-11 Oct atau 28 Sep-04 Oct)
                // Misal Dilan Smith yang punya 2 record di 05-11 Oct (satu paid 07 Oct, satu paid 13 Oct)
                // -------------------------------------------------------------
                $checkWeek2Payrolls = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-10-05')
                    ->get();

                if ($checkWeek2Payrolls->count() > 1) {
                    $this->warn("⚠️  [Duplikat Terdeteksi] {$user->name} memiliki {$checkWeek2Payrolls->count()} slip di minggu 05-11 Oct.");
                    // Urutkan: dahulukan yang dibayar pada tanggal 12/13 Oct (Billy), hapus yang lebih lama/duplikat
                    $sorted = $checkWeek2Payrolls->sort(function ($a, $b) {
                        $aDate = $a->paid_at ? Carbon::parse($a->paid_at)->timestamp : 0;
                        $bDate = $b->paid_at ? Carbon::parse($b->paid_at)->timestamp : 0;
                        return $bDate <=> $aDate;
                    })->values();

                    $keep = $sorted->first();
                    $dupes = $sorted->slice(1);

                    foreach ($dupes as $dupe) {
                        $this->line("   ❌ Hapus slip duplikat ID #{$dupe->id} (Status: {$dupe->status}, Nilai: \${$dupe->calculated_salary})");
                        if (!$dryRun) {
                            $dupe->notifications()->delete();
                            $dupe->delete();
                        }
                        $dedupCount++;
                    }
                }

                // Pastikan user punya slip di minggu 1 (28 Sep - 04 Oct) jika punya absensi tapi belum ada slip
                $hasW1 = Payroll::where('user_id', $user->id)
                    ->where('period_start', '2026-09-28')
                    ->exists();

                if (!$hasW1 && $hoursWeek1 > 0 && $calcSal1 > 0) {
                    if (!$dryRun) {
                        Payroll::create([
                            'user_id' => $user->id,
                            'period_start' => '2026-09-28',
                            'period_end' => '2026-10-04',
                            'total_hours' => $hoursWeek1,
                            'base_salary' => $baseSalary,
                            'calculated_salary' => $calcSal1,
                            'status' => 'pending',
                            'notes' => "Auto-created missing payroll for 28 Sep - 04 Oct on " . now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            if (!$dryRun) {
                DB::commit();
                $this->info("\n================ HASIL PERBAIKAN MASAL ================");
                $this->info("✅ Slip dibayar 07/08 Okt yang dikembalikan ke 28 Sep : {$movedToWeek1Count} staf");
                $this->info("✅ Slip periode 05-11 Okt yang di-reset jadi PENDING : {$createdWeek2PendingCount} staf");
                $this->info("✅ Data duplikat yang dibersihkan                    : {$dedupCount} record");
                $this->info("========================================================\n");
            } else {
                DB::rollBack();
                $this->info("\n[DRY RUN SELESAI] Akan dipindahkan {$movedToWeek1Count} slip ke 28 Sep, {$createdWeek2PendingCount} slip baru pending di 05-11 Okt, dan dihapus {$dedupCount} duplikat.");
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Terjadi kesalahan: " . $e->getMessage());
            return 1;
        }
    }
}
