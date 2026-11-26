<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'weekly_salary',
        'description',
        'is_active'
    ];

    protected $casts = [
        'weekly_salary' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Scope untuk role yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Format gaji per minggu
     */
    public function getFormattedWeeklySalaryAttribute()
    {
        return '$ ' . number_format($this->weekly_salary, 0, '.', ',');
    }

    /**
     * Calculate salary based on hours worked
     * 
     * Progressive 2-Tier Hourly Rates:
     * - 5 Jam Pertama (Jam 1 s/d 5)  : 8% per jam
     *   (1 jam = 8%, 2 jam = 16%, 3 jam = 24%, 4 jam = 32%, 5 jam = 40%)
     * - 5 Jam Berikutnya (Jam 6 s/d 10): 12% per jam
     *   (6 jam = 52%, 7 jam = 64%, 8 jam = 76%, 9 jam = 88%, 10 jam = 100%)
     * - Lembur / Overtime (> 10 jam) : 100% Gaji Pokok + $2.500 per jam lembur
     * - Batas Minimal (< 15 menit)   : $0 (dianggap tidak aktif bekerja)
     * 
     * @param float $totalHours Total hours worked
     * @return int Calculated salary
     */
    public function calculateSalary(float $totalHours): int
    {
        if ($totalHours < 0 || $this->weekly_salary <= 0) {
            return 0;
        }

        $min15Minutes = 0.25; // 15 menit dalam jam
        if ($totalHours < $min15Minutes) {
            return 0;
        }

        $weeklySalary = (float) $this->weekly_salary;

        // Tier 1: 5 Jam Pertama (8% per jam)
        if ($totalHours <= 5.0) {
            $percentage = $totalHours * 0.08;
            return (int) round($weeklySalary * $percentage);
        }

        // Tier 2: 5 Jam Kedua (12% per jam -> jam ke-6 s/d 10)
        // 5 jam pertama = 40%, jam berikutnya bertambah 12% per jam hingga 100% pada jam ke-10
        if ($totalHours <= 10.0) {
            $percentage = 0.40 + (($totalHours - 5.0) * 0.12);
            return (int) round($weeklySalary * $percentage);
        }

        // Tier 3: Lembur / Overtime (> 10 jam)
        // 100% Gaji Pokok untuk 10 jam pertama + $2.500 per jam lembur
        $baseSalary = (int) round($weeklySalary);
        $overtimeHours = $totalHours - 10.0;
        $overtimeRatePerHour = 2500;
        $overtimePay = (int) round($overtimeHours * $overtimeRatePerHour);

        return $baseSalary + $overtimePay;
    }

    /**
     * Dapatkan persentase gaji yang dicapai berdasarkan jam kerja (0% - 100%+)
     *
     * @param float $totalHours
     * @return float
     */
    public function getSalaryPercentage(float $totalHours): float
    {
        if ($totalHours < 0.25) {
            return 0.0;
        }

        if ($totalHours <= 5.0) {
            return round($totalHours * 8.0, 1);
        }

        if ($totalHours <= 10.0) {
            return round(40.0 + (($totalHours - 5.0) * 12.0), 1);
        }

        return 100.0;
    }

    /**
     * Find salary setting by role name (with alias support for ID/EN)
     * 
     * @param string|null $roleName
     * @return SalarySetting|null
     */
    public static function findByRole(?string $roleName): ?self
    {
        if (empty($roleName)) {
            return null;
        }

        // Direct search first
        $setting = static::where(function ($query) use ($roleName) {
            $query->where('role_name', $roleName)
                ->orWhereRaw('LOWER(role_name) = ?', [strtolower($roleName)]);
        })
            ->where('is_active', true)
            ->first();

        if ($setting) {
            return $setting;
        }

        // Mapping dictionary between Indonesian and English / aliases (comprehensive & bidirectional)
        $roleAliases = [
            'dokter_spesialis' => ['specialist_doctor', 'dokter spesialis', 'spesialis', 'specialist doctor', 'specialist', 'supervisor'],
            'specialist_doctor' => ['dokter_spesialis', 'dokter spesialis', 'spesialis', 'specialist doctor', 'specialist', 'supervisor'],
            'dokter spesialis' => ['specialist_doctor', 'dokter_spesialis', 'spesialis', 'specialist doctor', 'specialist', 'supervisor'],
            'spesialis' => ['specialist_doctor', 'dokter_spesialis', 'dokter spesialis', 'specialist doctor', 'specialist', 'supervisor'],
            'supervisor' => ['specialist_doctor', 'dokter_spesialis', 'dokter spesialis', 'spesialis'],

            'dokter_umum' => ['general_doctor', 'dokter umum', 'general doctor', 'dokter'],
            'general_doctor' => ['dokter_umum', 'dokter umum', 'general doctor', 'dokter'],
            'dokter umum' => ['general_doctor', 'dokter_umum', 'general doctor', 'dokter'],
            'dokter' => ['general_doctor', 'dokter_umum', 'dokter umum'],

            'dokter_muda' => ['co_ass', 'co-ass', 'coass', 'dokter muda'],
            'dokter muda' => ['co_ass', 'co-ass', 'coass', 'dokter_muda'],
            'co_ass' => ['dokter_muda', 'co-ass', 'coass', 'dokter muda'],
            'co-ass' => ['co_ass', 'dokter_muda', 'coass', 'dokter muda'],
            'coass' => ['co_ass', 'co-ass', 'dokter_muda', 'dokter muda'],

            'perawat' => ['nurse', 'paramedic'],
            'nurse' => ['perawat', 'paramedic'],
            'paramedic' => ['nurse', 'perawat'],

            'trainee' => ['magang', 'siswa', 'student'],
            'magang' => ['trainee'],

            'manajer' => ['manager', 'staff_manager', 'staff manager'],
            'manager' => ['manajer', 'staff_manager', 'staff manager'],
            'staff_manager' => ['manager', 'manajer', 'staff manager'],
            'staff manager' => ['staff_manager', 'manager', 'manajer'],

            'executive' => ['eksekutif', 'admin', 'administrator'],
            'eksekutif' => ['executive', 'admin', 'administrator'],
            'admin' => ['executive', 'eksekutif', 'administrator'],
            'administrator' => ['executive', 'eksekutif', 'admin'],

            'ceo' => ['direktur', 'direktur utama', 'wadir'],
            'it_support' => ['it support', 'developer', 'it'],
            'it support' => ['it_support', 'developer', 'it'],
        ];

        $lowerRole = strtolower(trim($roleName));
        $normalizedRole = str_replace(['-', '_'], ' ', $lowerRole);
        $underscoredRole = str_replace([' ', '-'], '_', $lowerRole);

        $lookupKeys = array_unique(array_filter([$lowerRole, $normalizedRole, $underscoredRole]));

        foreach ($lookupKeys as $key) {
            if (isset($roleAliases[$key])) {
                foreach ($roleAliases[$key] as $alias) {
                    $setting = static::where(function ($query) use ($alias) {
                        $query->where('role_name', $alias)
                            ->orWhereRaw('LOWER(role_name) = ?', [strtolower($alias)]);
                    })
                        ->where('is_active', true)
                        ->first();

                    if ($setting) {
                        return $setting;
                    }
                }
            }
        }

        // Additional attempt: search LIKE
        $setting = static::where('is_active', true)
            ->where(function ($query) use ($lowerRole, $normalizedRole, $underscoredRole) {
                $query->whereRaw('LOWER(role_name) LIKE ?', ["%{$lowerRole}%"])
                    ->orWhereRaw('LOWER(role_name) LIKE ?', ["%{$underscoredRole}%"])
                    ->orWhereRaw('LOWER(role_name) LIKE ?', ["%{$normalizedRole}%"]);
            })
            ->first();

        if ($setting) {
            return $setting;
        }

        return null;
    }

    /**
     * Check if this setting is valid for salary calculation
     * 
     * @return bool
     */
    public function isValidForCalculation(): bool
    {
        return $this->is_active && $this->weekly_salary > 0;
    }

    /**
     * Get validation rules for salary setting
     * 
     * @return array
     */
    public static function getValidationRules(): array
    {
        return [
            'role_name' => 'required|string|max:255|unique:salary_settings,role_name',
            'weekly_salary' => 'required|numeric|min:0|max:999999999',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];
    }
}