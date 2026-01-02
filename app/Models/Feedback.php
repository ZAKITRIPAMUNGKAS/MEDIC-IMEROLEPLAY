<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'name',
        'user_id',
        'type',
        'subject',
        'message',
        'image',
        'status',
        'reviewed_by',
        'reviewed_at',
        'notes'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    /**
     * Get the user who submitted the feedback
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the staff member who reviewed the feedback
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope to filter by type
     */
    public function scopeType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('type', $type);
        }
        return $query;
    }

    /**
     * Get display name (user name or provided name)
     */
    public function getDisplayNameAttribute()
    {
        // If name is provided, use it; otherwise use Ticket format
        if ($this->name) {
            return $this->name;
        }

        // If user exists, use user name
        if ($this->user) {
            return $this->user->name;
        }

        // Anonymous feedback - use Ticket format
        return 'Ticket #' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get badge color based on status
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'new' => 'blue',
            'reviewed' => 'yellow',
            'resolved' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get badge color based on type
     */
    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'kritik' => 'red',
            'saran' => 'green',
            default => 'gray'
        };
    }
}
