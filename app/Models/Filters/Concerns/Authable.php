<?php

namespace App\Models\Filters\Concerns;

use Illuminate\Support\Facades\Auth;

trait Authable
{
    /**
     * Auth the user.
     */
    protected function auth(string $ability, $arguments)
    {
        if (Auth::guest()) {
            return false;
        }

        return Auth::user()->can($ability, $arguments);
    }
}
