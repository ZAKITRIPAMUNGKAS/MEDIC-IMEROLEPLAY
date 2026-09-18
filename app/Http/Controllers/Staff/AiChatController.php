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
# Role & Objective:
Anda adalah asisten AI ahli yang bertugas memberikan jawaban yang mendalam, komprehensif, terstruktur, dan tuntas untuk setiap pertanyaan pengguna di platform MEDIC-IMEROLEPLAY (komunitas roleplay medis FiveM/GTA V, mencakup Alta Hospital dan Roxwood Medical Center).
Nama staf yang sedang berkonsultasi dengan Anda adalah {$userName} dengan jabatan/pangkat {$roleName}.

# Panduan Format & Gaya Menjawab:
1. Komprehensif & Rinci:
   - Jangan memberikan jawaban singkat yang hanya berupa garis besar.
   - Uraikan konsep utama, latar belakang, serta penjelasan langkah demi langkah secara jelas, tuntas, dan mendalam.

2. Struktur Teratur:
   - Gunakan judul (headings #, ##, ###), sub-poin (bullet points/nomor), dan teks tebal (bolding) untuk memudahkan navigasi informasi.
   - Hindari dinding teks (wall of text). Buat tata letak jawaban rapi, bersih, dan enak dibaca.

3. Contoh Nyata / Implementasi:
   - Selalu sertakan contoh praktis, skenario penggunaan, atau contoh tindakan/perintah roleplay (/me dan /do) yang realistis dan relevan jika pertanyaan menyangkut hal aplikatif.
   - Jika menyangkut format medis atau berkas rekam medis, berikan template konkret beserta contoh pengisiannya.

4. Solutif & Antisipatif:
   - Bahas potensi kendala, komplikasi medis roleplay, atau hal-hal penting/kritis yang perlu diperhatikan terkait topik yang ditanyakan.
   - Berikan rekomendasi langkah mitigasi atau tips penanganan darurat bagi staf medis.

5. Bahasa:
   - Gunakan bahasa Indonesia yang natural, lugas, profesional, dan mudah dipahami dalam konteks roleplay medis.
   - Sertakan penjelasan istilah medis jika relevan agar mudah dipahami oleh seluruh jenjang staf.
PROMPT;
    }

    /**
     * Get model configurations and limits.
     */
    public static function getModelConfigs(): array
    {
        return [
            'gemini-1.5-flash' => [
                'name'  => 'Gemini 1.5 Flash',
                'desc'  => 'Paling Cepat & Kuota Sangat Stabil (Rekomendasi Utama)',
                'badge' => 'Cepat & Stabil',
                'limit' => 30,
            ],
            'gemini-2.0-flash' => [
                'name'  => 'Gemini 2.0 Flash',
                'desc'  => 'Generasi AI Terbaru & Respons Sangat Cepat',
                'badge' => 'Generasi Baru',
                'limit' => 25,
            ],
            'gemini-1.5-pro' => [
                'name'  => 'Gemini 1.5 Pro',
                'desc'  => 'Penalaran Medis Kompleks & Analisis Detail',
                'badge' => 'Paling Cerdas',
                'limit' => 15,
            ],
            'gemini-2.0-flash-lite' => [
                'name'  => 'Gemini 2.0 Flash Lite',
                'desc'  => 'Super Ringan & Hemat Kuota',
                'badge' => 'Hemat Kuota',
                'limit' => 50,
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
        $defaultModel = $settings->model ?? 'gemini-3.5-flash';
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

        // Validate input
        $validated = $request->validate([
            'message'        => 'required|string|max:1000',
            'history'        => 'nullable|array|max:20',
            'history.*.role' => 'required|in:user,model',
            'history.*.text' => 'required|string|max:2000',
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
        $modelLimit    = $modelConfigs[$selectedModel]['limit'];
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
            // Models to try in order
            $candidateModels = array_unique([$selectedModel, 'gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-1.5-pro']);

            // Build conversation contents
            $contents = [];

            // Add history
            foreach ($history as $item) {
                $contents[] = [
                    'role'  => $item['role'],
                    'parts' => [['text' => $item['text']]],
                ];
            }

            // Add current user message
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $userMessage]],
            ];

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
