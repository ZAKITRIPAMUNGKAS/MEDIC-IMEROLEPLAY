<?php

namespace App\Exceptions;

use Exception;

class AttendanceException extends Exception
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

    public static function duplicateClockIn(array $context = []): self
    {
        return new self(
            'Player sudah melakukan clock in. Silakan clock out terlebih dahulu.',
            'DUPLICATE_CLOCK_IN',
            $context
        );
    }

    public static function noActiveSession(array $context = []): self
    {
        return new self(
            'Tidak ada sesi aktif untuk di-clock out.',
            'NO_ACTIVE_SESSION',
            $context
        );
    }

    public static function invalidTimeRange(array $context = []): self
    {
        return new self(
            'Waktu tidak valid.',
            'INVALID_TIME_RANGE',
            $context
        );
    }

    public static function durationTooShort(array $context = []): self
    {
        return new self(
            'Durasi terlalu pendek (minimum 1 menit).',
            'DURATION_TOO_SHORT',
            $context
        );
    }

    public static function durationTooLong(array $context = []): self
    {
        return new self(
            'Durasi melebihi batas maksimum.',
            'DURATION_TOO_LONG',
            $context
        );
    }

    public static function sessionCloseFailed(array $context = []): self
    {
        return new self(
            'Gagal menutup sesi.',
            'SESSION_CLOSE_FAILED',
            $context
        );
    }

    public static function userNotFound(array $context = []): self
    {
        return new self(
            'User tidak ditemukan.',
            'USER_NOT_FOUND',
            $context
        );
    }

    public static function validationFailed(array $errors, array $context = []): self
    {
        return new self(
            'Validasi gagal: ' . implode(', ', $errors),
            'VALIDATION_FAILED',
            array_merge($context, ['errors' => $errors])
        );
    }
}
