<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\PhoneNumber;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * @var \Overtrue\EasySms\EasySms
     */
    protected $client;

    /**
     * Create the JPush Notification channel.
     * @param \Overtrue\EasySms\EasySms $client
     */
    public function __construct(EasySms $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! ($to = $notifiable->routeNotificationFor('sms', $notification)) instanceof PhoneNumber) {
            return;
        }

        $message = $notification->toSms($notifiable);

        try {
            $this->client->send($to, $message);
        } catch (\Throwable $th) {
            throw $th->getLastException();
        }
    }
}
