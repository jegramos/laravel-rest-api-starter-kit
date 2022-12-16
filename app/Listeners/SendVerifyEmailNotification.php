<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserRegistered;

class SendVerifyEmailNotification
{
    /**
     * Handle the event.
     *
     * @param UserRegistered|UserCreated $event
     * @return void
     */
    public function handle(UserRegistered|UserCreated $event): void
    {
        if (!$event->user->hasVerifiedEmail()) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}
