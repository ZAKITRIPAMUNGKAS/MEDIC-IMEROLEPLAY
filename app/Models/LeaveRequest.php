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

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Scope: cuti approved yang sedang berlangsung pada tanggal tertentu.
     */
    public function scopeActiveOnDate($query, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return $query->where('status', self::STATUS_APPROVED)
                     ->where('start_date', '<=', $date)
                     ->where('end_date', '>=', $date);
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    /**
     * Cek apakah user sedang dalam periode cuti approved hari ini.
     *
     * @param int $userId
     * @param string|null $date  Format Y-m-d, default hari ini
     * @return bool
     */
    public static function isOnLeave(int $userId, ?string $date = null): bool
    {
        $date = $date ?? now()->toDateString();

        return static::where('user_id', $userId)
            ->activeOnDate($date)
            ->exists();
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
