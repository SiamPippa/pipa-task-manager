<?php

namespace App\Contracts\Services;

interface ProjectAnalyticsServiceInterface
{
    public function dashboard(array $filters): array;

    public function projectDetail(int $projectId, array $filters): ?array;
}
