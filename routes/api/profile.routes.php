<?php

use App\Http\Controllers\ProfileController;

Route::middleware(['auth:sanctum', 'verified.api'])->controller(ProfileController::class)->name('profile.')->group(function () {
    /** @uses \App\Http\Controllers\ProfileController::view() */
    Route::get('', 'view')->name('view');

    /** @uses \App\Http\Controllers\ProfileController::update() */
    Route::patch('', 'update')->name('update');

    /** @uses \App\Http\Controllers\ProfileController::uploadProfilePicture() */
    Route::post('profile-picture', 'uploadProfilePicture')->name('upload.profile-picture');

    /** @uses \App\Http\Controllers\ProfileController::changePassword() */
    Route::patch('change-password', 'changePassword')->name('change.password');
});
