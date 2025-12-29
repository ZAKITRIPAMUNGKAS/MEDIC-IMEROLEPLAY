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
        // Force HTTPS in production if APP_URL uses HTTPS
        if (config('app.url') && str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        
        // Auto-detect secure cookie for HTTPS
        if (request()->secure() || request()->header('X-Forwarded-Proto') === 'https') {
            config(['session.secure' => true]);
        }
    }
}
