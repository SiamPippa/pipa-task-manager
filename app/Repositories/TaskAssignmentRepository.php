<?php

namespace App\Repositories;

use App\Contracts\Repositories\TaskAssignmentRepositoryInterface;
use App\Models\TaskAssignment;
use App\Models\User;

class TaskAssignmentRepository extends BaseRepository implements TaskAssignmentRepositoryInterface
{
    protected function model(): string
    {
        return TaskAssignment::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        if (filled($filters['viewer_id'] ?? null)) {
            $viewer = User::query()->find($filters['viewer_id']);

            if ($viewer) {
                $query->visibleTo($viewer);
            }
        }
        if (filled($filters['company_id'] ?? null)) {
            $query->whereHas('task.project', function ($projectQuery) use ($filters) {
                $projectQuery->where('company_id', $filters['company_id']);
            });
        }

        if (filled($filters['project_id'] ?? null)) {
            $query->whereHas('task', function ($taskQuery) use ($filters) {
                $taskQuery->where('project_id', $filters['project_id']);
            });
        }

        $this->applyExactFilter($query, 'task_id', $filters['task_id'] ?? null);
        $this->applyExactFilter($query, 'user_id', $filters['user_id'] ?? null);

        if (filled($filters['search'] ?? null)) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->whereHas('task', function ($taskQuery) use ($term) {
                    $taskQuery->where('title', 'like', '%'.$term.'%')
                        ->orWhere('jira_task_no', 'like', '%'.$term.'%');
                })->orWhereHas('user', function ($userQuery) use ($term) {
                    $userQuery->where('name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%');
                });
            });
        }
    }
}
