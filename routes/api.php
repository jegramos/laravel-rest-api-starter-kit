<?php

use Illuminate\Support\Facades\Route;

/** V1 User resource routes */
Route::prefix('/v1/users')->group(base_path('routes/api/users.routes.php'));

/** V1 Auth Routes */
Route::prefix('/v1/auth')->group(base_path('routes/api/auth.routes.php'));

/** V1 Auth Routes */
Route::prefix('/v1/profile')->group(base_path('routes/api/profile.routes.php'));
