<?php

namespace Medz\Wind;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class Application {
    /**
     * container.
     *
     * @var Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Create new Application.
     *
     * @param array|Psr\Container\ContainerInterface $container Either a ContainerInterface or an associative array of app settings
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function __construct($container = [])
    {
        if (is_array($container)) {
            $container = new Container($container);
        }

        if (!$container instanceof ContainerInterface) {
            throw new InvalidArgumentException('Expected a ' + ContainerInterface::class);
        }

        $this->container = $container;
    }

    /**
     * Enable access to the DI container by consumers of $app
     *
     * @return Psr\Container\ContainerInterface
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function make($abstract = null)
    {
        // $
    }
}
