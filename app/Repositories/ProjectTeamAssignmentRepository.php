<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectTeamAssignmentRepositoryInterface;
use App\Models\ProjectTeamAssignment;
use App\Models\User;

class ProjectTeamAssignmentRepository extends BaseRepository implements ProjectTeamAssignmentRepositoryInterface
{
    protected function model(): string
    {
        return ProjectTeamAssignment::class;
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
        $this->applyExactFilter($query, 'team_id', $filters['team_id'] ?? null);

        if (filled($filters['search'] ?? null)) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->whereHas('project', function ($projectQuery) use ($term) {
                    $projectQuery->where('name', 'like', '%'.$term.'%')
                        ->orWhere('code', 'like', '%'.$term.'%');
                })->orWhereHas('team', function ($teamQuery) use ($term) {
                    $teamQuery->where('name', 'like', '%'.$term.'%')
                        ->orWhere('code', 'like', '%'.$term.'%');
                });
            });
        }
    }
}
