<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\User;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    protected function model(): string
    {
        return Project::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        if (filled($filters['viewer_id'] ?? null)) {
            $viewer = User::query()->find($filters['viewer_id']);

            if ($viewer) {
                $query->visibleTo($viewer);
            }
        }

        $this->applyExactFilter($query, 'company_id', $filters['company_id'] ?? null);
        $this->applyExactFilter($query, 'department_id', $filters['department_id'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null, ['name', 'code']);
        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);
    }
}
