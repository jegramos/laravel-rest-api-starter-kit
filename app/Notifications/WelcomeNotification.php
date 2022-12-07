<?php

namespace App\Notifications;

use App\Mail\Auth\WelcomeMessageMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @Channel
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $emailVerified = !$notifiable->email_verified_at;
        return (new MailMessage())
            ->subject(Lang::get('Welcome aboard!'))
            ->greeting('Welcome to Sunrise, ' . $notifiable->userProfile->first_name)
            ->line(Lang::get('Your account has been successfully created!'))
            ->lineIf(
                $emailVerified,
                Lang::get(
                    "We've sent a separate email to verify your account. Please go through the verification process."
                )
            )
            ->line(Lang::get('If you did not create an account, please ignore this email.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
