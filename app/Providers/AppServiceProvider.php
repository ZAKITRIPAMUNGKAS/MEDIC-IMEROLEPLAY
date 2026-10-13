<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production if APP_URL uses HTTPS and not running locally
        $isLocalHost = in_array(request()->getHost(), ['127.0.0.1', 'localhost']) || str_contains(request()->getHost(), '192.168.');
        if (!$isLocalHost && app()->environment('production') && config('app.url') && str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Auto-detect secure cookie for HTTPS
        if (request()->secure() || request()->header('X-Forwarded-Proto') === 'https') {
            config(['session.secure' => true]);
        }

        // Register observers for Telegram notifications
        \App\Models\ChatMessage::observe(\App\Observers\ChatMessageObserver::class);
        \App\Models\Feedback::observe(\App\Observers\FeedbackObserver::class);
    }
}
