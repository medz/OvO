<?php

namespace Medz\Wind\Console;

use Illuminate\Console\Application as LaravelApplication;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;

class Application extends LaravelApplication
{
    /**
     * Create a new Artisan console application.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  string  $version
     * @return void
     */
    public function __construct(Container $app, Dispatcher $events, $version)
    {
        parent::__construct($app, $events, $version);

        $this->setName(config('app.name'));
    }
}
