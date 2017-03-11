<?php

namespace Medz\Wind;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use Exception;
use Psr\Container\ContainerInterface;

class Application
{
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
     *
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
     * Enable access to the DI container by consumers of $app.
     *
     * @return Psr\Container\ContainerInterface
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function make($abstract)
    {
        if ($this->getContainer()->has($abstract)) {
            return $this->getContainer()->get($abstract);
        }

        if ($abstract instanceof Closure) {
            return $abstract($this);
        }

        return $this->build($abstract);
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param string $concrete
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function buind($concrete)
    {
        $reflector = new ReflectionClass($concrete);

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface of Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        if (!$reflector->isInstantiable()) {
            throw new Exception("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        // If there are no constructors, that means there are no dependencies then
        // we can just resolve the instances of the objects right away, without
        // resolving any other types or dependencies out of these containers.
        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        // Once we have all the constructor's parameters we can create each of the
        // dependency instances and then use the reflection instances to make a
        // new instance of this class, injecting the created dependencies in.
        $instances = $this->resolveDependencies(
            $dependencies
        );

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param array $dependencies
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function resolveDependencies(array $dependencies)
    {
        var_dump($dependencies);
        exit;
    }
}
