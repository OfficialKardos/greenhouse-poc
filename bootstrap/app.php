<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
        Route::middleware('web')
            ->group(base_path('routes/mobile.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'auth.mobile' => \App\Http\Middleware\MobileAuth::class,
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
            'admin.access' => \App\Http\Middleware\AdminAccess::class,
            'check.web.access' => \App\Http\Middleware\CheckWebAccess::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\Cors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();