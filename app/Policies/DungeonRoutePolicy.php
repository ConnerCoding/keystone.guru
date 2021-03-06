<?php

namespace App\Policies;

use App\Models\DungeonRoute;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DungeonRoutePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function view(?User $user, DungeonRoute $dungeonroute)
    {
        // Everyone can view dungeon routes (for now)
        return $dungeonroute->published;
    }

    /**
     * Determine whether the user can publish dungeon routes.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function publish(User $user, DungeonRoute $dungeonroute)
    {
        // Only authors or if the user is an admin
        return $dungeonroute->isOwnedByUser($user) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can rate a dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function rate(User $user, DungeonRoute $dungeonroute)
    {
        return !$dungeonroute->isOwnedByUser();
    }

    /**
     * Determine whether the user can favorite a dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function favorite(User $user, DungeonRoute $dungeonroute)
    {
        // All users may favorite all routes
        return true;
    }

    /**
     * Determine whether the user can clone a dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function clone(User $user, DungeonRoute $dungeonroute)
    {
        return $dungeonroute->published || $dungeonroute->isOwnedByUser($user) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function edit(?User $user, DungeonRoute $dungeonroute)
    {
        return $dungeonroute->mayUserEdit($user);
    }

    /**
     * Determine whether the user can delete the dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function delete(User $user, DungeonRoute $dungeonroute)
    {
        // Only the admin may delete routes
        return $dungeonroute->isOwnedByUser($user) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function restore(User $user, DungeonRoute $dungeonroute)
    {
        // Only authors or if the user is an admin
        return $dungeonroute->isOwnedByUser($user) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the dungeon route.
     *
     * @param  \App\User $user
     * @param  \App\Models\DungeonRoute $dungeonroute
     * @return mixed
     */
    public function forceDelete(User $user, DungeonRoute $dungeonroute)
    {
        //
        return $user->hasRole('admin');
    }
}
