<?php

namespace App\Providers;

use App\Helpers\DateTimeHelper;
use App\Helpers\ValidationHelper;
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
        $this->app->bind('PaginationHelper', function ($app) {
            return new PaginationHelper();
        });
        $this->app->bind('DateTimeHelper', function ($app) {
            return new DateTimeHelper();
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
