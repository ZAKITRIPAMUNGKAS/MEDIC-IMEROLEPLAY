<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatSession extends Model
{
    protected $fillable = [
        'session_token',
        'name',
        'user_id',
        'status',
        'is_read', // Admin read status
        'is_user_read' // User read status
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get anonymous display name for the session
     */
    public function getAnonymousNameAttribute()
    {
        // Simple ticket-based format: Ticket #00001
        return 'Ticket #' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
