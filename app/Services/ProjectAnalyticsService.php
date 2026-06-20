<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectAnalyticsRepositoryInterface;
use App\Contracts\Services\ProjectAnalyticsServiceInterface;

class ProjectAnalyticsService implements ProjectAnalyticsServiceInterface
{
    public function __construct(
        private readonly ProjectAnalyticsRepositoryInterface $repository
    ) {}

    public function dashboard(array $filters): array
    {
        return [
            'kpis' => $this->repository->getExecutiveKpis($filters),
            'projects' => $this->repository->getExecutiveSummary($filters),
            'aggregateTaskStatus' => $this->repository->getAggregateTaskStatus($filters),
        ];
    }

    public function projectDetail(int $projectId, array $filters): ?array
    {
        $detail = $this->repository->getProjectDetail($projectId, $filters);

        if (! $detail) {
            return null;
        }

        $developers = $this->repository->getDeveloperMetrics($projectId, $filters);
        $avgEfficiency = collect($developers)
            ->pluck('efficiency_score')
            ->filter()
            ->avg();

        return [
            'project' => $detail,
            'developers' => $developers,
            'team_avg_efficiency' => $avgEfficiency ? round($avgEfficiency, 2) : null,
            'charts' => $this->repository->getChartSeries($projectId, $filters),
        ];
    }
}
