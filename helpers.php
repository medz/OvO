<?php

namespace Medz\Wind;

/**
 * Get the available container instance.
 *
 * @param  string  $abstract
 * @return mixed|\Medz\Wind\Application
 *
 * @author Seven Du <shiweidu@outlook.com>
 */
function app($abstract = null)
{
    if (is_null($abstract)) {
        return Container::getApplication();
    }

    return Container::getApplication()->make($abstract);
}
