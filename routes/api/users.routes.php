<?php

use App\Enums\Permission;
use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum', 'verified.api'])->controller(UserController::class)->name('users.')->group(function () {
    /** @uses UserController::store */
    Route::middleware(['permission:' . Permission::CREATE_USERS->value])
        ->post('', 'store')
        ->name('store');

    /** @uses UserController::update */
    Route::middleware(['permission:' . Permission::UPDATE_USERS->value])
        ->patch('{id}', 'update')
        ->name('update');

    /** @uses UserController::search */
    Route::middleware(['permission:' . Permission::VIEW_USERS->value])
        ->get('/search', 'search')
        ->name('search');

    /** @uses UserController::index */
    Route::middleware(['permission:' . Permission::VIEW_USERS->value])
        ->get('', 'index')
        ->name('index');

    /** @uses UserController::read */
    Route::middleware(['permission:' . Permission::VIEW_USERS->value])
        ->get('{id}', 'read')
        ->name('read');

    /** @uses UserController::destroy */
    Route::middleware(['permission:' . Permission::DELETE_USERS->value])
        ->delete('{id}', 'destroy')
        ->name('destroy');

    /** @uses UserController::uploadProfilePicture */
    Route::middleware(['permission:' . Permission::UPDATE_USERS->value])
        ->post('{id}/profile-picture', 'uploadProfilePicture')
        ->name('upload.profile-picture');
});
