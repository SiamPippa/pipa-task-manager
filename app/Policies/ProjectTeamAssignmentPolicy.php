<?php

namespace App\Policies;

use App\Enums\Permission;
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
        return $this->create($user) && $assignment->isVisibleTo($user);
    }

    public function delete(User $user, ProjectTeamAssignment $assignment): bool
    {
        return $this->update($user, $assignment);
    }
}
