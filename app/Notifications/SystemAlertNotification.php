<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $level;
    private string $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $level, string $message)
    {
        $this->onQueue('emails');
        $this->level = strtoupper($level);
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * @Channel
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
                    ->greeting("System Notification: $this->level")
                    ->level($this->level)
                    ->line('Issue: ')
                    ->line($this->message)
                    ->line(
                        "You received this notification because you've been registered 
                        with a System Support role"
                    );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
