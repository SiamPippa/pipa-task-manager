<?php

namespace App\Repositories;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use App\Models\User;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    protected function model(): string
    {
        return Team::class;
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
        $this->applyExactFilter($query, 'team_lead_id', $filters['team_lead_id'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null, ['name', 'code']);
        $this->applyBooleanFilter($query, 'status', $filters['status'] ?? null);

        if (filled($filters['project_id'] ?? null)) {
            $query->whereHas('projectAssignments', function ($assignmentQuery) use ($filters) {
                $assignmentQuery->where('project_id', $filters['project_id']);
            });
        }
    }
}
