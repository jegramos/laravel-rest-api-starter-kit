<?php

namespace App\Providers;

use App\Helpers\AppHelper;
use App\Helpers\PaginationHelper;
use Illuminate\Support\ServiceProvider;

/**
 * Bind all custom-made Facades here
 */
class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('AppHelper', function ($app) {
            return new AppHelper();
        });
        $this->app->bind('PaginationHelper', function ($app) {
            return new PaginationHelper();
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
