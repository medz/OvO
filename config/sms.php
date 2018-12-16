<?php

return [
    'timeout' => (float) env('SMS_REQUEST_TIMEOUT', 5.0),
    'default' => [
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
        'gateways' => ['aliyun'],
    ],
    'gateways' => [
        'aliyun' => [
            'access_key_id' => env('SMS_ALIYUN_ACCESS_KEY_ID', ''),
            'access_key_secret' => env('SMS_ALIYUN_ACCESS_KEY_SECRET', ''),
            'sign_name' => env('SMS_ALIYUN_SIGN_NAME', ''),
        ],
    ],
    'channels' => [
        'aliyun' => [
            'text-verifcation-code' => [
                'template' => env('SMS_ALIYUN_TEXT_VERIFY_CODE_TEMPLATE', ''),
                'variable' => env('SMS_ALIYUN_TEXT_VERIFY_CODE_VAR', 'code'),
            ],
        ],
    ],
];
