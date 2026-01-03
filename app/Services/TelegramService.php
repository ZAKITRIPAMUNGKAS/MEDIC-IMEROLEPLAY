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

        // Debug: Log all chat IDs before sending
        Log::info('Telegram: Preparing to send notification', [
            'total_recipients' => count($this->chatIds),
            'chat_ids' => $this->chatIds,
            'message_preview' => substr($message, 0, 50) . '...'
        ]);

        $success = true;
        $sentCount = 0;
        $failedCount = 0;

        foreach ($this->chatIds as $index => $chatId) {
            $chatId = trim($chatId);
            if (empty($chatId)) {
                Log::warning("Telegram: Skipping empty chat ID at index {$index}");
                continue;
            }

            try {
                Log::info("Telegram: Sending to chat ID #{$index}: {$chatId}");

                $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => $parseMode,
                    'disable_web_page_preview' => $disablePreview,
                ]);

                if (!$response->successful()) {
                    $failedCount++;
                    Log::error('Telegram notification failed', [
                        'index' => $index,
                        'chat_id' => $chatId,
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                    $success = false;
                } else {
                    $sentCount++;
                    Log::info("Telegram: Successfully sent to chat ID {$chatId}");
                }
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Telegram notification exception', [
                    'index' => $index,
                    'chat_id' => $chatId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $success = false;
            }
        }

        // Summary log
        Log::info('Telegram: Notification batch completed', [
            'sent' => $sentCount,
            'failed' => $failedCount,
            'total' => count($this->chatIds)
        ]);

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
