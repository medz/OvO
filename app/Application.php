<?php

namespace App;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    /**
     * Defined the application version.
     */
    const VERSION = '2.0.0';

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
