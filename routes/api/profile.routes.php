<?php

use App\Http\Controllers\ProfileController;

Route::middleware(['auth:sanctum', 'verified.api'])->controller(ProfileController::class)->name('profile.')->group(function () {
    /** @uses ProfileController::view */
    Route::get('', 'view')->name('view');

    /** @uses ProfileController::update */
    Route::patch('', 'update')->name('update');

    /** @uses ProfileController::uploadProfilePicture */
    Route::post('profile-picture', 'uploadProfilePicture')->name('upload.profile-picture');

    /** @uses ProfileController::changePassword */
    Route::patch('password', 'changePassword')->name('change.password');
});
