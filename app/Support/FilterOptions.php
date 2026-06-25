<?php

namespace App\Support;

use App\Enums\TaskType;
use App\Enums\UserRole;
use App\Models\User;

class FilterOptions
{
    public static function booleanStatus(): array
    {
        return [
            ['value' => '1', 'label' => 'Active'],
            ['value' => '0', 'label' => 'Inactive'],
        ];
    }

    public static function projectDisplayStatus(): array
    {
        return [
            ['value' => 'not_started', 'label' => 'Not Started'],
            ['value' => 'in_progress', 'label' => 'In Progress'],
            ['value' => 'on_hold', 'label' => 'On Hold'],
            ['value' => 'completed', 'label' => 'Completed'],
            ['value' => 'delayed', 'label' => 'Delayed'],
        ];
    }

    public static function projectStatus(): array
    {
        return [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
            ['value' => 'completed', 'label' => 'Completed'],
        ];
    }

    public static function taskStatus(): array
    {
        return [
            ['value' => 'todo', 'label' => 'Todo'],
            ['value' => 'in_progress', 'label' => 'In Progress'],
            ['value' => 'done', 'label' => 'Done'],
        ];
    }

    public static function taskTypes(): array
    {
        return collect(TaskType::labels())
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    public static function userRoles(): array
    {
        return collect(UserRole::labels())
            ->map(fn (string $label, int $value) => ['value' => (string) $value, 'label' => $label])
            ->values()
            ->all();
    }

    public static function userRoleOptions(): array
    {
        return collect(UserRole::labels())
            ->map(fn (string $label, int $value) => (object) ['id' => $value, 'name' => $label])
            ->values()
            ->all();
    }

    public static function assignableRoleOptions(User $actor): array
    {
        $roles = UserRole::labels();

        if ($actor->actingRole() !== UserRole::ADMIN) {
            unset($roles[UserRole::ADMIN]);
        }

        return collect($roles)
            ->map(fn (string $label, int $value) => (object) ['id' => $value, 'name' => $label])
            ->values()
            ->all();
    }
}
