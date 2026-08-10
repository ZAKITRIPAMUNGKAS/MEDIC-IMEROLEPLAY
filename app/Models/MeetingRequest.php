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