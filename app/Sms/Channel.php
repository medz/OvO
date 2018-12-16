<?php

declare(strict_types=1);

namespace App\Sms;

use Exception;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\Contracts\MessageInterface;

class Channel
{
    protected static $vendors = [
        \Overtrue\EasySms\Gateways\AliyunGateway::class => 'Aliyun',
    ];

    protected static $channels = [
        \App\Sms\Messages\TextVerificationCode::class => 'TextVerificationCode',
    ];

    public static function make(GatewayInterface $gateway, MessageInterface $message): ScenesInterface
    {
        $className = sprintf(
            '\App\Sms\Vendor\%s\%s',
            static::findVendor($gateway),
            static::findChannel($message)
        );

        return new $className;
    }

    /**
     * Find channel.
     * @param Overtrue\EasySms\Contracts\MessageInterface $message
     * @return string
     */
    public static function findChannel(MessageInterface $message): string
    {
        foreach (static::$channels as $messageClassName => $channel) {
            if ($message instanceof $messageClassName) {
                Log::debug('Selected SMS vendor.', [
                    'channel' => $channel,
                    'className' => get_class($message),
                    'message' => $message,
                ]);

                return $channel;
            }
        }

        Log::error($msg = 'SMS channel is not supported.', [
            'className' => get_class($message),
            'message' => $message,
        ]);
        throw new Exception($msg);
    }

    /**
     * Find vendor.
     * @param \Overtrue\EasySms\Contracts\GatewayInterface $gateway
     * @return string
     */
    public static function findVendor(GatewayInterface $gateway): string
    {
        foreach (static::$vendors as $gatewayClassName => $directory) {
            if ($gateway instanceof $gatewayClassName) {
                Log::debug('Selected SMS vendor.', [
                    'vendor' => $directory,
                    'className' => get_class($gateway),
                    'gateway' => $gateway,
                ]);

                return $directory;
            }
        }

        Log::error($message = 'SMS vendors that are not supported at this time.', [
            'className' => get_class($gateway),
            'gateway' => $gateway,
        ]);
        throw new Exception($message);
    }
}
