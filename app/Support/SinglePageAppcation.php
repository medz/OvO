<?php

namespace App\Support;

use RuntimeException;
use App\Contracts\Applicable as ApplicableContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class SinglePageAppcation
{
    /**
     * SPAs.
     *
     * @var array
     */
    public static $applications = [
        'local' => \App\Application::class,
    ];

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create the support instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(ApplicationContract $app)
    {
        $this->app = $app;
    }

    /**
     * Publish SPA.
     *
     * @param string $name
     * @param string $applicable instance of "\App\Contracts\Applicable".
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public static function publish(string $name, string $applicable)
    {
        static::$applications[$name] = $applicable;
    }

    /**
     * Get "App\Contracts\Applicable" instance.
     *
     * @param string $spa
     * @return \App\Contracts\Applicable
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function applicable($spa = null)
    {
        $spa = $spa ?: $this->app['config']->get('app.spa');

        // Check whether the SPA is published.
        // If it is not published, the page is warned.
        if (! isset(static::$applications[$spa])) {
            throw new RuntimeException('SPA 没有被发布');
        }

        // Check whether the published SPA meets the requirements that can be applied.
        // If it is satisfied, it is returned to this SPA.
        if (($applicable = $this->app->make(static::$applications[$spa])) instanceof ApplicableContract) {
            return $applicable;
        }

        throw new RuntimeException('SPA 不是满足要求的实例');
    }
}
