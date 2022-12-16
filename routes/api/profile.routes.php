<?php

use App\Enums\Permission;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth:sanctum', 'verified.api'])->controller(ProfileController::class)->name('profile.')->group(function () {
    /** @uses ProfileController::view */
    Route::middleware(['permission:' . Permission::VIEW_PROFILE->value])->get('', 'view')->name('view');

    /** @uses ProfileController::update */
    Route::middleware(['permission:' . Permission::UPDATE_PROFILE->value])->patch('', 'update')->name('update');

    /** @uses ProfileController::uploadProfilePicture */
    Route::middleware(['permission:' . Permission::UPDATE_PROFILE->value])->post('profile-picture', 'uploadProfilePicture')->name('upload.profile-picture');

    /** @uses ProfileController::changePassword */
    Route::middleware(['permission:' . Permission::UPDATE_PROFILE->value])->patch('password', 'changePassword')->name('change.password');
});
