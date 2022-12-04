<?php

use App\Http\Controllers\ProfileController;

Route::middleware(['auth:sanctum'])->controller(ProfileController::class)->name('profile.')->group(function () {
    /** @uses \App\Http\Controllers\ProfileController::view() */
    Route::get('', 'view')->name('view');

    /** @uses \App\Http\Controllers\ProfileController::update() */
    Route::patch('', 'update')->name('update');
});
