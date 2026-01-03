<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TelegramSetting;

class TelegramService
{
    protected $botToken;
    protected $chatIds;
    protected $enabled;

    public function __construct()
    {
        $settings = TelegramSetting::getSettings();
        $this->botToken = $settings->bot_token;
        $this->chatIds = $settings->chat_ids_array;
        $this->enabled = $settings->enabled;
    }

    /**
     * Send notification to Telegram
     */
    public function sendNotification(string $message, array $options = []): bool
    {
        if (!$this->enabled || empty($this->botToken) || empty($this->chatIds)) {
            Log::warning('Telegram notifications disabled or not configured');
            return false;
        }

        $parseMode = $options['parse_mode'] ?? 'HTML';
        $disablePreview = $options['disable_web_page_preview'] ?? true;

        $success = true;
        foreach ($this->chatIds as $chatId) {
            $chatId = trim($chatId);
            if (empty($chatId))
                continue;

            try {
                $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => $parseMode,
                    'disable_web_page_preview' => $disablePreview,
                ]);

                if (!$response->successful()) {
                    Log::error('Telegram notification failed', [
                        'chat_id' => $chatId,
                        'response' => $response->json()
                    ]);
                    $success = false;
                }
            } catch (\Exception $e) {
                Log::error('Telegram notification exception', [
                    'chat_id' => $chatId,
                    'error' => $e->getMessage()
                ]);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send notification for new chat message
     */
    public function notifyNewChat(string $sessionName, string $message, string $url): bool
    {
        $notification = "🆕 <b>Chat Baru Masuk!</b>\n\n";
        $notification .= "👤 <b>Dari:</b> {$sessionName}\n";
        $notification .= "💬 <b>Pesan:</b> " . $this->escapeHtml(substr($message, 0, 100));

        if (strlen($message) > 100) {
            $notification .= "...";
        }

        $notification .= "\n\n🔗 <a href=\"{$url}\">Balas Sekarang</a>";

        return $this->sendNotification($notification);
    }

    /**
     * Send notification for new feedback
     */
    public function notifyNewFeedback(string $type, string $subject, string $from, string $url): bool
    {
        $emoji = $type === 'laporan' ? '⚠️' : '💡';
        $typeLabel = $type === 'laporan' ? 'Laporan' : 'Masukan';

        $notification = "{$emoji} <b>{$typeLabel} Baru!</b>\n\n";
        $notification .= "📋 <b>Subjek:</b> {$this->escapeHtml($subject)}\n";
        $notification .= "👤 <b>Dari:</b> {$this->escapeHtml($from)}\n";
        $notification .= "\n🔗 <a href=\"{$url}\">Lihat Detail</a>";

        return $this->sendNotification($notification);
    }

    /**
     * Test notification
     */
    public function sendTestNotification(): bool
    {
        $message = "✅ <b>Test Notification</b>\n\n";
        $message .= "Telegram Bot berhasil terhubung!\n";
        $message .= "Notifikasi untuk chat dan feedback sudah aktif. 🎉";

        return $this->sendNotification($message);
    }

    /**
     * Escape HTML special chars for Telegram
     */
    private function escapeHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Check if Telegram is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatIds) && $this->enabled;
    }
}
