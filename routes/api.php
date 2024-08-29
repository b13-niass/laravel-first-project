<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ClientController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    // Routes pour les utilisateurs
    Route::group(['as' => 'users.'], function () {
        Route::get('/users', function () {
            $data = [
                'message' => 'Liste des utilisateurs récupérée avec succès',
                'data' => ['users' => User::all()]
            ];
            return response()->json($data, 200);
        })->name('index'); // Alias: users.index

        Route::get('/users/{id}', function ($id) {
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

        Route::post('/users', [UserController::class, 'create'])->name('store'); // Alias: users.store

        Route::match(['put', 'patch'], '/users/{id}', [UserController::class, 'update'])->name('update'); // Alias: users.update

        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('destroy'); // Alias: users.destroy
    });

    // Routes pour les clients
    Route::group(['as' => 'clients.'], function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('index');
        Route::get('/clients/{id}', [ClientController::class, 'show'])->name('show');
        Route::post('/clients', [ClientController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '/clients/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('destroy');
    });
});

Route::post('/v1/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/v1/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
Route::get('/v1/getAuthUser', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
