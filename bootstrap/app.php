<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsStaff;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust reverse proxies (e.g. Cloudflare, CDN, reverse proxies) to detect real visitor IP & mitigate DDoS
        $middleware->trustProxies(at: '*', headers: SymfonyRequest::HEADER_X_FORWARDED_FOR | SymfonyRequest::HEADER_X_FORWARDED_HOST | SymfonyRequest::HEADER_X_FORWARDED_PORT | SymfonyRequest::HEADER_X_FORWARDED_PROTO);

        // Append custom web middleware (Laravel already includes cookie encryption, session, errors, and CSRF)
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastSeen::class,
        ]);

        // Redirect authenticated staff away from guest routes to staff.dashboard
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            return ($user && method_exists($user, 'isStaff') && $user->isStaff())
                ? route('staff.dashboard')
                : route('public.index');
        });

        // API group untuk middleware khusus API
        $middleware->api(append: [
            \App\Http\Middleware\ApiKeyAuth::class,
            \App\Http\Middleware\ApiRateLimit::class,
        ]);

        // Common aliases
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'staff' => EnsureUserIsStaff::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'alta_only' => \App\Http\Middleware\EnsureAltaHospital::class,
            'api.auth' => \App\Http\Middleware\ApiKeyAuth::class,
            'api.rate_limit' => \App\Http\Middleware\ApiRateLimit::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $handleTokenMismatch = function (Request $request) {
            \Log::warning('419 Page Expired handled', [
                'url' => $request->url(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);

            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session telah berakhir. Silakan refresh halaman dan coba lagi.',
                    'error' => 'TOKEN_MISMATCH',
                    'csrf_token' => csrf_token(),
                ], 419);
            }

            if ($request->is('staff/logout')) {
                if (!auth()->check()) {
                    return redirect()->route('public.index');
                }

                return redirect()->route('staff.dashboard')
                    ->with('error', 'Sesi halaman kedaluwarsa. Silakan klik tombol keluar lagi.');
            }

            if ($request->is('staff/login') || $request->is('login')) {
                return redirect()->route('staff.login')
                    ->with('error', 'Sesi halaman login telah berakhir. Silakan coba login kembali.')
                    ->withInput($request->except('_token', 'password'));
            }

            if ($request->is('staff/register')) {
                return redirect()->route('staff.register')
                    ->with('error', 'Sesi halaman registrasi telah berakhir. Silakan coba daftar kembali.')
                    ->withInput($request->except('_token', 'password', 'password_confirmation'));
            }

            if (str_contains($request->url(), 'clock-out')) {
                return redirect()->back()
                    ->with('error', 'Session telah berakhir. Silakan refresh halaman dan coba clock out lagi.')
                    ->withInput($request->except('_token', 'password'));
            }

            if (auth()->check()) {
                return redirect()->route('staff.dashboard')
                    ->with('error', 'Session halaman telah berakhir. Silakan coba lagi.');
            }

            return redirect()->route('staff.login')
                ->with('error', 'Session telah berakhir. Silakan login kembali.')
                ->withInput($request->except('_token', 'password', 'password_confirmation'));
        };

        $exceptions->render(function (TokenMismatchException $e, Request $request) use ($handleTokenMismatch) {
            return $handleTokenMismatch($request);
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($handleTokenMismatch) {
            if ($e->getStatusCode() === 419) {
                return $handleTokenMismatch($request);
            }

            return null;
        });
    })->create();
