<?php

use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->controller(UserController::class)->name('users.')->group(function () {
    /** @uses \App\Http\Controllers\UserController::store() */
    Route::middleware(['permission:create_users'])->post('', 'store')->name('store');

    /** @uses \App\Http\Controllers\UserController::update() */
    Route::middleware(['permission:update_users'])->patch('{id}', 'update')->name('update');

    /** @uses \App\Http\Controllers\UserController::index() */
    Route::middleware(['permission:view_users'])->get('', 'index')->name('index');

    /** @uses \App\Http\Controllers\UserController::read() */
    Route::middleware(['permission:view_users'])->get('{id}', 'read')->name('read');

    /** @uses \App\Http\Controllers\UserController::destroy() */
    Route::middleware(['permission:delete_users'])->delete('{id}', 'destroy')->name('destroy');

    /** @uses \App\Http\Controllers\UserController::uploadProfilePicture() */
    Route::middleware(['permission:update_users'])->post('{id}/profile-picture', 'uploadProfilePicture')
        ->name('upload.profile-picture');
});
