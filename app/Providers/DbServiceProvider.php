<?php

namespace App\Providers;

use App\Interfaces\Database\SchemaServiceInterface;
use App\Interfaces\HttpResources\UserServiceInterface;
use App\Services\Database\SchemaService;
use App\Services\HttpResources\UserService;
use Illuminate\Support\ServiceProvider;

class DbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(SchemaServiceInterface::class, SchemaService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
