<?php

use App\Http\Controllers\ProfileController;

Route::controller(ProfileController::class)->name('profile.')->group(function () {
    /** @uses \App\Http\Controllers\ProfileController::view() */
    Route::get('', 'view')->name('view');

    /** @uses \App\Http\Controllers\ProfileController::update() */
    Route::patch('', 'update')->name('update');
});
