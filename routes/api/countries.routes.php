<?php

use App\Http\Controllers\CountryController;

Route::controller(CountryController::class)->name('countries.')->group(function () {
    /** @uses CountryController::fetch */
    Route::get('', 'fetch')->name('fetch');
});
