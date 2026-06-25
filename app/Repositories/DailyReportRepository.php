<?php

namespace App\Repositories;

use App\Contracts\Repositories\DailyReportRepositoryInterface;
use App\Models\DailyReport;
use App\Models\User;

class DailyReportRepository extends BaseRepository implements DailyReportRepositoryInterface
{
    protected function model(): string
    {
        return DailyReport::class;
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

        $this->applyExactFilter($query, 'user_id', $filters['user_id'] ?? null);
        $this->applyExactFilter($query, 'project_id', $filters['project_id'] ?? null);
        $this->applyExactFilter($query, 'project_module_id', $filters['project_module_id'] ?? null);
        $this->applyExactFilter($query, 'task_id', $filters['task_id'] ?? null);
        $this->applyDateFromFilter($query, 'report_date', $filters['date_from'] ?? null);
        $this->applyDateToFilter($query, 'report_date', $filters['date_to'] ?? null);

        if (filled($filters['search'] ?? null)) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('summary', 'like', '%'.$term.'%')
                    ->orWhere('blocker', 'like', '%'.$term.'%')
                    ->orWhere('tomorrow_plan', 'like', '%'.$term.'%');
            });
        }
    }
}
