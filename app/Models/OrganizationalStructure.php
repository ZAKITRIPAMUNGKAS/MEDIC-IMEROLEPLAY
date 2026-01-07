<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationalStructure extends Model
{
    protected $fillable = [
        'structure_data',
        'required_names',
        'hospital_type',
        'is_active',
        'name'
    ];

    protected $casts = [
        'structure_data' => 'array',
        'required_names' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get active structure for a hospital type
     */
    public static function getActive($hospitalType = 'ems')
    {
        return static::where('hospital_type', $hospitalType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Scope to filter by hospital type
     */
    public function scopeHospitalType($query, $type)
    {
        return $query->where('hospital_type', $type);
    }

    /**
     * Activate this structure (deactivates others of same type)
     */
    public function activate()
    {
        // Deactivate all others of same hospital type
        static::where('hospital_type', $this->hospital_type)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        // Activate this one
        $this->is_active = true;
        $this->save();
    }
}
