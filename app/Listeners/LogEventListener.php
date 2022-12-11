<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Log\Events\MessageLogged;

class LogEventListener
{
    // use InteractsWithQueue;

    public const LOG_LEVELS = [
        'debug' => 1,
        'info' => 2,
        'notice' => 3,
        'warning' => 4,
        'error' => 5,
        'critical' => 6,
        'alert' => 7,
        'emergency' => 8
    ];

    /**
     * Handle the event.
     *
     * @param MessageLogged $event
     * @return void
     */
    public function handle(MessageLogged $event): void
    {
        // Only send email notifications when in prod or testing
        if (app()->environment() != 'production') {
            return;
        }

        if (self::LOG_LEVELS[$event->level] >= self::LOG_LEVELS[config('logging.event_listener_level')]) {
            $users = User::permission(['receive_system_alerts'])->get();
            /** @var User $user */
            foreach ($users as $user) {
                $user->notify(new SystemAlertNotification($event->level, $event->message));
            }
        }
    }
}
