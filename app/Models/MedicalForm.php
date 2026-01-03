<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalForm extends Model
{
    protected $fillable = [
        'character_name',
        'citizen_id',
        'form_type',
        'linked_form_id', // For linking test psikologi with surat psikolog
        'hospital',
        'description',
        'form_data',
        'status',
        'processed_by',
        'processed_at',
        'notes',
        'ip_address',
        'testimoni',
        'rating',
        'testimoni_approved'
    ];

    protected $casts = [
        'form_data' => 'array',
        'processed_at' => 'datetime',
    ];

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeApprovedTestimonials($query)
    {
        return $query->where('testimoni_approved', true)
            ->whereNotNull('testimoni')
            ->whereNotNull('rating');
    }
}
