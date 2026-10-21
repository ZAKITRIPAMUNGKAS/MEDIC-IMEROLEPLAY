<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaseApplication extends Model
{
    protected $fillable = [
        'user_id',
        'konsulen_id',
        'stase_name',
        'department',
        'start_date',
        'end_date',
        'notes',
        // Approval konsulen
        'status',
        'konsulen_approved_by',
        'konsulen_approved_at',
        'konsulen_notes',
        // Approval MSL
        'msl_approved_by',
        'msl_approved_at',
        'msl_notes',
        // Hasil kelulusan
        'passed',
        'grade',
        'certification_id',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'konsulen_approved_at'=> 'datetime',
        'msl_approved_at'     => 'datetime',
        'passed'              => 'boolean',
    ];

    const STATUS_PENDING_KONSULEN  = 'pending_konsulen';
    const STATUS_APPROVED_KONSULEN = 'approved_konsulen';
    const STATUS_PENDING_MSL       = 'pending_msl';
    const STATUS_APPROVED          = 'approved';
    const STATUS_REJECTED          = 'rejected';
    const STATUS_COMPLETED         = 'completed';

    const AVAILABLE_STASES = [
        'Stase Ilmu Bedah',
        'Stase Ilmu Penyakit Dalam',
        'Stase Kesehatan Anak (Pediatri)',
        'Stase Kebidanan & Kandungan (Obgyn)',
        'Stase Neurologi (Saraf)',
        'Stase THT-KL (Telinga Hidung Tenggorok)',
        'Stase Mata (Oftalmologi)',
        'Stase Dermatologi & Venereologi (Kulit Kelamin)',
        'Stase Kedokteran Jiwa (Psikiatri)',
        'Stase Anestesiologi & Terapi Intensif',
        'Stase Radiologi',
        'Stase Kedokteran Forensik & Medikolegal',
        'Stase Kardiologi & Kedokteran Vaskular (Jantung)',
        'Stase Pulmonologi & Kedokteran Respirasi (Paru)',
        'Stase Kegawatdaruratan (IGD & Trauma)',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsulen()
    {
        return $this->belongsTo(User::class, 'konsulen_id');
    }

    public function konsulenApprovedBy()
    {
        return $this->belongsTo(User::class, 'konsulen_approved_by');
    }

    public function mslApprovedBy()
    {
        return $this->belongsTo(User::class, 'msl_approved_by');
    }

    public function certification()
    {
        return $this->belongsTo(MemberCertification::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_konsulen'  => 'Menunggu Persetujuan Konsulen',
            'approved_konsulen' => 'Disetujui Konsulen, Menunggu MSL',
            'pending_msl'       => 'Dalam Proses Verifikasi MSL',
            'approved'          => 'Disetujui — Stase Aktif',
            'rejected'          => 'Ditolak',
            'completed'         => 'Selesai',
            default             => $this->status,
        };
    }
}
