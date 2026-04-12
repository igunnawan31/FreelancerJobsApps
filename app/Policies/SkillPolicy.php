<?php

namespace App\Policies;

use App\Enums\UserEnums\UserRole;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SkillPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Skill $skill): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function create(User $user): bool {
        return $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Skill $skill): bool {
        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, Skill $skill): bool {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Skill $skill): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Skill $skill): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
