<?php

namespace App\Providers;

use App\Interfaces\HttpResources\UserServiceInterface;
use App\Models\User;
use App\Services\HttpResources\UserService;
use Illuminate\Support\ServiceProvider;

class HttpResourceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, function () {
            return new UserService(new User());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
