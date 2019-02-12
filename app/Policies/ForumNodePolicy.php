<?php

namespace App\Policies;

use App\Models\User;
use App\App\Models\ForumNode;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumNodePolicy
{
    use HandlesAuthorization;

    /**
     * Has the user.
     * @param \App\Models\User $user
     * @return bool
     */
    public function has(User $user): bool
    {
        return (bool) $user->jurisdictions->firstWhere('node', 'forum:nodes-manage');
    }
}
