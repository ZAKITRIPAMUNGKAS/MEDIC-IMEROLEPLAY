<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateApplication extends Model
{
    protected $fillable = [
        'user_id',
        'type',             // vehicle_land, vehicle_heli, operation_cert
        'division',         // ga, pnd
        'title',
        'reason',
        'notes',
        'status',           // pending, approved, rejected
        'verified_by',
        'verified_at',
        'admin_notes',
        'certification_id',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function certification()
    {
        return $this->belongsTo(MemberCertification::class, 'certification_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui & Diterbitkan',
            'rejected' => 'Ditolak',
            default    => 'Menunggu Peninjauan',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'emerald',
            'rejected' => 'rose',
            default    => 'amber',
        };
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'vehicle_land'   => 'Kendaraan Darat (Ambulans)',
            'vehicle_heli'   => 'Helikopter Medis',
            'operation_cert' => 'Sertifikasi Operasi Medis',
            default          => $this->title ?? 'Sertifikat',
        };
    }
}
