<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSubRole extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'short_name',
        'hospital',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class, 'sub_role_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHospital($query, string $hospital = 'alta')
    {
        return $query->where('hospital', $hospital);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Badge style inline (background + text color) untuk ditampilkan di profil.
     */
    public function getBadgeStyleAttribute(): string
    {
        $hex = $this->color ?? '#6366f1';
        return "background-color: {$hex}22; color: {$hex}; border: 1px solid {$hex}55;";
    }

    /**
     * Cek apakah divisi ini adalah divisi tertentu by slug.
     * Contoh: $subRole->isDiv('pnd')
     */
    public function isDiv(string $slug): bool
    {
        return strtolower($this->name) === strtolower($slug);
    }
}
