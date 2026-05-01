<?php

namespace App\Policies;

use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\UserEnums\UserRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::FREELANCER) {
            if ($project->project_status === ProjectStatus::STATUS_OPEN) {
                return true;
            }

            if (in_array($project->project_status, [
                ProjectStatus::STATUS_REQUESTED_BY_ADMIN,
                ProjectStatus::STATUS_REQUESTED_BY_FREELANCER,
                ProjectStatus::STATUS_RUNNING,
                ProjectStatus::STATUS_REVISION,
                ProjectStatus::STATUS_COMPLETED,
                ProjectStatus::STATUS_DONE,
            ])) {
                return $project->user_id === $user->user_id;
            }

            return false;
        }

        if ($user->role === UserRole::CLIENT) {
            if (in_array($project->project_status, [
                ProjectStatus::STATUS_OPEN,
                ProjectStatus::STATUS_RUNNING,
                ProjectStatus::STATUS_REVISION,
                ProjectStatus::STATUS_COMPLETED,
                ProjectStatus::STATUS_DONE,
            ])) {
                return $project->client_id === $user->user_id;
            }

            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function request(User $user, Project $project): bool 
    {
        return $user->role === UserRole::FREELANCER
            && $project->project_status === ProjectStatus::STATUS_OPEN
            && !$user->hasActiveProject();
    }

    public function assign(User $user, Project $project): bool
    {
        if ($user->role !== UserRole::ADMIN) {
            return false;
        }

        if ($project->project_status !== ProjectStatus::STATUS_OPEN) {
            return false;
        }

        return true;
    }

    public function accept(User $user, Project $project): bool
    {
        if($user->role === UserRole::ADMIN) {
            return $project->project_status === ProjectStatus::STATUS_REQUESTED_BY_FREELANCER;
        }

        if($user->role === UserRole::FREELANCER) {
            return $project->project_status === ProjectStatus::STATUS_REQUESTED_BY_ADMIN
                && $project->user_id === $user->user_id;
        }

        return false;
    }

    public function reject(User $user, Project $project): bool
    {
        if($user->role === UserRole::ADMIN) {
            return $project->project_status === ProjectStatus::STATUS_REQUESTED_BY_FREELANCER;
        }

        if($user->role === UserRole::FREELANCER) {
            return $project->project_status === ProjectStatus::STATUS_REQUESTED_BY_ADMIN
                && $project->user_id === $user->user_id;
        }

        return false;
    }

    public function submit(User $user, Project $project): bool
    {
        return $user->role === UserRole::FREELANCER
            && $project->project_status === ProjectStatus::STATUS_RUNNING
            && $project->user_id === $user->user_id;
    }

    public function approve(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN
            && $project->project_status === ProjectStatus::STATUS_COMPLETED;
    }

    public function revise(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN
            && $project->project_status === ProjectStatus::STATUS_COMPLETED;
    }

    public function resubmit(User $user, Project $project): bool
    {
        return $user->role === UserRole::FREELANCER
            && $project->project_status === ProjectStatus::STATUS_REVISION
            && $project->user_id === $user->user_id;
    }

    public function ratings(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN
            && $project->project_status === ProjectStatus::STATUS_DONE;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Project $project): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Project $project): bool
    // {
    //     //
    // }
}
