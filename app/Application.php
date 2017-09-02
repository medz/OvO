<?php

namespace App;

use App\Contracts\Applicable;
use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication implements Applicable
{
    /**
     * Defined the application version.
     */
    const VERSION = '1.1.0-alpha';

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

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function toResponse($request)
    {
        if ($request->path() !== '/') {
            return $this->abort(404);
        }

        return view('welcome');
    }
}
