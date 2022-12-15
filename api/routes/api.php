<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::group([
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/auth/refresh', [\App\Http\Controllers\AuthController::class, 'refreshToken']);
        Route::get('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/auth/me', [\App\Http\Controllers\AuthController::class, 'me'])->name('me');
    });

Route::apiResources([
    '/applist'       => \App\Http\Controllers\ApplistController::class,
    "/signatures"    => \App\Http\Controllers\SignatureController::class,
    "/organizations" => \App\Http\Controllers\OrganizationController::class,
]);

Route::post("/applist/remove", [\App\Http\Controllers\ApplistController::class, "massDelete"]);
Route::post("/organizations/remove", [\App\Http\Controllers\OrganizationController::class, "massDelete"]);
