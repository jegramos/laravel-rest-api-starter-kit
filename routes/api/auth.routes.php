<?php

use App\Http\Controllers\AuthController;

/**
 *  This uses Laravel 9 Route group controllers
 *  @see https://laravel.com/docs/9.x/routing#route-group-controllers
 */
Route::controller(AuthController::class)->group(function () {
    /** @uses \App\Http\Controllers\AuthController::store() */
    Route::post('tokens', 'store')->name('auth.store');

    /** @uses \App\Http\Controllers\AuthController::destroy() */
    Route::middleware(['auth:sanctum', 'verified.api'])->delete('tokens', 'destroy')->name('auth.destroy');

    /** @uses \App\Http\Controllers\AuthController::fetch() */
    Route::middleware(['auth:sanctum', 'verified.api'])->get('tokens', 'fetch')->name('auth.fetch');

    /** @uses \App\Http\Controllers\AuthController::revoke() */
    Route::middleware(['auth:sanctum', 'verified.api'])->post('tokens/revoke', 'revoke')->name('auth.revoke');

    /** @uses \App\Http\Controllers\AuthController::resendEmailVerification() */
    Route::middleware(['auth:sanctum'])
        ->get('email/send-verification', 'resendEmailVerification')
        ->name('verification.resend');

    /** @uses \App\Http\Controllers\AuthController::verifyEmail() */
    Route::middleware(['auth:sanctum', 'signed'])
        ->get('email/verify/{id}/{hash}', 'verifyEmail')
        ->name('verification.verify');

    /** @uses \App\Http\Controllers\AuthController::forgotPassword() */
    Route::post('forgot-password', 'forgotPassword')->name('password.forgot');

    /** @uses \App\Http\Controllers\AuthController::resetPassword() */
    // Route::get('reset-password', 'resetPassword')->name('password.reset');
});
