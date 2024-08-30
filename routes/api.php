<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ClientController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    // Routes pour les utilisateurs
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', function () {
            $data = [
                'message' => 'Liste des utilisateurs récupérée avec succès',
                'data' => ['users' => User::all()]
            ];
            return response()->json($data, 200);
        })->name('index'); // Alias: users.index

        Route::get('/{id}', function ($id) {
            $user = User::find($id);
            if (!$user) {
                return response()->json(["message" => "Utilisateur introuvable", "data" => null], 404);
            }

            $data = [
                'message' => 'Utilisateur retrouvé avec succès',
                'data' => [$user]
            ];
            return response()->json($data, 200);
        })->name('show'); // Alias: users.show

        Route::post('/', [UserController::class, 'create'])->name('store'); // Alias: users.store

        Route::match(['put', 'patch'], '/{id}', [UserController::class, 'update'])->name('update'); // Alias: users.update

        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy'); // Alias: users.destroy
    });

    // Routes pour les clients
    Route::group(['prefix' => 'clients', 'as' => 'clients.'], function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/{id}', [ClientController::class, 'show'])->name('show');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'articles', 'as' => 'articles.'], function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/{id}', [ArticleController::class, 'show'])->name('show');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '/{id}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::post('/stock', [ArticleController::class, 'updateStock'])->name('update.stock');
        // Route pour restaurer un article soft-deleted
        Route::post('/{id}/restore', [ArticleController::class, 'restore']);

        // Route pour supprimer définitivement un article
        Route::delete('/{id}/force-delete', [ArticleController::class, 'forceDelete']);
    });
});

Route::post('/v1/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/v1/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
Route::get('/v1/getAuthUser', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
