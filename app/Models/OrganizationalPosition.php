<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationalPosition extends Model
{
    protected $fillable = [
        'level',
        'level_key',
        'parent_id',
        'title',
        'position_name',
        'user_id',
        'display_order',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'level' => 'integer',
        'display_order' => 'integer'
    ];

    /**
     * Relationship: User assigned to this position
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Parent position (hierarchical)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relationship: Child positions (hierarchical)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('display_order');
    }

    /**
     * Scope: Only active positions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by level
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope: Order by display_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Scope: Root level positions (no parent)
     */
    public function scopeRootLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get full hierarchy name (parent > child > grandchild)
     */
    public function getFullHierarchyNameAttribute(): string
    {
        $names = [$this->title];
        $position = $this;

        while ($position->parent) {
            $position = $position->parent;
            array_unshift($names, $position->title);
        }

        return implode(' > ', $names);
    }

    /**
     * Check if position has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
