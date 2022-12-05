<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Superusers cannot be deleted
     *
     * @param User $user
     * @param User $targetUser
     * @return Response
     */
    public function delete(User $user, User $targetUser): Response
    {
        if ($targetUser->hasRole(Role::SUPER_USER->value)) {
            return Response::deny();
        }

        return Response::allow();
    }

    /**
     * Superusers cannot be edited
     *
     * @param User $user
     * @param User $targetUser
     * @return Response
     */
    public function update(User $user, User $targetUser): Response
    {
        if ($targetUser->hasRole(Role::SUPER_USER->value)) {
            return Response::deny();
        }

        return Response::allow();
    }
}
