<?php

namespace App\Helpers;

use App\Models\SalarySetting;
use App\Models\User;

class PayrollHelper
{
    /**
     * Calculate weekly salary based on role and total hours
     *
     * @param string|null $roleName
     * @param int $totalSeconds
     * @param float|null $customWeeklySalary
     * @return int
     */
    public static function computeWeeklySalary(?string $roleName, int $totalSeconds, ?float $customWeeklySalary = null): int
    {
        if ($totalSeconds <= 0) {
            return 0;
        }

        $salarySetting = $roleName ? SalarySetting::findByRole($roleName) : null;

        // If custom salary is provided, use it. If not, check validation.
        if ($customWeeklySalary > 0) {
            // If salary setting doesn't exist (e.g. no role), create a dummy one for calculation
            if (!$salarySetting) {
                $salarySetting = new SalarySetting();
            }
            $salarySetting->weekly_salary = $customWeeklySalary;
        } elseif (!$salarySetting || !$salarySetting->isValidForCalculation()) {
            // Try case-insensitive lookup
            if (!$salarySetting && $roleName) {
                $salarySetting = SalarySetting::whereRaw('LOWER(role_name) = ?', [strtolower($roleName)])
                    ->where('is_active', true)
                    ->first();
            }

            // Fallback: try default trainee or first active setting
            if (!$salarySetting || !$salarySetting->isValidForCalculation()) {
                $salarySetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                    ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
            }

            if (!$salarySetting || !$salarySetting->isValidForCalculation()) {
                return 0;
            }
        }

        $totalHours = TimeHelper::secondsToHours($totalSeconds);
        return $salarySetting->calculateSalary($totalHours);
    }

    /**
     * Convert total seconds to hours for payroll storage
     *
     * @param int $totalSeconds
     * @return float
     */
    public static function convertSecondsToHours(int $totalSeconds): float
    {
        return TimeHelper::secondsToHours($totalSeconds);
    }

    /**
     * Get base salary for role or custom
     *
     * @param string|null $roleName
     * @param float|null $customWeeklySalary
     * @return int
     */
    public static function getBaseSalary(?string $roleName, ?float $customWeeklySalary = null): int
    {
        if ($customWeeklySalary > 0) {
            return (int) $customWeeklySalary;
        }

        $salarySetting = $roleName ? SalarySetting::findByRole($roleName) : null;
        if (!$salarySetting || !$salarySetting->isValidForCalculation()) {
            $salarySetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
        }

        return $salarySetting ? (int) $salarySetting->weekly_salary : 0;
    }

    /**
     * Format currency amount
     *
     * @param int|float|null $amount
     * @return string
     */
    public static function formatCurrency($amount = 0): string
    {
        return '$ ' . number_format((int) ($amount ?? 0), 0, '.', ',');
    }

    /**
     * Calculate overtime pay
     *
     * @param int $regularHours
     * @param int $totalHours
     * @param float $hourlyRate
     * @param float $overtimeMultiplier
     * @return int
     */
    public static function calculateOvertimePay(
        int $regularHours,
        int $totalHours,
        float $hourlyRate,
        float $overtimeMultiplier = 1.5
    ): int {
        $overtimeHours = max(0, $totalHours - $regularHours);
        return (int) ($overtimeHours * $hourlyRate * $overtimeMultiplier);
    }

    /**
     * Calculate total pay including overtime
     *
     * @param int $regularHours
     * @param int $totalHours
     * @param float $hourlyRate
     * @param float $overtimeMultiplier
     * @return int
     */
    public static function calculateTotalPay(
        int $regularHours,
        int $totalHours,
        float $hourlyRate,
        float $overtimeMultiplier = 1.5
    ): int {
        $regularPay = min($totalHours, $regularHours) * $hourlyRate;
        $overtimePay = self::calculateOvertimePay($regularHours, $totalHours, $hourlyRate, $overtimeMultiplier);

        return (int) ($regularPay + $overtimePay);
    }

