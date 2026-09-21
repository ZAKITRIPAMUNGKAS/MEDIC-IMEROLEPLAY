<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentPeriod extends Model
{
    protected $fillable = [
        'hospital',
        'batch_name',
        'is_open',
        'opened_by',
        'closed_by',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'is_open'   => 'boolean',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function applications()
    {
        return $this->hasMany(RecruitmentApplication::class, 'period_id');
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

    public function scopeForHospital($query, string $hospital = 'alta')
    {
        return $query->where('hospital', $hospital);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function currentOpen(string $hospital = 'alta'): ?self
    {
        return static::forHospital($hospital)->open()->latest('opened_at')->first();
    }

    public static function isOpen(string $hospital = 'alta'): bool
    {
        return static::forHospital($hospital)->open()->exists();
    }
}
