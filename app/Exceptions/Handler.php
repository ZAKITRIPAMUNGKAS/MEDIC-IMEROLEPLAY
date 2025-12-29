<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle 419 Page Expired error
        if ($exception instanceof TokenMismatchException) {
            \Log::error('419 Page Expired', [
                'url' => $request->url(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'exception' => $exception->getMessage(),
                'user_id' => auth()->id(),
                'session_id' => session()->getId()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session telah berakhir. Silakan refresh halaman dan coba lagi.',
                    'error' => 'TOKEN_MISMATCH',
                    'csrf_token' => csrf_token()
                ], 419);
            }

            // Check if it's a clock out request
            if (str_contains($request->url(), 'clock-out')) {
                return redirect()->back()
                    ->with('error', 'Session telah berakhir. Silakan refresh halaman dan coba clock out lagi.')
                    ->withInput($request->except('_token', 'password'));
            }

            return redirect()->route('staff.login')
                ->with('error', 'Session telah berakhir. Silakan login kembali.')
                ->withInput($request->except('_token', 'password'));
        }

        return parent::render($request, $exception);
    }
}