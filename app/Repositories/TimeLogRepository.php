<?php

namespace App\Repositories;

use App\Contracts\Repositories\TimeLogRepositoryInterface;
use App\Models\TimeLog;

class TimeLogRepository extends BaseRepository implements TimeLogRepositoryInterface
{
    protected function model(): string
    {
        return TimeLog::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        if (filled($filters['company_id'] ?? null)) {
            $query->whereHas('project', function ($projectQuery) use ($filters) {
                $projectQuery->where('company_id', $filters['company_id']);
            });
        }

        $this->applyExactFilter($query, 'project_id', $filters['project_id'] ?? null);
        $this->applyExactFilter($query, 'task_id', $filters['task_id'] ?? null);
        $this->applyExactFilter($query, 'user_id', $filters['user_id'] ?? null);
        $this->applyDateFromFilter($query, 'start_time', $filters['date_from'] ?? null);
        $this->applyDateToFilter($query, 'start_time', $filters['date_to'] ?? null);

        if (filled($filters['search'] ?? null)) {
            $term = $filters['search'];
            $query->where('note', 'like', '%'.$term.'%');
        }
    }
}
