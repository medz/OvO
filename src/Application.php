<?php

namespace Medz\Wind;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    /**
     * Defined the application version.
     */
    const VERSION = '1.1.0';

    /**
     * Get the path to the application "src" directory.
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'src';
    }
}
