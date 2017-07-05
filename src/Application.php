<?php

namespace Medz\Fans;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    /**
     * Defined the application version.
     */
    const VERSION = '1.1.0-alpha';

    /**
     * Get the path to the application "src" directory.
     *
     * @param string $path
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'src'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function registerCoreContainerAliases()
    {
        parent::registerCoreContainerAliases();

        // Register class aliases.
        $this->alias('app', static::class);
    }
}
