<?php

namespace App\Constants;

class AttendanceConstants
{
    // Duration limits
    const MAX_DURATION_HOURS = 48;
    const MIN_DURATION_MINUTES = 1;
    const WARNING_DURATION_HOURS = 24;
    const LONG_DUTY_THRESHOLD_HOURS = 8;

    // Session types
    const SESSION_TYPES = [
        'work' => 'Kerja',
        'break' => 'Istirahat',
        'meeting' => 'Rapat',
        'overtime' => 'Lembur'
    ];

    // Session status
    const SESSION_STATUS_ACTIVE = 'active';
    const SESSION_STATUS_COMPLETED = 'completed';
    const SESSION_STATUS_CANCELLED = 'cancelled';

    // Payroll periods
    const PAYROLL_PERIOD_WEEKLY = 'weekly';
    const PAYROLL_PERIOD_MONTHLY = 'monthly';
    const PAYROLL_PERIOD_DAILY = 'daily';

    // Timezone
    const DEFAULT_TIMEZONE = 'Asia/Jakarta';

    // Validation rules
    const VALIDATION_RULES = [
        'player_id' => 'required|string|max:255|regex:/^[a-zA-Z0-9_:]+$/',
        'player_name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s_-]+$/',
        'clock_in' => 'required|date|before_or_equal:now',
        'clock_out' => 'nullable|date|after:clock_in|before_or_equal:now',
        'session_type' => 'nullable|string|in:work,break,meeting,overtime',
        'notes' => 'nullable|string|max:1000'
    ];

    // Error messages
    const ERROR_MESSAGES = [
        'duplicate_clock_in' => 'Player sudah melakukan clock in. Silakan clock out terlebih dahulu.',
        'duration_too_short' => 'Durasi terlalu pendek (minimum 1 menit)',
        'duration_too_long' => 'Durasi melebihi batas maksimum',
        'invalid_time_range' => 'Waktu tidak valid',
        'no_active_session' => 'Tidak ada sesi aktif',
        'clock_in_required' => 'Clock in diperlukan',
        'clock_out_required' => 'Clock out diperlukan'
    ];

    // Success messages
    const SUCCESS_MESSAGES = [
        'clock_in_success' => 'Clock in berhasil',
        'clock_out_success' => 'Clock out berhasil',
        'session_created' => 'Sesi berhasil dibuat',
        'session_updated' => 'Sesi berhasil diperbarui',
        'session_cancelled' => 'Sesi berhasil dibatalkan'
    ];

    // Discord webhook types
    const DISCORD_WEBHOOK_TYPES = [
        'absensi' => 'absensi',
        'surat_kesehatan' => 'surat_kesehatan',
        'surat_psikolog' => 'surat_psikolog',
        'operasi_plastik' => 'operasi_plastik',
        'konsultasi_medis' => 'konsultasi_medis',
        'laporan_kecelakaan' => 'laporan_kecelakaan',
        'permintaan_ambulans' => 'permintaan_ambulans',
        'pendaftaran_karakter' => 'pendaftaran_karakter',
        'tes_psikologi' => 'tes_psikologi'
    ];

    // Rate limiting
    const RATE_LIMITS = [
        'api_absensi' => '30,1', // 30 requests per minute
        'api_general' => '60,1', // 60 requests per minute
        'web_attendance' => '10,1' // 10 requests per minute
    ];

    // Database indexes
    const DATABASE_INDEXES = [
        'attendances_user_work_date' => ['user_id', 'work_date'],
        'attendances_work_date_session_duration' => ['work_date', 'session_duration'],
        'attendances_user_session_type' => ['user_id', 'session_type'],
        'payrolls_user_period' => ['user_id', 'period_start', 'period_end'],
        'payrolls_status' => ['status']
    ];

    /**
     * Get session type display name
     *
     * @param string $type
     * @return string
     */
    public static function getSessionTypeDisplayName(string $type): string
    {
        return self::SESSION_TYPES[$type] ?? ucfirst($type);
    }

    /**
     * Get all session types
     *
     * @return array
     */
    public static function getAllSessionTypes(): array
    {
        return array_keys(self::SESSION_TYPES);
    }

    /**
     * Check if session type is valid
     *
     * @param string $type
     * @return bool
     */
    public static function isValidSessionType(string $type): bool
    {
        return array_key_exists($type, self::SESSION_TYPES);
    }

    /**
     * Get validation rules for specific field
     *
     * @param string $field
     * @return string|null
     */
    public static function getValidationRule(string $field): ?string
    {
        return self::VALIDATION_RULES[$field] ?? null;
    }

    /**
     * Get error message for specific error
     *
     * @param string $error
     * @return string|null
     */
    public static function getErrorMessage(string $error): ?string
    {
        return self::ERROR_MESSAGES[$error] ?? null;
    }

    /**
     * Get success message for specific action
     *
     * @param string $action
     * @return string|null
     */
    public static function getSuccessMessage(string $action): ?string
    {
        return self::SUCCESS_MESSAGES[$action] ?? null;
    }

    /**
     * Get rate limit for specific endpoint
     *
     * @param string $endpoint
     * @return string|null
     */
    public static function getRateLimit(string $endpoint): ?string
    {
        return self::RATE_LIMITS[$endpoint] ?? null;
    }
}
