<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromotionPeriod extends Model
{
    protected $fillable = [
        'name',
        'batch',
        'hospital',
        'start_date',
        'end_date',
        'is_open',
        'opened_by',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_open'    => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function applications()
    {
        return $this->hasMany(PromotionApplication::class, 'period_id');
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeForHospital($query, string $hospital = 'alta')
    {
        return $query->where('hospital', $hospital);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Ambil periode aktif terbuka untuk hospital tertentu.
     */
    public static function currentOpen(string $hospital = 'alta'): ?self
    {
        return self::where('is_open', true)->where('hospital', $hospital)->latest()->first();
    }
}
