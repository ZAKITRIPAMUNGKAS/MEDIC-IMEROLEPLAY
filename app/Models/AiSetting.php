<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'provider',
        'api_key',
        'model',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'api_key'  => 'encrypted',
    ];

    /**
     * Available Gemini models.
     */
    public static function geminiModels(): array
    {
        return [
            'gemini-3.5-flash'       => 'Gemini 3.5 Flash (Paling Cepat & Stabil - Direkomendasikan)',
            'gemini-3.5-flash-lite'  => 'Gemini 3.5 Flash Lite (Super Ringan & Responsif)',
            'gemini-3.6-flash'       => 'Gemini 3.6 Flash',
            'gemini-3.1-flash-lite'  => 'Gemini 3.1 Flash Lite',
            'gemini-3.7-flash'       => 'Gemini 3.7 Flash',
        ];
    }

    /**
     * Get or create settings singleton.
     */
    public static function getSettings(): self
    {
        try {
            return static::firstOrCreate([], [
                'provider' => 'gemini',
                'model'    => 'gemini-3.5-flash',
                'enabled'  => false,
            ]);
        } catch (\Throwable $e) {
            // Jika tabel belum ada di database, buat otomatis on-the-fly
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('ai_settings')) {
                    \Illuminate\Support\Facades\Schema::create('ai_settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->id();
                        $table->string('provider')->default('gemini');
                        $table->text('api_key')->nullable();
                        $table->string('model')->default('gemini-3.5-flash');
                        $table->boolean('enabled')->default(false);
                        $table->timestamps();
                    });

                    return static::firstOrCreate([], [
                        'provider' => 'gemini',
                        'model'    => 'gemini-3.5-flash',
                        'enabled'  => false,
                    ]);
                }
            } catch (\Throwable $ex) {
                // Ignore schema creation failure
            }

            $instance = new static([
                'provider' => 'gemini',
                'model'    => 'gemini-3.5-flash',
                'enabled'  => false,
            ]);
            return $instance;
        }
    }

    /**
     * Check if the settings are fully configured.
     */
    public function isConfigured(): bool
    {
        return $this->enabled && !empty($this->api_key);
    }

    /**
     * Mask the API key for display (show last 4 chars only).
     */
    public function getMaskedApiKeyAttribute(): string
    {
        if (empty($this->api_key)) {
            return '';
        }
        $key = $this->api_key;
        $visible = substr($key, -4);
        return str_repeat('•', max(0, strlen($key) - 4)) . $visible;
    }
}
