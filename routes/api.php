<?php

use Illuminate\Support\Facades\Route;

/** V1 User resource routes */
Route::prefix('/v1/users')->group(base_path('routes/api/users.routes.php'));

/** V1 Auth Routes */
Route::prefix('/v1/auth')->group(base_path('routes/api/auth.routes.php'));

/** V1 Auth Routes */
Route::prefix('/v1/profile')->group(base_path('routes/api/profile.routes.php'));

/** V1 Availability Routes */
Route::prefix('/v1/availability')->group(base_path('routes/api/availability.routes.php'));

/** V1 Countries */
Route::prefix('v1/countries')->group(base_path('routes/api/countries.routes.php'));
