<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

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
         $middleware->alias([
             'api.format.response' => \App\Http\Middleware\FormatResponse::class
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($e instanceof RouteNotFoundException) {
                return response()->json(['success' => 'failed', 'message' => 'Non authentifié', 'data' => null], 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'data' => null,
                    'message' => "Cette élément n'existe pas",
                ], Response::HTTP_OK);            }
        });

//        $exceptions->render(function (AuthorizationException $e, Request $request) {
//            if ($e instanceof AuthorizationException) {
//                return response()->json([
//                    'data' => null,
//                    'message' => "Cette élément n'existe pas",
//                ], Response::HTTP_OK);            }
//        });

    })->create();
