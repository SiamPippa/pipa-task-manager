<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\DailyReport;
use App\Models\User;

class DailyReportPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::DAILY_REPORTS_VIEW)
            || $this->allows($user, Permission::DAILY_REPORTS_MANAGE)
            || $this->allows($user, Permission::DAILY_REPORTS_MANAGE_OWN);
    }

    public function view(User $user, DailyReport $dailyReport): bool
    {
        return $this->viewAny($user) && $dailyReport->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::DAILY_REPORTS_MANAGE)
            || $this->allows($user, Permission::DAILY_REPORTS_MANAGE_OWN);
    }

    public function update(User $user, DailyReport $dailyReport): bool
    {
        if (! $this->view($user, $dailyReport)) {
            return false;
        }

        if ($this->allows($user, Permission::DAILY_REPORTS_MANAGE)) {
            if ($user->actingRole() === UserRole::MANAGER) {
                $dailyReport->loadMissing('project');

                return $dailyReport->project && $this->sameDepartment($user, $dailyReport->project);
            }

            return true;
        }

        return $this->allows($user, Permission::DAILY_REPORTS_MANAGE_OWN)
            && $dailyReport->user_id === $user->id;
    }

    public function delete(User $user, DailyReport $dailyReport): bool
    {
        return $this->update($user, $dailyReport);
    }
}
