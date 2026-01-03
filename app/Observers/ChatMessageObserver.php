<?php

namespace App\Observers;

use App\Models\ChatMessage;
use App\Services\TelegramService;
use App\Models\TelegramSetting;

class ChatMessageObserver
{
    /**
     * Handle the ChatMessage "created" event.
     */
    public function created(ChatMessage $chatMessage): void
    {
        // Only notify for user messages (not staff replies)
        if ($chatMessage->is_staff_reply) {
            return;
        }

        $settings = TelegramSetting::getSettings();

        if (!$settings->enabled || !$settings->notify_chat) {
            return;
        }

        try {
            $telegram = new TelegramService();

            $session = $chatMessage->session;
            $sessionName = $session->anonymous_name ?? 'Unknown';
            $message = $chatMessage->message;
            $url = url('/admin/chat');

            $telegram->notifyNewChat($sessionName, $message, $url);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram notification for chat', [
                'message_id' => $chatMessage->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
