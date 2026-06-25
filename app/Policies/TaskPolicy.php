<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;

class TaskPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::TASKS_VIEW);
    }

    public function view(User $user, Task $task): bool
    {
        return $this->viewAny($user) && $task->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::TASKS_MANAGE);
    }

    public function update(User $user, Task $task): bool
    {
        if (! $this->view($user, $task)) {
            return false;
        }

        if ($this->allows($user, Permission::TASKS_MANAGE)) {
            return true;
        }

        if ($user->actingRole() === UserRole::GENERAL) {
            return $task->assignees()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        if (! $this->allows($user, Permission::TASKS_MANAGE)) {
            return false;
        }

        if ($user->actingRole() === UserRole::ADMIN) {
            return true;
        }

        if ($user->actingRole() === UserRole::DEPARTMENT_HEAD) {
            return $task->project && $this->sameDepartment($user, $task->project);
        }

        return $this->view($user, $task);
    }
}
