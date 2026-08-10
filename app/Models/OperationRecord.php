<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_waktu',
        'lokasi',
        'jenis_operasi',
        'hospital',
        'nama_pasien',
        'diagnosa',
        'tindakan_operasi',
        'hasil_operasi',
        'catatan',
        'created_by',
        'dpjp_id',
        'medical_details',
    ];

    protected $casts = [
        'tanggal_waktu' => 'datetime',
        'medical_details' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dpjp()
    {
        return $this->belongsTo(User::class, 'dpjp_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'operation_record_members', 'operation_record_id', 'user_id');
    }

    public function photos()
    {
        return $this->hasMany(OperationPhoto::class, 'operation_record_id');
    }

    public function logs()
    {
        return $this->hasMany(OperationRecordLog::class, 'operation_record_id')->with('user')->orderBy('created_at', 'desc');
    }


    /**
     * Get base points by operation type (PHP 7.4+ compatible)
     */
    public function getBasePointsAttribute()
    {
        switch ($this->jenis_operasi) {
            case 'Operasi Mayor':
                return 60;
            case 'Operasi Minor':
                return 30;
            case 'Emergency':
                return 25;
            case 'Konsultasi Spesialisasi':
                return 40;
            default:
                return 15;
        }
    }

    /**
     * Get DPJP (lead doctor) points (base + 20 bonus)
     */
    public function getDpjpPointsAttribute()
    {
        return $this->base_points + 20;
    }

    /**
     * Get points earned by a specific user for this operation (PHP 7.4+ compatible)
     */
    public function getPointsForUser($userId)
    {
        $firstMember = $this->members->first();
        $firstMemberId = $firstMember ? $firstMember->id : null;
        $isDpjp = ($this->created_by == $userId) || ($firstMemberId == $userId);
        return $isDpjp ? $this->dpjp_points : $this->base_points;
    }
}
