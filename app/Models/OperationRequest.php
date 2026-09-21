<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_operasi',
        'patient_name',
        'diagnosis',
        'planned_procedure',
        'scheduled_at',
        'dpjp_id',
        'assistant_ids', // JSON array of user IDs
        'notes',
        'status',        // pending, approved, rejected, completed
        'verified_by_pnd',
        'pnd_verified_at',
        'pnd_notes',
        'operation_record_id', // setelah selesai, link ke operation_records
    ];

    protected $casts = [
        'scheduled_at'    => 'datetime',
        'pnd_verified_at' => 'datetime',
        'assistant_ids'   => 'array',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_COMPLETED = 'completed';

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dpjp()
    {
        return $this->belongsTo(User::class, 'dpjp_id');
    }

    public function verifiedByPnd()
    {
        return $this->belongsTo(User::class, 'verified_by_pnd');
    }

    public function operationRecord()
    {
        return $this->belongsTo(OperationRecord::class);
    }

    public function getAssistantsAttribute()
    {
        if (empty($this->assistant_ids)) return collect([]);
        return User::whereIn('id', $this->assistant_ids)->get(['id','name','staff_id']);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'Menunggu Verifikasi PND',
            'approved'  => 'Disetujui — Siap Dilaksanakan',
            'rejected'  => 'Ditolak PND',
            'completed' => 'Selesai',
            default     => $this->status,
        };
    }
}
