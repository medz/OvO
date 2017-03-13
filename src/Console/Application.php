<?php

namespace Medz\Wind\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('phpwind Fans console.', '1.0.0-dev');
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return Command[] An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [

        ]);
    }
}
