<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
 */
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::group([
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/auth/refresh', [\App\Http\Controllers\AuthController::class, 'refreshToken']);
        Route::get('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/auth/me', [\App\Http\Controllers\AuthController::class, 'me'])->name('me');
    });
