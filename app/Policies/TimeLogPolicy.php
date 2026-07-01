<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\TimeLog;
use App\Models\User;

class TimeLogPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::TIME_LOGS_VIEW)
            || $this->allows($user, Permission::TIME_LOGS_MANAGE)
            || $this->allows($user, Permission::TIME_LOGS_MANAGE_OWN);
    }

    public function view(User $user, TimeLog $timeLog): bool
    {
        return $this->viewAny($user) && $timeLog->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::TIME_LOGS_MANAGE)
            || $this->allows($user, Permission::TIME_LOGS_MANAGE_OWN);
    }

    public function update(User $user, TimeLog $timeLog): bool
    {
        if (! $this->view($user, $timeLog)) {
            return false;
        }

        if ($this->allows($user, Permission::TIME_LOGS_MANAGE)) {
            if ($user->actingRole() === UserRole::PROJECT_MANAGER) {
                return $timeLog->project?->managers()->where('users.id', $user->id)->exists();
            }

            return true;
        }

        return $this->allows($user, Permission::TIME_LOGS_MANAGE_OWN)
            && $timeLog->user_id === $user->id;
    }

    public function delete(User $user, TimeLog $timeLog): bool
    {
        return $this->update($user, $timeLog);
    }
}
