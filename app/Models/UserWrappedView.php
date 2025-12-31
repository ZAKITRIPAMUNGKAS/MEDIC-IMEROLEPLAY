<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWrappedView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'viewed_at',
        'session_id',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the wrapped view.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user has viewed wrapped for a specific year
     */
    public static function hasViewed($userId, $year = null)
    {
        $year = $year ?? now()->year;

        return self::where('user_id', $userId)
            ->where('year', $year)
            ->exists();
    }

    /**
     * Record a wrapped view
     */
    public static function recordView($userId, $year = null, $sessionId = null)
    {
        $year = $year ?? now()->year;
        $sessionId = $sessionId ?? session()->getId();

        return self::create([
            'user_id' => $userId,
            'year' => $year,
            'viewed_at' => now(),
            'session_id' => $sessionId,
        ]);
    }
}
