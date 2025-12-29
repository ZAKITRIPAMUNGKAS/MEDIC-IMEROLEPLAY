<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRenameLog extends Model
{
    protected $fillable = [
        'batch_id',
        'user_id',
        'old_name',
        'new_name',
        'similarity_score',
        'match_type',
        'status',
        'error_message',
        'renamed_at',
    ];

    protected $casts = [
        'similarity_score' => 'float',
        'renamed_at' => 'datetime',
    ];

    /**
     * Get the batch this log belongs to
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(UserRenameBatch::class, 'batch_id');
    }

    /**
     * Get the user that was renamed
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
