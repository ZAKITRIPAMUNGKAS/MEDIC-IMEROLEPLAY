<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MeetingRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requested_date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'injected_attendance_id',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    // --- Relationships ---

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

    // --- Scopes ---

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // --- Helpers ---

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get formatted duration of the meeting request
     */
    public function getFormattedDuration(): string
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        // Handle cross-day
        if ($end->lt($start)) {
            $end->addDay();
        }

        $diffMinutes = $start->diffInMinutes($end);
        $hours = floor($diffMinutes / 60);
        $minutes = $diffMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } else {
            return "{$minutes} menit";
        }
    }

    /**
     * Get duration in seconds
     */
    public function getDurationInSeconds(): int
    {
        $start = Carbon::parse($this->requested_date->format('Y-m-d') . ' ' . $this->start_time);
        $end = Carbon::parse($this->requested_date->format('Y-m-d') . ' ' . $this->end_time);

        if ($end->lt($start)) {
            $end->addDay();
        }

        return $start->diffInSeconds($end);
    }

    /**
     * Get status badge info for display
     */
    public function getStatusBadge(): array
    {
        return match ($this->status) {
            'pending' => [
                'label' => 'Menunggu',
                'color' => 'yellow',
                'icon' => 'fa-clock',
            ],
            'approved' => [
                'label' => 'Disetujui',
                'color' => 'green',
                'icon' => 'fa-check-circle',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'color' => 'red',
                'icon' => 'fa-times-circle',
            ],
            default => [
                'label' => 'Unknown',
                'color' => 'gray',
                'icon' => 'fa-question-circle',
            ],
        };
    }
}
