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
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        $validApiKey = config('app.api_key', env('API_KEY'));
        
        // Jika API key tidak dikonfigurasi, skip validasi
        if (empty($validApiKey)) {
            return $next($request);
        }
        
        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak valid atau tidak ditemukan',
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
