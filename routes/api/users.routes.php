<?php

use App\Http\Controllers\UserController;

/**
 *  This uses Laravel 9 Route group controllers
 *  @see https://laravel.com/docs/9.x/routing#route-group-controllers
 */
Route::controller(UserController::class)->name('users.')->group(function () {
    Route::get('', 'index')->name('index');
    Route::post('', 'store')->name('store');
    Route::get('{id}', 'read')->name('read');
    Route::patch('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});
