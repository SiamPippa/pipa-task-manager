<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\ProjectTeamAssignment;
use App\Models\User;

class ProjectTeamAssignmentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::PROJECT_TEAM_ASSIGNMENTS_VIEW);
    }

    public function view(User $user, ProjectTeamAssignment $assignment): bool
    {
        return $this->viewAny($user) && $assignment->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::PROJECT_TEAM_ASSIGNMENTS_MANAGE);
    }

    public function update(User $user, ProjectTeamAssignment $assignment): bool
    {
        if (! $this->create($user) || ! $assignment->isVisibleTo($user)) {
            return false;
        }

        if ($user->actingRole() === UserRole::PROJECT_MANAGER) {
            $assignment->loadMissing('project');

            return $assignment->project
                && $assignment->project->managers()->where('users.id', $user->id)->exists();
        }

        return true;
    }

    public function delete(User $user, ProjectTeamAssignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }
}
