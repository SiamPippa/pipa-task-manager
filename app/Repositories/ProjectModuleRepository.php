<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectModuleRepositoryInterface;
use App\Models\ProjectModule;
use App\Models\User;

class ProjectModuleRepository extends BaseRepository implements ProjectModuleRepositoryInterface
{
    protected function model(): string
    {
        return ProjectModule::class;
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
            $query->whereHas('project', fn ($projectQuery) => $projectQuery->where('company_id', $filters['company_id']));
        }

        if (filled($filters['department_id'] ?? null)) {
            $query->whereHas('project', fn ($projectQuery) => $projectQuery->where('department_id', $filters['department_id']));
        }

        $this->applyExactFilter($query, 'project_id', $filters['project_id'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null, ['name']);
    }
}
