<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCertification extends Model
{
    protected $fillable = [
        'user_id',
        'type',         // vehicle_land, vehicle_heli, visum_alive, visum_dead, operation_cert, medical_contract
        'division',     // ga, msl, pnd, ie
        'title',
        'certificate_number',
        'issued_by_user_id',
        'issue_date',
        'expiry_date',
        'file_path',
        'notes',
        'status',       // active, expired, revoked
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
    ];

    // Tipe sertifikat yang dikeluarkan per divisi
    const TYPES = [
        'ga'  => ['vehicle_land' => 'Sertifikat Kendaraan Darat', 'vehicle_heli' => 'Sertifikat Kendaraan Heli'],
        'msl' => ['visum_alive' => 'Sertifikat Visum Hidup', 'visum_dead' => 'Sertifikat Visum Mati', 'stase' => 'Sertifikat Kelulusan Stase'],
        'pnd' => [
            'operation_cert'              => 'Sertifikat Pelatihan Operasi Medis',
            'training_operasi'            => 'Sertifikat Pelatihan Operasi Medis',
            'training_surat_menyurat'     => 'Sertifikat Pelatihan Surat Menyurat',
            'training_visum_hidup'        => 'Sertifikat Pelatihan Visum Hidup Medis',
            'training_rekam_medis'        => 'Sertifikat Pelatihan Rekam Medis',
            'training_pemulsaran_jenazah' => 'Sertifikat Pelatihan Pemulsaran Jenazah',
        ],
        'ie'  => ['medical_contract' => 'Surat Perjanjian Kontrak Medis'],
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfDivision($query, string $division)
    {
        return $query->where('division', strtolower($division));
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTypeNameAttribute(): string
    {
        foreach (self::TYPES as $div => $types) {
            if (isset($types[$this->type])) return $types[$this->type];
        }
        return $this->type ?? '-';
    }
}
