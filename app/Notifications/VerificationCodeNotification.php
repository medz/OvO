<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['sms'];
    }

    /**
     * Get the SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toSms($notifiable)
    {
        return [
            'template' => config('sms.channels.aliyun.text-verifcation-code.template'),
            'data' => [
                config('sms.channels.aliyun.text-verifcation-code.variable') => $this->code,
            ],
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
