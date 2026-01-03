<?php

namespace App\Observers;

use App\Models\Feedback;
use App\Services\TelegramService;
use App\Models\TelegramSetting;

class FeedbackObserver
{
    /**
     * Handle the Feedback "created" event.
     */
    public function created(Feedback $feedback): void
    {
        $settings = TelegramSetting::getSettings();

        if (!$settings->enabled || !$settings->notify_feedback) {
            return;
        }

        try {
            $telegram = new TelegramService();

            $type = $feedback->type; // 'laporan' or 'masukan'
            $subject = $feedback->subject;
            $from = $feedback->display_name ?? 'Anonymous';
            $url = url('/admin/feedback');

            $telegram->notifyNewFeedback($type, $subject, $from, $url);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram notification for feedback', [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
