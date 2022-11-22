<?php

namespace App\Providers;

use App\Helpers\GeneralHelper;
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
        $this->app->bind('GeneralHelper', function ($app) {
            return new GeneralHelper();
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
