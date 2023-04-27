<?php

namespace App\Providers;

use App\Helpers\DateTimeHelper;
use App\Helpers\PaginationHelper;
use Illuminate\Support\ServiceProvider;

/**
 * Bind all custom-made Facades here
 */
class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('PaginationHelper', function ($app) {
            return new PaginationHelper();
        });
        $this->app->bind('DateTimeHelper', function ($app) {
            return new DateTimeHelper();
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
