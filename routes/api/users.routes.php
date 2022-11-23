<?php

use App\Http\Controllers\UserController;

/**
 *  This uses Laravel 9 Route group controllers
 *  @see https://laravel.com/docs/9.x/routing#route-group-controllers
 */
Route::controller(UserController::class)->name('users.')->group(function () {
    Route::post('', 'store')->name('store');
    Route::patch('{id}', 'update')->name('update');
    Route::get('', 'index')->name('index');
    Route::get('{id}', 'read')->name('read');
    Route::delete('{id}', 'destroy')->name('destroy');
});
