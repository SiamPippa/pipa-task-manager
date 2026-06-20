<?php

namespace App\Contracts\Repositories;

interface ProjectAnalyticsRepositoryInterface
{
    public function getExecutiveSummary(array $filters): array;

    public function getExecutiveKpis(array $filters): array;

    public function getAggregateTaskStatus(array $filters): array;

    public function getProjectDetail(int $projectId, array $filters): ?array;

    public function getDeveloperMetrics(int $projectId, array $filters): array;

    public function getChartSeries(int $projectId, array $filters): array;
}
