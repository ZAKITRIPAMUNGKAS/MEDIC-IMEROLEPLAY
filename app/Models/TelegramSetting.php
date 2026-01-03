<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramSetting extends Model
{
    protected $fillable = [
        'bot_token',
        'chat_ids',
        'enabled',
        'notify_chat',
        'notify_feedback',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'notify_chat' => 'boolean',
        'notify_feedback' => 'boolean',
    ];

    /**
     * Get chat IDs as array
     */
    public function getChatIdsArrayAttribute(): array
    {
        if (empty($this->chat_ids)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $this->chat_ids)));
    }

    /**
     * Get or create settings singleton
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'enabled' => false,
            'notify_chat' => true,
            'notify_feedback' => true,
        ]);
    }
}
