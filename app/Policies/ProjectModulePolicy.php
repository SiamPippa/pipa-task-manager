<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\ProjectModule;
use App\Models\User;

class ProjectModulePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::PROJECTS_VIEW);
    }

    public function view(User $user, ProjectModule $module): bool
    {
        return $this->viewAny($user) && $module->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::PROJECTS_MANAGE);
    }

    public function update(User $user, ProjectModule $module): bool
    {
        if (! $this->create($user) || ! $module->isVisibleTo($user)) {
            return false;
        }

        if ($user->actingRole() === UserRole::ADMIN) {
            return true;
        }

        if ($user->actingRole() === UserRole::DEPARTMENT_HEAD) {
            return $module->project && $this->sameCompany($user, $module->project) && $this->sameDepartment($user, $module->project);
        }

        if ($user->actingRole() === UserRole::MANAGER) {
            return $module->project && $this->canMutateInOwnDepartment($user, $module->project);
        }

        return false;
    }

    public function delete(User $user, ProjectModule $module): bool
    {
        return $this->update($user, $module);
    }
}
