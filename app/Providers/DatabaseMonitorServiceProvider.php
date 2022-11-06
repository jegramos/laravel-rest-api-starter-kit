<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class DatabaseMonitorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $threshold = 1500; // in milliseconds
        DB::whenQueryingForLongerThan($threshold, function (Connection $connection, QueryExecuted $event) {
            // Notify development team...
        });
    }
}
