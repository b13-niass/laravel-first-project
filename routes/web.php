<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/swagger', 'swagger');
// Route::get('/swagger', function () {
//     return view('swagger');
// });
