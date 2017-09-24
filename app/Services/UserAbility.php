<?php

namespace App\Services;

use App\Models\User as UserModel;
use Illuminate\Support\Collection;
use App\Contracts\Services\UserAbility as UserAbilityContract;

class UserAbility implements UserAbilityContract
{
    /**
     * Current user.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Get all roles or get first role.
     *
     * @param string $role
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function roles(string $role = '')
    {
        $roles = $this->user->roles()->get()->keyBy('name');

        if ($role) {
            return $roles->get($role, false);
        }

        return $roles;
    }

    /**
     * Get all abilities or get first ability.
     *
     * @param string $ability
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function all(string $ability = '')
    {
        $roles = $this->roles();
        $roles->load('abilities');

        $abilities = $roles->reduce(function ($collect, $role) {
            return $collect->merge(
                $role->abilities->keyBy('name')
            );
        }, new Collection());

        if ($ability) {
            return $abilities->get($ability, false);
        }

        return $abilities;
    }

    /**
     * Set user.
     *
     * @param \App\Models\User $user
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function setUser(UserModel $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user model.
     *
     * @return \App\Models\User
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user(): UserModel
    {
        return $this->user;
    }
}
