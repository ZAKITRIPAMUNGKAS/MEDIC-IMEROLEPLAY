<?php

namespace App\Exceptions;

use Exception;

class PayrollException extends Exception
{
    protected $errorCode;
    protected $context;

    public function __construct(string $message = "", string $errorCode = "", array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public static function salarySettingNotFound(array $context = []): self
    {
        return new self(
            'Pengaturan gaji tidak ditemukan untuk role ini.',
            'SALARY_SETTING_NOT_FOUND',
            $context
        );
    }

    public static function invalidSalaryCalculation(array $context = []): self
    {
        return new self(
            'Perhitungan gaji tidak valid.',
            'INVALID_SALARY_CALCULATION',
            $context
        );
    }

    public static function payrollAlreadyExists(array $context = []): self
    {
        return new self(
            'Gaji untuk periode ini sudah ada.',
            'PAYROLL_ALREADY_EXISTS',
            $context
        );
    }

    public static function noAttendanceData(array $context = []): self
    {
        return new self(
            'Tidak ada data absensi untuk periode ini.',
            'NO_ATTENDANCE_DATA',
            $context
        );
    }

    public static function payrollNotFound(array $context = []): self
    {
        return new self(
            'Data gaji tidak ditemukan.',
            'PAYROLL_NOT_FOUND',
            $context
        );
    }

    public static function payrollAlreadyPaid(array $context = []): self
    {
        return new self(
            'Gaji sudah dibayar.',
            'PAYROLL_ALREADY_PAID',
            $context
        );
    }

    public static function payrollCannotBeCancelled(array $context = []): self
    {
        return new self(
            'Gaji yang sudah dibayar tidak dapat dibatalkan.',
            'PAYROLL_CANNOT_BE_CANCELLED',
            $context
        );
    }

    public static function invalidPeriod(array $context = []): self
    {
        return new self(
            'Periode tidak valid.',
            'INVALID_PERIOD',
            $context
        );
    }

    public static function notificationFailed(array $context = []): self
    {
        return new self(
            'Gagal mengirim notifikasi.',
            'NOTIFICATION_FAILED',
            $context
        );
    }
}
