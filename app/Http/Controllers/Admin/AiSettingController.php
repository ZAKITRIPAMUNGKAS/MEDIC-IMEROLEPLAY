<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSettingController extends Controller
{
    /**
     * Show the AI settings page.
     */
    public function index()
    {
        $settings = AiSetting::getSettings();
        $geminiModels = AiSetting::geminiModels();

        return view('admin.ai-settings.index', compact('settings', 'geminiModels'));
    }

    /**
     * Update AI settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'nullable|string|max:500',
            'model'   => 'nullable|string|max:100',
            'enabled' => 'boolean',
        ]);

        // If api_key field is blank string, keep existing key
        $settings = AiSetting::getSettings();

        $isEnabled = $request->boolean('enabled');

        $data = [
            'provider' => 'gemini',
            'model'    => $validated['model'] ?? 'gemini-3.5-flash',
            'enabled'  => $isEnabled,
        ];

        // Only update API key if a new one was provided
        if (!empty($validated['api_key'])) {
            $data['api_key'] = trim($validated['api_key']);
        }

        // Gunakan fill & save agar otomatis INSERT jika record belum ada
        $settings->fill($data);
        $settings->save();

        // Beri tahu admin jika diaktifkan tapi API key masih kosong
        if ($isEnabled && empty($settings->api_key)) {
            return redirect()->route('admin.ai-settings.index')
                ->with('warning', 'Integrasi AI BERHASIL DIAKTIFKAN dan tombol AI sudah muncul di aplikasi! Namun API Key masih kosong. Harap masukkan API Key Gemini Anda agar AI dapat merespons pertanyaan.');
        }

        return redirect()->route('admin.ai-settings.index')
            ->with('success', 'Pengaturan AI berhasil disimpan! Status: ' . ($isEnabled ? 'Aktif' : 'Nonaktif'));
    }

    /**
     * Test the Gemini API connection.
     */
    public function test(Request $request)
    {
        $settings = AiSetting::getSettings();

        // Ambil API Key dari request (jika dikirim dari input form) atau dari database
        $apiKey = trim($request->input('api_key') ?? '') ?: $settings->api_key;
        $primaryModel = $request->input('model') ?: ($settings->model ?? 'gemini-3.5-flash');

        if (empty($apiKey)) {
            return back()->with('error', 'API Key masih kosong! Silakan ketik atau tempelkan API Key Gemini Anda di kolom Google Gemini API Key terlebih dahulu.');
        }

        try {
            $candidateModels = array_unique([$primaryModel, 'gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-1.5-pro']);

            $response = null;
            $usedModel = $primaryModel;
            $errorMsg = 'Unknown error';

            foreach ($candidateModels as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);
                    $response = Http::withoutVerifying()
                        ->withHeaders([
                            'x-goog-api-key' => $apiKey,
                            'Content-Type'   => 'application/json',
                        ])
                        ->timeout(25)
                        ->post($url, [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => 'Respond with exactly: "MEDIC-IMEROLEPLAY AI connection successful."']
                                    ]
                                ]
                            ],
                            'generationConfig' => [
                                'maxOutputTokens' => 30,
                            ]
                        ]);

                    if ($response->successful()) {
                        $usedModel = $model;
                        break;
                    } else {
                        $errorBody = $response->json();
                        $errorMsg  = $errorBody['error']['message'] ?? ('HTTP ' . $response->status());
                    }
                } catch (\Exception $subEx) {
                    $errorMsg = $subEx->getMessage();
                }
            }

            if ($response && $response->successful()) {
                $body = $response->json();
                $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? 'OK';

                Log::info('[AI Settings] Test connection successful', [
                    'model'    => $usedModel,
                    'response' => $text,
                ]);

                return back()->with('success', "✅ Koneksi berhasil! Model: {$usedModel}. Respons: \"{$text}\"");
            }

            Log::warning('[AI Settings] Test connection failed', [
                'error' => $errorMsg,
            ]);

            return back()->with('error', "Gagal terhubung ke Gemini API: {$errorMsg}");

        } catch (\Exception $e) {
            Log::error('[AI Settings] Test connection exception', [
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
