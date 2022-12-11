<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Events\UserRegistered;
use App\Listeners\LogEventListener;
use App\Listeners\SendVerifyEmailNotification;
use App\Listeners\SendWelcomeEmailNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Log\Events\MessageLogged;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            SendVerifyEmailNotification::class,
        ],
        UserCreated::class => [
            SendWelcomeEmailNotification::class,
            SendVerifyEmailNotification::class
        ],
        MessageLogged::class => [
            LogEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
