<?php

namespace App\Exceptions;

use Exception;

class FormException extends Exception
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

    public static function formNotFound(array $context = []): self
    {
        return new self(
            'Formulir tidak ditemukan.',
            'FORM_NOT_FOUND',
            $context
        );
    }

    public static function invalidStatus(array $context = []): self
    {
        return new self(
            'Status formulir tidak valid untuk operasi ini.',
            'INVALID_STATUS',
            $context
        );
    }

    public static function alreadyProcessed(array $context = []): self
    {
        return new self(
            'Formulir sudah diproses.',
            'ALREADY_PROCESSED',
            $context
        );
    }

    public static function processingFailed(array $context = []): self
    {
        return new self(
            'Gagal memproses formulir.',
            'PROCESSING_FAILED',
            $context
        );
    }

    public static function validationFailed(array $errors, array $context = []): self
    {
        return new self(
            'Validasi formulir gagal: ' . implode(', ', $errors),
            'VALIDATION_FAILED',
            array_merge($context, ['errors' => $errors])
        );
    }

    public static function webhookFailed(array $context = []): self
    {
        return new self(
            'Gagal mengirim notifikasi Discord.',
            'WEBHOOK_FAILED',
            $context
        );
    }
}
