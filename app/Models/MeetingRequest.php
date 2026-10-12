<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingRequest extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'requested_date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'photo',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'injected_attendance_id',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'reviewed_at'    => 'datetime',
    ];

    protected $appends = [
        'photo_url',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'injected_attendance_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset($this->photo) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /*
    |--------------------------------------------------------------------------
    | Period Validation Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if there are any pending meeting requests in a date range for specified users
     */
    public static function hasPendingInPeriod($startDate, $endDate, $userIds = null): bool
    {
        return static::queryPendingInPeriod($startDate, $endDate, $userIds)->exists();
    }

    /**
     * Get count of pending meeting requests in a date range
     */
    public static function countPendingInPeriod($startDate, $endDate, $userIds = null): int
    {
        return static::queryPendingInPeriod($startDate, $endDate, $userIds)->count();
    }

    /**
     * Get pending meeting requests in a date range
     */
    public static function getPendingInPeriod($startDate, $endDate, $userIds = null)
    {
        return static::queryPendingInPeriod($startDate, $endDate, $userIds)
            ->with('user')
            ->get();
    }

    /**
     * Base query for pending meeting requests in a date range
     */
    public static function queryPendingInPeriod($startDate, $endDate, $userIds = null)
    {
        $startStr = $startDate instanceof \DateTimeInterface ? $startDate->format('Y-m-d') : (string) $startDate;
        $endStr = $endDate instanceof \DateTimeInterface ? $endDate->format('Y-m-d') : (string) $endDate;

        $query = static::pending()
            ->whereBetween('requested_date', [$startStr, $endStr]);

        if (!empty($userIds)) {
            $query->whereIn('user_id', (array) $userIds);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Duration Helpers
    |--------------------------------------------------------------------------
    */

    protected function getStartAndEndTime(): array
    {
        $date = $this->requested_date->format('Y-m-d');

        $start = Carbon::parse("$date {$this->start_time}");
        $end   = Carbon::parse("$date {$this->end_time}");

        if ($end->lt($start)) {
            $end->addDay();
        }

        return [$start, $end];
    }

    public function getDurationInSeconds(): int
    {
        [$start, $end] = $this->getStartAndEndTime();

        return $start->diffInSeconds($end);
    }

   public function getFormattedDuration(): string
{
    [$start, $end] = $this->getStartAndEndTime();

    $minutes = $start->diffInMinutes($end);

    $hours = floor($minutes / 60);
    $mins = $minutes % 60;

    if ($hours > 0 && $mins > 0) {
        return "{$hours} jam {$mins} menit";
    }

    if ($hours > 0) {
        return "{$hours} jam";
    }

    return "{$mins} menit";
}

    /*
    |--------------------------------------------------------------------------
    | UI Helpers
    |--------------------------------------------------------------------------
    */

   public function getStatusBadge(): array
{
    switch ($this->status) {
        case self::STATUS_PENDING:
            return [
                'label' => 'Menunggu',
                'color' => 'yellow',
                'icon'  => 'fa-clock',
            ];

        case self::STATUS_APPROVED:
            return [
                'label' => 'Disetujui',
                'color' => 'green',
                'icon'  => 'fa-check-circle',
            ];

        case self::STATUS_REJECTED:
            return [
                'label' => 'Ditolak',
                'color' => 'red',
                'icon'  => 'fa-times-circle',
            ];

        default:
            return [
                'label' => 'Unknown',
                'color' => 'gray',
                'icon'  => 'fa-question-circle',
            ];
    }
}
}