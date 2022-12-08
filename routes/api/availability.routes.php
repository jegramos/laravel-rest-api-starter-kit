<?php

use App\Http\Controllers\AvailabilityController;

Route::controller(AvailabilityController::class)->name('availability.')->group(function () {
    /** @uses AvailabilityController::getEmailAvailability */
    Route::get('/email', 'getEmailAvailability')->name('email');

    /** @uses AvailabilityController::getUsernameAvailability */
    Route::get('/username', 'getUsernameAvailability')->name('username');
});
