<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ClientController;
use \App\Http\Controllers\Api\DetteController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'api.format.response'], function () {
    // Routes pour les utilisateurs
    Route::group(['prefix' => 'users', 'as' => 'users.', 'middleware' => 'auth:api'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index'); // Alias: users.index

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
    Route::group(['prefix' => 'clients', 'as' => 'clients.', 'middleware' => 'auth:api'], function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/{id}', [ClientController::class, 'show'])->name('show');
        Route::get('/{id}/user', [ClientController::class, 'showWithUser'])->name('showWithUser');
        Route::get('/{id}/dettes', [ClientController::class, 'showDettesUser'])->name('showDettesUser');
        Route::post('/telephone', [ClientController::class, 'showByPhone'])->name('showByPhone');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'dettes', 'as' => 'dettes.', 'middleware' => 'auth:api'], function () {
        Route::get('/', [DetteController::class, 'index'])->name('index');
        Route::post('/', [DetteController::class, 'create'])->name('create');
        Route::get('{id}', [DetteController::class, 'showWithClient'])->name('showWithClient');
        Route::get('/{id}/articles', [DetteController::class, 'showWithArticle'])->name('showWithArticle');
        Route::get('/{id}/paiements', [DetteController::class, 'showPaiementsDette'])->name('showPaiementsDette');
        Route::post('/{id}/paiements', [DetteController::class, 'addPaiementsDette'])->name('addPaiementsDette');
        Route::get('/clients/messages', [DetteController::class, 'clientsMessages'])->name('clients.messages');
    });

    Route::group(['prefix' => 'articles', 'as' => 'articles.', 'middleware' => 'auth:api'], function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/{id}', [ArticleController::class, 'show'])->name('show');
        Route::post('/libelle', [ArticleController::class, 'showByLibelle'])->name('showByLibelle');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
//        Route::match(['put', 'patch'], '/{id}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}', [ArticleController::class, 'update'])->name('update');
        Route::post('/stock', [ArticleController::class, 'updateStock'])->name('update.stock');
        // Route pour restaurer un article soft-deleted
        Route::post('/{id}/restore', [ArticleController::class, 'restore']);

        // Route pour supprimer définitivement un article
        Route::delete('/{id}/force-delete', [ArticleController::class, 'forceDelete']);
    });

    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    Route::post('/register', [ClientController::class, 'register'])->middleware(['auth:api'])->name('auth.register');

    Route::get('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('auth.refresh');
    Route::get('/getAuthUser', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:api')->name('auth.getAuthenticatedUser');


});

Route::get('/teste', function (Request $request){
    $dette = \App\Models\Dette::where('id',1)->with('client')->with('articles')->with('paiements')->get();
    return response()->json(compact('dette'), 200);
});
