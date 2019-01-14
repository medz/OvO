<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Jurisdiction;
use Illuminate\Auth\Access\HandlesAuthorization;

class InternationalTelephoneCodePolicy
{
    use HandlesAuthorization;

    /**
     * Has the user.
     * @param \App\Models\User $user
     * @return bool
     */
    public function has(User $user): bool
    {
        return (bool) $user->jurisdictions->firstWhere('node', 'ttc:manage');
    }
}