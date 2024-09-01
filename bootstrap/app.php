<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware('api')
                ->prefix('wane')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        // $middleware->append(HealthCheck::class);
        // $middleware->web(append: Middleware::class);
        // $middleware->web(replace: [Middleware::class => $middleware::class]);
        // $middleware->web(remove: Middleware::class);
        // $middleware->alias([
        //     'authMy' => Middleware::class
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($e instanceof RouteNotFoundException) {
                return response()->json(['success' => 'failed', 'message' => 'Non authentifié', 'data' => null], 422);
            }
        });

    })->create();
