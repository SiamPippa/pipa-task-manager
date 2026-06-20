<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::PROJECTS_VIEW);
    }

    public function view(User $user, Project $project): bool
    {
        return $this->viewAny($user) && $project->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::PROJECTS_MANAGE);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->create($user) && $this->canManageProject($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }

    private function canManageProject(User $user, Project $project): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::DEPARTMENT_HEAD) {
            return $this->sameCompany($user, $project) && $this->sameDepartment($user, $project);
        }

        return false;
    }
}
