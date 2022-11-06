<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        /**
         * Load IDE helper for non-production environment
         * @see https://github.com/barryvdh/laravel-ide-helper
         */
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        /**
         * Notify developers if query load times reach the threshold
         */
        $threshold = 700; // in milliseconds
        DB::whenQueryingForLongerThan($threshold, function (Connection $connection, QueryExecuted $event) {
            Log::warning('DB query took too long', [
                'connection_name' => $connection->getName(),
                'event' => $event
            ]);
            /** @TODO: Notify development team...*/
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
