<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // Pastikan ini ada untuk route API
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tambahan: Alias untuk middleware group 'api' (untuk route API protected)
        $middleware->alias([
            'api' => [
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,  // Handle stateful API (untuk Flutter/mobile)
                'throttle:api',  // Rate limiting
                \Illuminate\Routing\Middleware\SubstituteBindings::class,  // Route model binding
            ],
        ]);

        // Opsional: Group middleware jika butuh
        $middleware->api(prepend: [
            // Tambah middleware global untuk semua API route jika perlu
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();