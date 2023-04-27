<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserRegistered;
use App\Notifications\WelcomeNotification;

class SendWelcomeEmailNotification
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered|UserCreated $event): void
    {
        $notification = new WelcomeNotification();
        $notification->afterCommit();
        $event->user->notify($notification);
    }
}
