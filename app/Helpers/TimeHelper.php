<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    /**
     * Format duration in seconds to HH:MM:SS format
     *
     * @param int $seconds
     * @return string
     */
    public static function formatDuration(int $seconds): string
    {
        if ($seconds < 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Format duration in seconds to HH.MM.SS format (for Discord)
     *
     * @param int $seconds
     * @return string
     */
    public static function formatDurationForDiscord(int $seconds): string
    {
        if (!$seconds) {
            return '00.00.00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = floor($seconds % 60);

        return sprintf('%02d.%02d.%02d', $hours, $minutes, $secs);
    }

    /**
     * Convert hours to seconds
     *
     * @param float $hours
     * @return int
     */
    public static function hoursToSeconds(float $hours): int
    {
        return (int) ($hours * 3600);
    }

    /**
     * Convert seconds to hours
     *
     * @param int $seconds
     * @return float
     */
    public static function secondsToHours(int $seconds): float
    {
        return round($seconds / 3600, 2);
    }

    /**
     * Convert minutes to seconds
     *
     * @param int $minutes
     * @return int
     */
    public static function minutesToSeconds(int $minutes): int
    {
        return $minutes * 60;
    }

    /**
     * Convert seconds to minutes
     *
     * @param int $seconds
     * @return int
     */
    public static function secondsToMinutes(int $seconds): int
    {
        return floor($seconds / 60);
    }

    /**
     * Get duration between two Carbon instances in seconds
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return int
     */
    public static function getDurationInSeconds(Carbon $start, Carbon $end): int
    {
        return $start->diffInSeconds($end);
    }

    /**
     * Get duration between two Carbon instances in hours
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return float
     */
    public static function getDurationInHours(Carbon $start, Carbon $end): float
    {
        return self::secondsToHours(self::getDurationInSeconds($start, $end));
    }

    /**
     * Check if duration exceeds threshold
     *
     * @param int $seconds
     * @param int $thresholdHours
     * @return bool
     */
    public static function exceedsThreshold(int $seconds, int $thresholdHours = 24): bool
    {
        return self::secondsToHours($seconds) > $thresholdHours;
    }

    /**
     * Get human readable duration
     *
     * @param int $seconds
     * @return string
     */
    public static function getHumanReadableDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . ' jam';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . ' menit';
        }
        if ($secs > 0 || empty($parts)) {
            $parts[] = $secs . ' detik';
        }

        return implode(', ', $parts);
    }

    /**
     * Validate time range
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public static function validateTimeRange(Carbon $start, Carbon $end): array
    {
        $errors = [];

        if ($end->lt($start)) {
            $errors[] = 'Waktu akhir tidak boleh sebelum waktu mulai';
        }

        $duration = self::getDurationInHours($start, $end);
        if ($duration > 48) {
            $errors[] = 'Durasi tidak boleh melebihi 48 jam';
        }

        if ($duration < 0.016) { // Less than 1 minute
            $errors[] = 'Durasi minimal 1 menit';
        }

        return $errors;
    }
}