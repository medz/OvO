<?php

namespace Medz\Wind;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;

class Container extends PimpleContainer implements ContainerInterface
{
    /**
     * store the container.
     *
     * @var Medz\Wind\Application
     */
    protected static $app;

    /**
     * Get the container instance.
     *
     * @return Medz\Wind\Application
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public static function getApplication()
    {
        if (static::$app instanceof Application) {
            static::$app = new Application();
        }

        return static::$app;
    }
}
