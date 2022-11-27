<?php

namespace App\Providers;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Resources\UserServiceInterface;
use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use App\Services\Resources\UserService;
use Illuminate\Support\ServiceProvider;

class ResourceManagementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, function () {
            return new UserService(new User());
        });
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
