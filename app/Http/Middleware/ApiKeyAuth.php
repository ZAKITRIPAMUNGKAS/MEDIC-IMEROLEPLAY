<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') 
            ?? $request->input('api_key') 
            ?? $request->bearerToken();

        $validApiKey = config('app.api_key', env('API_KEY'));
        $farmasiApiKey = env('FARMASI_API_KEY');
        
        // Jika API key tidak dikonfigurasi sama sekali di .env, skip validasi
        if (empty($validApiKey) && empty($farmasiApiKey)) {
            return $next($request);
        }
        
        $isAuthorized = false;
        if (!empty($validApiKey) && hash_equals((string)$validApiKey, (string)$apiKey)) {
            $isAuthorized = true;
        }
        if (!empty($farmasiApiKey) && hash_equals((string)$farmasiApiKey, (string)$apiKey)) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak valid atau tidak ditemukan. Gunakan header X-API-Key, Authorization: Bearer, atau parameter ?api_key=',
                'error_code' => 'INVALID_API_KEY'
            ], 401);
        }
        
        // Log request untuk audit
        \Log::info('API Request', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'timestamp' => now()
        ]);
        
        return $next($request);
    }
}
