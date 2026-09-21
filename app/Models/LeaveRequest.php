<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'letter_date',
        'subject',
        'recipient',
        'applicant_name',
        'position',
        'start_date',
        'end_date',
        'duration_days',
        'reason_ic',
        'reason_ooc',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'letter_date'  => 'date',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'approved_at'  => 'datetime',
    ];

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Menunggu Persetujuan',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'yellow',
        };
    }
}
