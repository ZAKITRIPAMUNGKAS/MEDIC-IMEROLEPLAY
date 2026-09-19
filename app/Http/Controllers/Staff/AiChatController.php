<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AiChatController extends Controller
{
    /**
     * System prompt context for MEDIC-IMEROLEPLAY
     */
    private function getSystemPrompt(): string
    {
        $userName = Auth::user()->name ?? 'Staff';
        $roleName = Auth::user()->role->name ?? 'Staff';

        return <<<PROMPT
# Role & Identitas:
Anda adalah asisten AI resmi MEDIC-IMEROLEPLAY yang cerdas, adaptif, profesional, dan ramah. Anda melayani anggota/staf medis rumah sakit (Alta Hospital dan Roxwood Medical Center) serta pengguna portal medis.
Nama pengguna yang sedang berkonsultasi dengan Anda adalah {$userName} (Pangkat/Jabatan: {$roleName}).

# Gaya Berkomunikasi & Menjawab:
1. Natural, Luwes, dan Kontekstual:
   - Berikan jawaban alami seperti asisten AI modern pada umumnya, tidak kaku atau menggunakan pola template yang monoton.
   - Pahami maksud utama pertanyaan dan sesuaikan gaya serta kedalaman jawaban dengan kebutuhan spesifik masing-masing anggota.
   - Jika pengguna bertanya santai atau sekadar menyapa, balas dengan ramah, natural, dan solutif tanpa memaksakan penjelasan yang berlebihan.

2. Penggunaan Perintah Roleplay (/me & /do):
   - JANGAN memaksakan atau selalu menyertakan perintah /me dan /do di setiap jawaban.
   - Berikan contoh /me dan /do HANYA jika pengguna secara spesifik memintanya, atau saat pengguna bertanya tentang bagaimana cara roleplay / simulasi tindakan langsung suatu prosedur medis di FiveM.
   - Untuk pertanyaan seputar SOP, informasi medis, penjelasan istilah, tata laksana klinis, dosis/obat, alur rekam medis, administrasi, ataupun diskusi umum, jawablah secara lugas dan informatif seperti AI pada umumnya tanpa menyisipkan /me dan /do.

3. Rapi, Terstruktur & Proporsional:
   - Gunakan format markdown yang rapi (headings, bullet points, atau numbering) jika jawaban membutuhkan rincian tahapan agar mudah dibaca.
   - Hindari dinding teks yang terlalu padat. Jawab secara proporsional sesuai tingkat kebutuhan pertanyaan.

4. Bermanfaat & Solutif:
   - Berikan informasi yang akurat, tepat guna, dan relevan dengan konteks medis maupun roleplay medis komunitas.
   - Gunakan bahasa Indonesia yang baik, lugas, santun, dan bersahabat.
PROMPT;
    }

    /**
     * Get model configurations and limits.
     */
    public static function getModelConfigs(): array
    {
        return [
            'gemini-3.5-flash' => [
                'name'  => 'Gemini 3.5 Flash',
                'desc'  => 'Paling Cepat & Kuota Sangat Stabil (Rekomendasi Utama)',
                'badge' => 'Cepat & Stabil',
                'limit' => 30,
            ],
            'gemini-3.5-flash-lite' => [
                'name'  => 'Gemini 3.5 Flash Lite',
                'desc'  => 'Super Ringan & Hemat Kuota',
                'badge' => 'Hemat Kuota',
                'limit' => 50,
            ],
            'gemini-3.6-flash' => [
                'name'  => 'Gemini 3.6 Flash',
                'desc'  => 'Penalaran Medis Lanjutan & Analisis Detail',
                'badge' => 'Cerdas & Detail',
                'limit' => 25,
            ],
            'gemini-3.7-flash' => [
                'name'  => 'Gemini 3.7 Flash',
                'desc'  => 'Generasi AI Terbaru & Sangat Responsif',
                'badge' => 'Generasi Baru',
                'limit' => 20,
            ],
        ];
    }

    /**
     * Get live quota status for all models for a specific user.
     */
    public static function getModelQuotas(?int $userId = null): array
    {
        $userId  = $userId ?? (Auth::id() ?? 0);
        $configs = self::getModelConfigs();
        $results = [];

        foreach ($configs as $key => $conf) {
            $cacheKey  = 'ai_model_usage_' . $userId . '_' . str_replace('.', '_', $key);
            $used      = (int) Cache::get($cacheKey, 0);
            $limit     = $conf['limit'];
            $remaining = max(0, $limit - $used);
            $percent   = $limit > 0 ? round(($remaining / $limit) * 100) : 0;

            if ($remaining === 0) {
                $statusLabel = 'Habis';
                $statusColor = 'red';
                $dotColor    = '#ef4444';
            } elseif ($percent <= 30) {
                $statusLabel = 'Tinggal Sedikit';
                $statusColor = 'amber';
                $dotColor    = '#f59e0b';
            } else {
                $statusLabel = 'Masih Banyak';
                $statusColor = 'emerald';
                $dotColor    = '#10b981';
            }

            $results[$key] = [
                'key'          => $key,
                'name'         => $conf['name'],
                'desc'         => $conf['desc'],
                'badge'        => $conf['badge'],
                'limit'        => $limit,
                'used'         => $used,
                'remaining'    => $remaining,
                'percent'      => $percent,
                'status'       => $statusLabel,
                'status_color' => $statusColor,
                'dot_color'    => $dotColor,
            ];
        }

        return $results;
    }

    /**
     * API endpoint to get available models with live quota status.
     */
    public function getModels()
    {
        $settings     = AiSetting::getSettings();
        $configs      = self::getModelConfigs();
        $defaultModel = $settings->model ?? 'gemini-3.5-flash';
        if (!isset($configs[$defaultModel])) {
            $defaultModel = array_key_first($configs) ?? 'gemini-3.5-flash';
        }
        $quotas       = self::getModelQuotas(Auth::id());

        return response()->json([
            'success'       => true,
            'default_model' => $defaultModel,
            'models'        => $quotas,
        ]);
    }

    /**
     * Handle incoming chat message and return AI response.
     */
    public function send(Request $request)
    {
        // Check AI is configured
        $settings = AiSetting::getSettings();
        if (!$settings->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur AI belum dikonfigurasi. Hubungi admin.',
            ], 503);
        }

        // Validate input - allow long text for history to accommodate detailed AI answers
        $validated = $request->validate([
            'message'        => 'required|string|max:4000',
            'history'        => 'nullable|array|max:30',
            'history.*.role' => 'required|in:user,model',
            'history.*.text' => 'required|string|max:50000',
            'model'          => 'nullable|string|max:60',
        ]);

        $userMessage = trim($validated['message']);
        $history     = $validated['history'] ?? [];

        // Determine target model
        $modelConfigs  = self::getModelConfigs();
        $selectedModel = $validated['model'] ?? $settings->model ?? 'gemini-3.5-flash';
        if (!isset($modelConfigs[$selectedModel])) {
            $selectedModel = 'gemini-3.5-flash';
        }

        // Rate limit: per model per user per hour
        $modelLimit    = $modelConfigs[$selectedModel]['limit'] ?? 30;
        $rateLimitKey  = 'ai_model_usage_' . (Auth::id() ?? 0) . '_' . str_replace('.', '_', $selectedModel);
        $messageCount  = (int) Cache::get($rateLimitKey, 0);

        if ($messageCount >= $modelLimit) {
            return response()->json([
                'success'   => false,
                'message'   => "⏳ Batas kuota untuk {$modelConfigs[$selectedModel]['name']} sudah habis ({$modelLimit}/jam). Silakan pilih model lain yang kuotanya masih banyak!",
                'models'    => self::getModelQuotas(Auth::id()),
            ], 429);
        }

        try {
            $apiKey = trim($settings->api_key ?? '');
            // Verified working models to try in order
            $candidateModels = array_unique([$selectedModel, 'gemini-3.5-flash', 'gemini-3.5-flash-lite', 'gemini-3.6-flash', 'gemini-3.7-flash']);

            // Build conversation contents with valid turn alternation (user -> model -> user ...)
            $contents = [];
            $lastRole = null;

            // Process history items
            foreach ($history as $item) {
                $role = $item['role'] === 'model' ? 'model' : 'user';
                $text = trim($item['text'] ?? '');

                // Filter empty or error notices
                if ($text === '' || str_starts_with($text, '⚠️')) {
                    continue;
                }

                // Trim extremely long turns to preserve token budget
                if (mb_strlen($text) > 8000) {
                    $text = mb_substr($text, 0, 8000) . '...';
                }

                if ($lastRole === $role) {
                    // Merge consecutive same-role turns to keep Gemini API happy
                    $contents[count($contents) - 1]['parts'][0]['text'] .= "\n\n" . $text;
                } else {
                    $contents[] = [
                        'role'  => $role,
                        'parts' => [['text' => $text]],
                    ];
                    $lastRole = $role;
                }
            }

            // Gemini API requires multi-turn chat to begin with a 'user' turn
            while (!empty($contents) && $contents[0]['role'] !== 'user') {
                array_shift($contents);
            }

            // Append current user prompt
            if ($lastRole === 'user' && !empty($contents)) {
                $contents[count($contents) - 1]['parts'][0]['text'] .= "\n\n" . $userMessage;
            } else {
                $contents[] = [
                    'role'  => 'user',
                    'parts' => [['text' => $userMessage]],
                ];
            }

            $payload = [
                'system_instruction' => [
                    'parts' => [['text' => $this->getSystemPrompt()]],
                ],
                'contents'           => $contents,
                'generationConfig'   => [
                    'maxOutputTokens'  => 3072,
                    'temperature'      => 0.7,
                    'topP'             => 0.9,
                ],
            ];

            $lastError = 'Tidak ada respons dari AI.';
            $successText = null;
            $usedModel = null;

            foreach ($candidateModels as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);
                    $response = Http::withoutVerifying()
                        ->withHeaders([
                            'x-goog-api-key' => $apiKey,
                            'Content-Type'   => 'application/json',
                        ])
                        ->timeout(40)
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $body = $response->json();
                        $successText = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($successText) {
                            $usedModel = $model;
                            break;
                        }
                    } else {
                        $errorBody = $response->json();
                        $lastError = $errorBody['error']['message'] ?? ('HTTP ' . $response->status());
                    }
                } catch (\Exception $reqEx) {
                    $lastError = $reqEx->getMessage();
                }
            }

            if ($successText !== null) {
                // Increment specific model usage counter (expires in 1 hour)
                $usedModelKey = 'ai_model_usage_' . (Auth::id() ?? 0) . '_' . str_replace('.', '_', $usedModel);
                $currUsed = (int) Cache::get($usedModelKey, 0);
                Cache::put($usedModelKey, $currUsed + 1, now()->addHour());

                Log::info('[AI Chat] Message sent', [
                    'user_id' => Auth::id(),
                    'model'   => $usedModel,
                ]);

                $quotas = self::getModelQuotas(Auth::id());
                $remainingForUsed = $quotas[$usedModel]['remaining'] ?? 0;

                return response()->json([
                    'success'   => true,
                    'message'   => $successText,
                    'model'     => $usedModel,
                    'remaining' => $remainingForUsed,
                    'models'    => $quotas,
                ]);
            }

            Log::warning('[AI Chat] All candidate models failed', ['last_error' => $lastError]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapat respons AI: ' . $lastError,
                'models'  => self::getModelQuotas(Auth::id()),
            ], 500);

        } catch (\Exception $e) {
            Log::error('[AI Chat] Exception', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Coba lagi.',
                'models'  => self::getModelQuotas(Auth::id()),
            ], 500);
        }
    }
}
