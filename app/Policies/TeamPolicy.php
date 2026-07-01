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
        if ($user->actingRole() === UserRole::SUPER_ADMIN) {
            return true;
        }

        return $this->allows($user, Permission::TEAMS_MANAGE);
    }

    public function update(User $user, Team $team): bool
    {
        if ($user->actingRole() === UserRole::SUPER_ADMIN) {
            return true;
        }

        if ($this->allows($user, Permission::TEAMS_MANAGE)) {
            return $this->sameCompany($user, $team);
        }

        return false;
    }

    public function delete(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }
}
