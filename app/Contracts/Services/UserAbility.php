<?php

namespace App\Contracts\Services;

interface UserAbility
{
    /**
     * get users all roles.
     *
     * @param string $role
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function roles(string $role = '');

    /**
     * Get users all abilities.
     *
     * @param string $ability
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function all(string $ability = '');
}
