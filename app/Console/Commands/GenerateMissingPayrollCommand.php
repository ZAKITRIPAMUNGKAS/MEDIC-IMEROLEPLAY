<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Helpers\PayrollHelper;
use Carbon\Carbon;

class GenerateMissingPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:generate-missing 
                            {--start=2026-09-28 : Tanggal mulai periode}
                            {--end=2026-10-04 : Tanggal akhir periode}
                            {--dry-run : Mode simulasi tanpa simpan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lengkapi data gaji staf yang belum ada atau hilang pada periode tertentu berdasarkan absensi asli';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->option('start');
        $endDate = $this->option('end');
        $dryRun = $this->option('dry-run');

        $this->info("🔍 Memeriksa absensi staf untuk periode {$startDate} s/d {$endDate}...");

        $periodStart = Carbon::parse($startDate);
        $periodEnd = Carbon::parse($endDate);

        // Auto-heal attendances where clock_out is not null but is_active is true
        Attendance::whereNotNull('clock_out')
            ->where('is_active', true)
            ->where(function ($q) use ($periodStart, $periodEnd) {
                $q->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                  ->orWhere(function ($sub) use ($periodStart, $periodEnd) {
                      $sub->whereDate('clock_in', '>=', $periodStart->toDateString())
                          ->whereDate('clock_in', '<=', $periodEnd->toDateString());
                  });
            })
            ->update(['is_active' => false]);

        // Ambil semua user yang memiliki data absensi di periode ini
        $userIdsWithAttendance = Attendance::where(function ($sub) use ($periodStart, $periodEnd) {
                $sub->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                    ->orWhere(function ($cin) use ($periodStart, $periodEnd) {
                        $cin->whereDate('clock_in', '>=', $periodStart->toDateString())
                            ->whereDate('clock_in', '<=', $periodEnd->toDateString());
                    });
            })
            ->where(function ($st) {
                $st->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                   ->orWhereNull('session_type');
            })
            ->where(function ($act) {
                $act->where('is_active', false)
                    ->orWhereNotNull('clock_out');
            })
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->distinct()
            ->pluck('user_id');

        if ($userIdsWithAttendance->isEmpty()) {
            $this->warn("⚠️ Tidak ditemukan data absensi staf pada periode ini.");
            return 0;
        }

        $this->info("👥 Ditemukan {$userIdsWithAttendance->count()} staf yang memiliki jam dinas di periode ini.");

        // Cek siapa yang sudah punya payroll di periode / minggu ini
        $existingUserIds = Payroll::where(function ($q) use ($periodStart, $periodEnd) {
            $q->where(function ($exact) use ($periodStart, $periodEnd) {
                $exact->where('period_start', $periodStart->toDateString())
                      ->where('period_end', $periodEnd->toDateString());
            })
            ->orWhere('period_start', $periodStart->toDateString())
            ->orWhereBetween('period_start', [
                $periodStart->copy()->startOfWeek()->toDateString(),
                $periodStart->copy()->endOfWeek()->toDateString()
            ]);
        })
        ->pluck('user_id')
        ->toArray();

        $missingUserIds = $userIdsWithAttendance->diff($existingUserIds);

        if ($missingUserIds->isEmpty()) {
            $this->info("✅ Semua staf yang memiliki absensi sudah memiliki data payroll pada periode ini. Tidak ada data yang hilang!");
            return 0;
        }

        $this->warn("⚠️ Ditemukan {$missingUserIds->count()} staf yang memiliki absensi TAPI belum ada data gaji pada periode ini:");

        $createdCount = 0;

        foreach ($missingUserIds as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            $attendances = Attendance::where('user_id', $user->id)
                ->where(function ($sub) use ($periodStart, $periodEnd) {
                    $sub->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                        ->orWhere(function ($cin) use ($periodStart, $periodEnd) {
                            $cin->whereDate('clock_in', '>=', $periodStart->toDateString())
                                ->whereDate('clock_in', '<=', $periodEnd->toDateString());
                        });
                })
                ->where(function ($st) {
                    $st->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                       ->orWhereNull('session_type');
                })
                ->where(function ($act) {
                    $act->where('is_active', false)
                        ->orWhereNotNull('clock_out');
                })
                ->whereNotNull('session_duration')
                ->where('session_duration', '>', 0)
                ->get();

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

            if ($calculatedSalary <= 0 && $totalHours > 0) {
                $fallbackBase = PayrollHelper::getBaseSalary(null, $customSalary);
                $calculatedSalary = PayrollHelper::computeWeeklySalary(null, $totalSeconds, $customSalary);
                if ($baseSalary <= 0) {
                    $baseSalary = $fallbackBase;
                }
            }

            $this->line(" + [User ID {$user->id}] {$user->name} | Total Jam: {$totalHours}h | Gaji: Rp " . number_format($calculatedSalary, 0, ',', '.'));

            if (!$dryRun) {
                Payroll::create([
                    'user_id' => $user->id,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'total_hours' => $totalHours,
                    'base_salary' => $baseSalary,
                    'calculated_salary' => $calculatedSalary,
                    'status' => 'pending',
                    'notes' => "Restored missing payroll on " . now()->format('Y-m-d H:i:s') . " for {$user->name}",
                ]);
                $createdCount++;
            }
        }

        if ($dryRun) {
            $this->warn("[DRY RUN] Mode pratinjau selesai. Jalankan tanpa flag --dry-run untuk membuat data gaji di atas.");
        } else {
            $this->info("🎉 Berhasil membuat {$createdCount} data gaji yang hilang untuk periode {$startDate} s/d {$endDate}!");
        }

        return 0;
    }
}