    /**
     * Get payroll summary for user
     *
     * @param User $user
     * @param string $periodStart
     * @param string $periodEnd
     * @return array
     */
    public static function getPayrollSummary(User $user, string $periodStart, string $periodEnd): array
    {
        $roleName = $user->role?->name;
        if (!$roleName && $user->effective_medic_role) {
            $roleName = $user->effective_medic_role->name;
        }
        if (!$roleName && $user->medicRole) {
            $roleName = $user->medicRole->name;
        }

        $salarySetting = $roleName ? SalarySetting::findByRole($roleName) : null;
        if (!$salarySetting || !$salarySetting->isValidForCalculation()) {
            $salarySetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
        }

        // Get total hours worked in the period (comprehensive session types and cross-date checks)
        $totalSeconds = $user->attendances()
            ->where(function ($dq) use ($periodStart, $periodEnd) {
                $dq->whereBetween('work_date', [$periodStart, $periodEnd])
                   ->orWhereBetween(\Illuminate\Support\Facades\DB::raw('DATE(clock_in)'), [$periodStart, $periodEnd]);
            })
            ->where(function ($sq) {
                $sq->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                   ->orWhereNull('session_type')
                   ->orWhereNotIn('session_type', ['break', 'custom']);
            })
            ->where(function ($cq) {
                $cq->where('is_active', false)
                   ->orWhereNotNull('clock_out');
            })
            ->whereNotNull('session_duration')
            ->where('session_duration', '>', 0)
            ->sum('session_duration');

        $totalHours = TimeHelper::secondsToHours($totalSeconds);
        $calculatedSalary = self::computeWeeklySalary($roleName, $totalSeconds);

        return [
            'weekly_salary' => $salarySetting->weekly_salary,
            'base_salary' => self::getBaseSalary($roleName),
            'calculated_salary' => $calculatedSalary,
            'total_hours' => $totalHours,
            'total_seconds' => $totalSeconds,
            'is_eligible' => true,
            'formatted_salary' => self::formatCurrency($calculatedSalary),
            'formatted_hours' => TimeHelper::formatDuration($totalSeconds)
        ];
    }

    /**
     * Validate payroll data
     *
     * @param array $data
     * @return array
     */
    public static function validatePayrollData(array $data): array
    {
        $errors = [];

        if (empty($data['user_id'])) {
            $errors[] = 'User ID is required';
        }

        if (empty($data['period_start'])) {
            $errors[] = 'Period start is required';
        }

        if (empty($data['period_end'])) {
            $errors[] = 'Period end is required';
        }

        if (!empty($data['period_start']) && !empty($data['period_end'])) {
            $start = \Carbon\Carbon::parse($data['period_start']);
            $end = \Carbon\Carbon::parse($data['period_end']);

            if ($end->lt($start)) {
                $errors[] = 'Period end must be after period start';
            }
        }

        if (isset($data['total_hours']) && $data['total_hours'] < 0) {
            $errors[] = 'Total hours cannot be negative';
        }

        if (isset($data['calculated_salary']) && $data['calculated_salary'] < 0) {
            $errors[] = 'Calculated salary cannot be negative';
        }

        return $errors;
    }

    /**
     * Get payroll statistics
     *
     * @param string $periodStart
     * @param string $periodEnd
     * @return array
     */
    public static function getPayrollStatistics(string $periodStart, string $periodEnd): array
    {
        $totalPayrolls = \App\Models\Payroll::whereBetween('period_start', [$periodStart, $periodEnd])->count();
        $pendingPayrolls = \App\Models\Payroll::whereBetween('period_start', [$periodStart, $periodEnd])
            ->where('status', 'pending')->count();
        $paidPayrolls = \App\Models\Payroll::whereBetween('period_start', [$periodStart, $periodEnd])
            ->where('status', 'paid')->count();
        $totalAmount = \App\Models\Payroll::whereBetween('period_start', [$periodStart, $periodEnd])
            ->where('status', 'paid')->sum('calculated_salary');
        $pendingAmount = \App\Models\Payroll::whereBetween('period_start', [$periodStart, $periodEnd])
            ->where('status', 'pending')->sum('calculated_salary');

        return [
            'total_payrolls' => $totalPayrolls,
            'pending_payrolls' => $pendingPayrolls,
            'paid_payrolls' => $paidPayrolls,
            'total_amount' => $totalAmount,
            'pending_amount' => $pendingAmount,
            'formatted_total_amount' => self::formatCurrency($totalAmount),
            'formatted_pending_amount' => self::formatCurrency($pendingAmount)
        ];
    }
}