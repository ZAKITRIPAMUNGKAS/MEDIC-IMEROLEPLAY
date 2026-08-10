<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user) {
                    $lastSeen = $user->last_seen_at;
                    if (!$lastSeen || now()->diffInSeconds($lastSeen) >= 60) {
                        \Illuminate\Support\Facades\DB::table('users')
                            ->where('id', $user->id)
                            ->update(['last_seen_at' => now()]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if column doesn't exist yet on database
        }

        return $next($request);
    }
}
