<?php

namespace App\Providers;

use App\Interfaces\CloudFileServices\CloudFileServiceInterface;
use App\Services\CloudFileServices\S3FileService;
use Illuminate\Support\ServiceProvider;

class CloudFileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CloudFileServiceInterface::class, function () {
            return new S3FileService();
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
