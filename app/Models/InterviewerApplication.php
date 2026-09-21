<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewerApplication extends Model
{
    protected $fillable = [
        'user_id',
        'hospital',
        'reason',
        'status',
        'action_by',
        'action_at',
        'action_notes',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30"><i class="fas fa-clock text-[10px]"></i> Menunggu ACC</span>',
            'approved' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30"><i class="fas fa-check-circle text-[10px]"></i> Disetujui (ACC)</span>',
            'rejected' => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30"><i class="fas fa-times-circle text-[10px]"></i> Ditolak</span>',
            'revoked'  => '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-500/20 text-slate-400 border border-slate-500/30"><i class="fas fa-ban text-[10px]"></i> Dicabut</span>',
            default    => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/10 text-white">' . e($this->status) . '</span>',
        };
    }
}
