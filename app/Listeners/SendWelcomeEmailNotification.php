<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserRegistered;
use App\Notifications\WelcomeNotification;

class SendWelcomeEmailNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserRegistered|UserCreated $event
     * @return void
     */
    public function handle(UserRegistered|UserCreated $event): void
    {
        $notification = new WelcomeNotification();
        $notification->afterCommit();
        $event->user->notify($notification);
    }
}
