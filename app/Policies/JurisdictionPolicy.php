<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jurisdiction;
use Illuminate\Auth\Access\HandlesAuthorization;

class JurisdictionPolicy
{
    use HandlesAuthorization;

    /**
     * Has the user.
     * @param \App\Models\User $user
     * @return bool
     */
    public function has(User $user): bool
    {
        return (bool) $user->jurisdictions->firstWhere('node', 'user:jurisdiction');
    }
}
