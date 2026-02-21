<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Services\ActivityLogService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                try {
                    app(ActivityLogService::class)->critical('api.auth.unauthorized', [
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'reason' => 'Missing or invalid token',
                    ], $request);
                } catch (\Throwable $t) {
                    // Jangan gagal response karena log error
                }
            }

            return null; // Lanjut default handler
        });
    })->create();
