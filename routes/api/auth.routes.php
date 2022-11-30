<?php

/**
 *  This uses Laravel 9 Route group controllers
 *  @see https://laravel.com/docs/9.x/routing#route-group-controllers
 */

use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->name('auth.')->group(function () {
    /** @uses \App\Http\Controllers\AuthController::login() */
    Route::post('login', 'login')->name('login');

    /** @uses \App\Http\Controllers\AuthController::logout() */
    Route::middleware(['auth:sanctum'])->post('logout', 'logout')->name('logout');

    /** @uses \App\Http\Controllers\AuthController::getAccessTokens() */
    Route::middleware(['auth:sanctum'])->get('tokens', 'getAccessTokens')->name('get-access-tokens');

    /** @uses \App\Http\Controllers\AuthController::revokeAccessTokens() */
    Route::middleware(['auth:sanctum'])->post('revoke-access', 'revokeAccessTokens')->name('revoke-access');

    /** @uses \App\Http\Controllers\AuthController::revokeAllAccessTokens() */
    Route::middleware(['auth:sanctum'])->post('revoke-all-access', 'revokeAllAccessTokens')->name('revoke-all-access');
});
