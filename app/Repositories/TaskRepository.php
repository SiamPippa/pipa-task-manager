<?php

namespace App\Repositories;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    protected function model(): string
    {
        return Task::class;
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
            $query->whereHas('project', function ($projectQuery) use ($filters) {
                $projectQuery->where('company_id', $filters['company_id']);
            });
        }

        $this->applyExactFilter($query, 'project_id', $filters['project_id'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null, ['title', 'jira_task_no']);
        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);
    }
}
