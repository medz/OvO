<?php

namespace App\Policies;

use App\Models\User;
use App\App\Models\ForumThread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can before forum threads.
     */
    public function before(User $user, $ability)
    {
        return (bool) $user->jurisdictions->firstWhere('node', 'forum:threads-manage');
    }

    /**
     * Determine whether the user can update the forum thread.
     *
     * @param  \App\Models\User  $user
     * @param  \App\App\Models\ForumThread  $thread
     * @return mixed
     */
    public function update(User $user, ForumThread $thread)
    {
        return $user->id === $thread->publisher_id;
    }

    /**
     * Determine whether the user can delete the forum thread.
     *
     * @param  \App\Models\User  $user
     * @param  \App\App\Models\ForumThread  $thread
     * @return mixed
     */
    public function delete(User $user, ForumThread $thread)
    {
        return $user->id === $thread->publisher_id;
    }
}
