<?php

use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum', 'role:admin|super_user'])
    ->controller(UserController::class)
    ->name('users.')
    ->group(function () {
        /** @uses \App\Http\Controllers\UserController::store() */
        Route::post('', 'store')->name('store');

        /** @uses \App\Http\Controllers\UserController::update() */
        Route::patch('{id}', 'update')->name('update');

        /** @uses \App\Http\Controllers\UserController::index() */
        Route::get('', 'index')->name('index');

        /** @uses \App\Http\Controllers\UserController::read() */
        Route::get('{id}', 'read')->name('read');

        /** @uses \App\Http\Controllers\UserController::destroy() */
        Route::delete('{id}', 'destroy')->name('destroy');

        /** @uses \App\Http\Controllers\UserController::uploadProfilePicture() */
        Route::post('{id}/profile-picture', 'uploadProfilePicture')->name('upload.profile-picture');
    });
