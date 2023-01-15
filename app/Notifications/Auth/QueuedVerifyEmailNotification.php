<?php

namespace App\Notifications\Auth;

use App\Enums\Queue;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

/**
 * Added a bit of customization to Laravel's default
 * verify email notification: Queueable and mail content edits
 */
class QueuedVerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    private string $notifiableName;

    public function __construct(mixed $notifiable)
    {
        $this->notifiableName = $notifiable->userProfile->first_name;
        $this->onQueue(Queue::EMAILS->value);
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return $this->buildMailMessage($verificationUrl);
    }

    /**
     * Get the verify-email notification mail message for the given URL.
     *
     * @param string $url
     * @return MailMessage
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            ->subject(Lang::get('Verify Your Email Address'))
            ->greeting('Hey, ' . $this->notifiableName)
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $url)
            ->line(Lang::get('If you did not create an account, please ignore this email.'));
    }

    /**
     * Overwrite the default verification URL as it points back to the
     * API endpoint and not the SPA
     *
     * @param $notifiable
     * @return mixed|string
     */
    protected function verificationUrl($notifiable): mixed
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        // this returns https://<api.domain.com>/api/v1/auth/email/verify/<id>/<hash>?expires=<value>&signature=<value>
        $apiRoute = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            false
        );

        // returns /api/v1/auth/email/verify/1/1
        $apiBase = route('verification.verify', ['id' => 1, 'hash' => 1], false);

        // strip the id and hash value
        $apiBase = explode('1/1', $apiBase)[0];

        // transform to: https://spa.domain.com/auth/verify-email/<id>/<hash>?expires=<value>&signature=<value>
        $frontEndUrl = config('clients.web.url.verify-email');
        return $frontEndUrl . '/' . explode($apiBase, $apiRoute)[1];
    }
}
