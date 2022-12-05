<?php

use App\Enums\Permission;
use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->controller(UserController::class)->name('users.')->group(function () {
    /** @uses \App\Http\Controllers\UserController::store() */
    Route::middleware(['permission:' . Permission::CREATE_USERS->value])
        ->post('', 'store')
        ->name('store');

    /** @uses \App\Http\Controllers\UserController::update() */
    Route::middleware(['permission:' . Permission::UPDATE_USERS->value])
        ->patch('{id}', 'update')
        ->name('update');

    /** @uses \App\Http\Controllers\UserController::index() */
    Route::middleware(['permission:' . Permission::VIEW_USERS->value])
        ->get('', 'index')
        ->name('index');

    /** @uses \App\Http\Controllers\UserController::read() */
    Route::middleware(['permission:' . Permission::VIEW_USERS->value])
        ->get('{id}', 'read')
        ->name('read');

    /** @uses \App\Http\Controllers\UserController::destroy() */
    Route::middleware(['permission:' . Permission::DELETE_USERS->value])
        ->delete('{id}', 'destroy')
        ->name('destroy');

    /** @uses \App\Http\Controllers\UserController::uploadProfilePicture() */
    Route::middleware(['permission:' . Permission::UPDATE_USERS->value])
        ->post('{id}/profile-picture', 'uploadProfilePicture')
        ->name('upload.profile-picture');
});
