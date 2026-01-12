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
     * Rules:
     * - < 15 menit (0.25 jam) = $0 (dianggap tidak bekerja)
     * - 15 menit - < 1 jam = Minimum 10% dari bayaran mingguan
     * - 1-5 jam = 50%
     * - 5-<10 jam = 80%
     * - ≥ 10 jam = 100% + overtime ($500 per jam untuk setiap jam di atas 10)
     * 
     * Examples:
     * - 9.5 jam = 80% weekly salary
     * - 10 jam = 100% weekly salary
     * - 12 jam = 100% + (2 × $500) = 100% + $1,000
     * - 15 jam = 100% + (5 × $500) = 100% + $2,500
     * 
     * @param float $totalHours Total hours worked
     * @return int Calculated salary
     */
    public function calculateSalary(float $totalHours): int
    {
        if ($totalHours < 0 || $this->weekly_salary <= 0) {
            return 0;
        }

        $weeklySalary = $this->weekly_salary;
        $min15Minutes = 0.25; // 15 menit dalam jam
        $oneHour = 1.0;
        $fiveHours = 5.0;
        $tenHours = 10.0;

        // < 15 menit (0.25 jam) = $0 (dianggap tidak bekerja)
        if ($totalHours < $min15Minutes) {
            return 0;
        }

        // 15 menit - < 1 jam = Minimum 10% dari bayaran mingguan
        if ($totalHours >= $min15Minutes && $totalHours < $oneHour) {
            return (int) ($weeklySalary * 0.10);
        }

        // 1-5 jam = 50%
        if ($totalHours >= $oneHour && $totalHours < $fiveHours) {
            return (int) ($weeklySalary * 0.50);
        }

        // 5-<10 jam = 80%
        if ($totalHours >= $fiveHours && $totalHours < $tenHours) {
            return (int) ($weeklySalary * 0.80);
        }

        // ≥ 10 jam = 100% + overtime
        // Base: 100% weekly salary untuk 10 jam pertama
        // Overtime: (total jam - 10) × $500 per jam
        $baseSalary = (int) $weeklySalary;

        if ($totalHours > $tenHours) {
            $overtimeHours = $totalHours - $tenHours;
            $overtimeRatePerHour = 500;
            $overtimePay = $overtimeHours * $overtimeRatePerHour;

            return (int) ($baseSalary + $overtimePay);
        }

        // Exactly 10 hours = 100% weekly salary, no overtime
        return $baseSalary;
    }

    /**
     * Find salary setting by role name
     * 
     * @param string|null $roleName
     * @return SalarySetting|null
     */
    public static function findByRole(?string $roleName): ?self
    {
        if (empty($roleName)) {
            return null;
        }

        return static::where(function ($query) use ($roleName) {
            $query->where('role_name', $roleName)
                ->orWhereRaw('LOWER(role_name) = ?', [strtolower($roleName)]);
        })
            ->where('is_active', true)
            ->first();
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