<?php

namespace App\Providers;

use App\Interfaces\Services\CloudFileManager\CanGenerateTempUrl;
use App\Interfaces\Services\CloudFileManager\CloudFileManagerInterface;
use App\Services\CloudFileManager\S3FileManager;
use Illuminate\Support\ServiceProvider;

class CloudFileManagementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(CloudFileManagerInterface::class, function () {
            return new S3FileManager();
        });
        $this->app->bind(CanGenerateTempUrl::class, function () {
            return new S3FileManager();
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
