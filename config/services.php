<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
<<<<<<< HEAD
        'key'    => env('SES_KEY'),
=======
        'key' => env('SES_KEY'),
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
<<<<<<< HEAD
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
=======
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
        'secret' => env('STRIPE_SECRET'),
    ],

];
