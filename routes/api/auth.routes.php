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
    Route::post('logout', 'logout')->name('logout');

    /** @uses \App\Http\Controllers\AuthController::store() */
    Route::post('tokens', 'store')->name('store');

    /** @uses \App\Http\Controllers\AuthController::destroy() */
    Route::middleware(['auth:sanctum'])->delete('tokens', 'destroy')->name('destroy');

    /** @uses \App\Http\Controllers\AuthController::fetch() */
    Route::middleware(['auth:sanctum'])->get('tokens', 'fetch')->name('fetch');

    /** @uses \App\Http\Controllers\AuthController::revoke() */
    Route::middleware(['auth:sanctum'])->post('tokens/revoke', 'revoke')->name('revoke');
});
