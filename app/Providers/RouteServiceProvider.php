<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
// use Dingo\Api\Routing\Router as DingoRouter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // $this->makeApiRouter();
        // $this->mapWebRoutes();
        Route::middleware('api')->group(base_path('routes/api.php'));
    }

    /*
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    // protected function mapWebRoutes()
    // {
    //     Route::middleware('web')
    //          ->namespace($this->namespace)
    //          ->group(base_path('routes/web.php'));
    // }

    /*
     * Make API Router.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    // protected function makeApiRouter()
    // {
    //     Route::prefix('api')
    //         ->middleware('api')
    //         ->group(base_path('routes/api.php'));
    // }
}
