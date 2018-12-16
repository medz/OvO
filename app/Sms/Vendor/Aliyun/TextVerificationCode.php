<?php

declare(strict_types=1);

namespace App\Sms\Vendor\Aliyun;

use App\Sms\ScenesInterface;
use Illuminate\Support\Facades\Config;

class TextVerificationCode implements ScenesInterface
{
    public function template(): string
    {
        return Config::get('sms.channels.aliyun.text-verifcation-code.template', '');
    }

    public function data($payload): array
    {
        return [
            Config::get('sms.channels.aliyun.text-verifcation-code.variable', 'code') => $payload,
        ];
    }

    public function content($payload): string
    {
        return '';
    }
}
