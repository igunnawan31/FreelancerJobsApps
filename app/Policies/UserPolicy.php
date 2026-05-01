<?php

namespace App\Policies;

use App\Enums\UserEnums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function view(User $user, User $targetUser): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        return $user->user_id === $targetUser->user_id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function update(User $user, User $targetUser): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        return $user->user_id === $targetUser->user_id;
    }

    public function changePassword(User $user, User $targetUser): bool
    {
        return $user->user_id === $targetUser->user_id;
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->role === UserRole::ADMIN;
    }

}
