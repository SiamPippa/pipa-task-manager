<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\TaskAssignment;
use App\Models\User;

class TaskAssignmentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::TASK_ASSIGNMENTS_VIEW);
    }

    public function view(User $user, TaskAssignment $assignment): bool
    {
        return $this->viewAny($user) && $assignment->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::TASK_ASSIGNMENTS_MANAGE);
    }

    public function update(User $user, TaskAssignment $assignment): bool
    {
        if (! $this->create($user) || ! $assignment->isVisibleTo($user)) {
            return false;
        }

        if ($user->actingRole() === UserRole::MANAGER) {
            $assignment->loadMissing('task.project');

            return $assignment->task?->project && $this->sameDepartment($user, $assignment->task->project);
        }

        return true;
    }

    public function delete(User $user, TaskAssignment $assignment): bool
    {
        if ($this->create($user) && $assignment->isVisibleTo($user)) {
            if ($user->actingRole() === UserRole::MANAGER) {
                $assignment->loadMissing('task.project');

                return $assignment->task?->project && $this->sameDepartment($user, $assignment->task->project);
            }

            return true;
        }

        return $user->actingRole() === UserRole::GENERAL && $assignment->user_id === $user->id;
    }
}
