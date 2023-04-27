<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Log\Events\MessageLogged;

class LogEventListener
{
    public const LOG_LEVELS = [
        'debug' => 1,
        'info' => 2,
        'notice' => 3,
        'warning' => 4,
        'error' => 5,
        'critical' => 6,
        'alert' => 7,
        'emergency' => 8,
    ];

    /**
     * Handle the event.
     */
    public function handle(MessageLogged $event): void
    {
        // Only send email notifications when in prod, uat, or development
        if (! in_array(app()->environment(), ['production', 'uat', 'development'])) {
            return;
        }

        // check the logging level set in config
        if (self::LOG_LEVELS[$event->level] < self::LOG_LEVELS[config('logging.event_listener_level')]) {
            return;
        }

        // send a notification to all users with the system alert permission
        $users = User::permission(['receive_system_alerts'])->cursor();
        /** @var User $user */
        foreach ($users as $user) {
            $user->notify(new SystemAlertNotification($event->level, $event->message));
        }
    }
}
