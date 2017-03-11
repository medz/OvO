<?php

namespace Medz\Wind;

class Application extends Container
{
    /**
     * The base path for the Laravel installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Create a new application instance.
     *
     * @param string $basePath
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
    }

    /**
     * Set the base path for the application.
     *
     * @param string $basePath
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @author Seven Du <shiweidu@outlook.com>
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Get the path to the application "src" directory.
     *
     * @return string
     *
     * @author Seven Du <shiweidu@outlook.com>
     * @homepage http://medz.cn
     */
    public function path()
    {
        return $this->basePath().DIRECTORY_SEPARATOR.'src';
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function configPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function registerCoreContainerAliases()
    {
        foreach ([
            'app' => [
                \Medz\Wind\Application::class,
                \Psr\Container\ContainerInterface::class,
            ],
            'config' => [
                \Illuminate\Config\Repository::class,
            ],
        ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }
}
