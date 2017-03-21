<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
<<<<<<< HEAD
            'driver'      => 'database',
            'table'       => 'jobs',
            'queue'       => 'default',
=======
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
            'retry_after' => 90,
        ],

        'beanstalkd' => [
<<<<<<< HEAD
            'driver'      => 'beanstalkd',
            'host'        => 'localhost',
            'queue'       => 'default',
=======
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
<<<<<<< HEAD
            'key'    => 'your-public-key',
            'secret' => 'your-secret-key',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
            'queue'  => 'your-queue-name',
=======
            'key' => 'your-public-key',
            'secret' => 'your-secret-key',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
            'queue' => 'your-queue-name',
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
            'region' => 'us-east-1',
        ],

        'redis' => [
<<<<<<< HEAD
            'driver'      => 'redis',
            'connection'  => 'default',
            'queue'       => 'default',
=======
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
            'retry_after' => 90,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
<<<<<<< HEAD
        'table'    => 'failed_jobs',
=======
        'table' => 'failed_jobs',
>>>>>>> 2312580af8a20e78f96f988d420c073f899cbead
    ],

];
