<?php

namespace App\Policies;

use App\Models\User;
use App\App\Models\Talk;
use Illuminate\Auth\Access\HandlesAuthorization;

class TalkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create talks.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (! $user->phone_verified_at) {
            $this->deny(trans('auth.unauthorized'));
        }
        
        return true;
    }

    /**
     * Determine whether the user can delete the talk.
     *
     * @param  \App\Models\User  $user
     * @param  \App\App\Models\Talk  $talk
     * @return mixed
     */
    public function delete(User $user, Talk $talk)
    {
        if ($user->id === $talk->publisher_id || $user->jurisdictions->firstWhere('node', 'talk:destroy')) {
            return true;
        }

        $this->deny(trans('auth.unauthorized'));
    }
}
