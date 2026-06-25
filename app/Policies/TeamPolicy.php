<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;

class TeamPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::TEAMS_VIEW);
    }

    public function view(User $user, Team $team): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        if ($this->update($user, $team)) {
            return true;
        }

        return $team->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->isDepartmentHeadManager($user) || $this->isTeamLeadManager($user);
    }

    public function update(User $user, Team $team): bool
    {
        if ($this->isDepartmentHeadManager($user)) {
            return $this->canManageDepartmentTeam($user, $team);
        }

        if ($this->isTeamLeadManager($user)) {
            return $this->sameCompany($user, $team);
        }

        return false;
    }

    public function delete(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }

    private function canManageDepartmentTeam(User $user, Team $team): bool
    {
        return $this->sameCompany($user, $team) && $this->sameDepartment($user, $team);
    }

    private function isDepartmentHeadManager(User $user): bool
    {
        return $user->actingRole() === UserRole::DEPARTMENT_HEAD
            && $this->allows($user, Permission::TEAMS_MANAGE);
    }

    private function isTeamLeadManager(User $user): bool
    {
        return $user->actingRole() === UserRole::TEAM_LEAD
            && $this->allows($user, Permission::TEAMS_MANAGE_LED);
    }
}
