<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectAnalyticsRepositoryInterface;
use App\Models\CompanySetting;
use App\Models\DailyReport;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TimeLog;
use App\Models\User;
use App\Support\ProjectMetricsCalculator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectAnalyticsRepository implements ProjectAnalyticsRepositoryInterface
{
    public function getExecutiveSummary(array $filters): array
    {
        $filters = $this->normalizeFilters($filters);
        $projects = $this->baseProjectQuery($filters)
            ->with(['company:id,name'])
            ->orderBy('name')
            ->get();

        if ($projects->isEmpty()) {
            return [];
        }

        $projectIds = $projects->pluck('id');
        $metricsByProject = $this->batchProjectMetrics($projectIds, $filters);

        $rows = [];

        foreach ($projects as $project) {
            $metrics = $metricsByProject[$project->id] ?? $this->emptyMetrics();
            $rows[] = $this->formatProjectRow($project, $metrics);
        }

        if (filled($filters['display_status'] ?? null)) {
            $rows = array_values(array_filter(
                $rows,
                fn (array $row) => $row['display_status'] === $filters['display_status']
            ));
        }

        return $rows;
    }

    public function getExecutiveKpis(array $filters): array
    {
        $rows = $this->getExecutiveSummary($filters);

        if ($rows === []) {
            return [
                'total_projects' => 0,
                'avg_completion_percent' => 0,
                'total_logged_hours' => 0,
                'total_estimated_hours' => 0,
                'at_risk_count' => 0,
                'total_tasks' => 0,
                'total_done_tasks' => 0,
            ];
        }

        $totalProjects = count($rows);
        $avgCompletion = round(collect($rows)->avg('completion_percent'), 1);
        $atRiskCount = collect($rows)->filter(
            fn (array $row) => in_array($row['schedule_health'], [
                ProjectMetricsCalculator::HEALTH_AT_RISK,
                ProjectMetricsCalculator::HEALTH_DELAYED,
            ], true)
        )->count();

        return [
            'total_projects' => $totalProjects,
            'avg_completion_percent' => $avgCompletion,
            'total_logged_hours' => round(collect($rows)->sum('logged_hours'), 1),
            'total_estimated_hours' => round(collect($rows)->sum('estimated_hours'), 1),
            'at_risk_count' => $atRiskCount,
            'total_tasks' => (int) collect($rows)->sum('total_tasks'),
            'total_done_tasks' => (int) collect($rows)->sum('done_tasks'),
        ];
    }

    public function getAggregateTaskStatus(array $filters): array
    {
        $filters = $this->normalizeFilters($filters);
        $projectIds = $this->baseProjectQuery($filters)->pluck('id');

        if ($projectIds->isEmpty()) {
            return ['todo' => 0, 'in_progress' => 0, 'done' => 0, 'blocked' => 0];
        }

        $statusCounts = Task::query()
            ->whereIn('project_id', $projectIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $blockedCount = $this->blockedTaskQuery($projectIds, $filters)->count('tasks.id');

        return [
            'todo' => (int) ($statusCounts['todo'] ?? 0),
            'in_progress' => (int) ($statusCounts['in_progress'] ?? 0),
            'done' => (int) ($statusCounts['done'] ?? 0),
            'blocked' => $blockedCount,
        ];
    }

    public function getProjectDetail(int $projectId, array $filters): ?array
    {
        $filters = $this->normalizeFilters($filters);
        $project = $this->baseProjectQuery($filters)
            ->with(['company:id,name', 'assignedTeams.members:id,name'])
            ->whereKey($projectId)
            ->first();

        if (! $project) {
            return null;
        }

        $metrics = $this->batchProjectMetrics(collect([$projectId]), $filters)[$projectId]
            ?? $this->emptyMetrics();

        return array_merge(
            $this->formatProjectRow($project, $metrics),
            [
                'company_name' => $project->company?->name,
                'assigned_members_list' => $metrics['assigned_members_list'],
                'active_contributors_list' => $metrics['active_contributors_list'],
            ]
        );
    }

    public function getDeveloperMetrics(int $projectId, array $filters): array
    {
        $filters = $this->normalizeFilters($filters);
        $dateFrom = $filters['date_from'];
        $dateTo = $filters['date_to'];

        $assignments = TaskAssignment::query()
            ->whereHas('task', fn (Builder $q) => $q->where('project_id', $projectId))
            ->with(['user:id,name', 'task:id,status,estimate_hours'])
            ->get();

        $loggedByUser = TimeLog::query()
            ->where('project_id', $projectId)
            ->when($dateFrom, fn (Builder $q) => $q->whereDate('start_time', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $q) => $q->whereDate('start_time', '<=', $dateTo))
            ->selectRaw('user_id, SUM(total_minutes) as total_minutes')
            ->groupBy('user_id')
            ->pluck('total_minutes', 'user_id');

        $byUser = [];

        foreach ($assignments as $assignment) {
            $userId = $assignment->user_id;

            if (! isset($byUser[$userId])) {
                $byUser[$userId] = [
                    'user_id' => $userId,
                    'name' => $assignment->user?->name ?? 'Unknown',
                    'assigned_tasks' => 0,
                    'completed_tasks' => 0,
                    'estimated_hours_done' => 0.0,
                    'logged_hours' => round(((float) ($loggedByUser[$userId] ?? 0)) / 60, 2),
                ];
            }

            $byUser[$userId]['assigned_tasks']++;

            if ($assignment->task?->status === 'done') {
                $byUser[$userId]['completed_tasks']++;
                $byUser[$userId]['estimated_hours_done'] += (float) ($assignment->task->estimate_hours ?? 0);
            }
        }

        foreach ($loggedByUser as $userId => $minutes) {
            if (! isset($byUser[$userId])) {
                $user = User::query()->find($userId, ['id', 'name']);
                $byUser[$userId] = [
                    'user_id' => $userId,
                    'name' => $user?->name ?? 'Unknown',
                    'assigned_tasks' => 0,
                    'completed_tasks' => 0,
                    'estimated_hours_done' => 0.0,
                    'logged_hours' => round($minutes / 60, 2),
                ];
            }
        }

        return collect($byUser)
            ->map(function (array $row) {
                $row['estimated_hours_done'] = round($row['estimated_hours_done'], 2);
                $row['completion_rate'] = ProjectMetricsCalculator::completionRate(
                    $row['completed_tasks'],
                    $row['assigned_tasks']
                );
                $row['efficiency_score'] = ProjectMetricsCalculator::efficiencyScore(
                    $row['estimated_hours_done'],
                    $row['logged_hours']
                );

                return $row;
            })
            ->sortByDesc('logged_hours')
            ->values()
            ->all();
    }

    public function getChartSeries(int $projectId, array $filters): array
    {
        $filters = $this->normalizeFilters($filters);
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);

        $taskStatus = Task::query()
            ->where('project_id', $projectId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $projectIds = collect([$projectId]);
        $blockedCount = $this->blockedTaskQuery($projectIds, $filters)->count('tasks.id');

        $weeklyActual = TimeLog::query()
            ->where('project_id', $projectId)
            ->whereDate('start_time', '>=', $dateFrom)
            ->whereDate('start_time', '<=', $dateTo)
            ->selectRaw('YEARWEEK(start_time, 1) as week_key, SUM(total_minutes) as total_minutes')
            ->groupBy('week_key')
            ->orderBy('week_key')
            ->pluck('total_minutes', 'week_key');

        $weeklyEstimate = Task::query()
            ->where('project_id', $projectId)
            ->selectRaw('YEARWEEK(updated_at, 1) as week_key, SUM(estimate_hours) as total_hours')
            ->groupBy('week_key')
            ->orderBy('week_key')
            ->pluck('total_hours', 'week_key');

        $weekKeys = $weeklyActual->keys()->merge($weeklyEstimate->keys())->unique()->sort()->values();
        $weekLabels = [];
        $actualSeries = [];
        $estimateSeries = [];

        foreach ($weekKeys as $weekKey) {
            $weekLabels[] = 'W'.substr((string) $weekKey, -2);
            $actualSeries[] = round(((float) ($weeklyActual[$weekKey] ?? 0)) / 60, 1);
            $estimateSeries[] = round((float) ($weeklyEstimate[$weekKey] ?? 0), 1);
        }

        $developers = $this->getDeveloperMetrics($projectId, $filters);
        $devNames = array_column($developers, 'name');
        $devHours = array_column($developers, 'logged_hours');
        $devEfficiency = array_map(
            fn ($score) => $score ?? 0,
            array_column($developers, 'efficiency_score')
        );

        $completionTrend = Task::query()
            ->where('project_id', $projectId)
            ->where('status', 'done')
            ->whereDate('updated_at', '>=', $dateFrom)
            ->whereDate('updated_at', '<=', $dateTo)
            ->selectRaw('DATE(updated_at) as done_date, COUNT(*) as count')
            ->groupBy('done_date')
            ->orderBy('done_date')
            ->get();

        $cumulative = 0;
        $trendLabels = [];
        $trendSeries = [];

        foreach ($completionTrend as $point) {
            $cumulative += (int) $point->count;
            $trendLabels[] = Carbon::parse($point->done_date)->format('M j');
            $trendSeries[] = $cumulative;
        }

        return [
            'taskStatus' => [
                'labels' => ['Pending', 'In Progress', 'Completed', 'Blocked'],
                'series' => [
                    (int) ($taskStatus['todo'] ?? 0),
                    (int) ($taskStatus['in_progress'] ?? 0),
                    (int) ($taskStatus['done'] ?? 0),
                    $blockedCount,
                ],
            ],
            'hoursComparison' => [
                'labels' => $weekLabels ?: ['No data'],
                'estimated' => $estimateSeries ?: [0],
                'actual' => $actualSeries ?: [0],
            ],
            'workload' => [
                'labels' => $devNames ?: ['No data'],
                'series' => $devHours ?: [0],
            ],
            'efficiency' => [
                'labels' => $devNames ?: ['No data'],
                'series' => $devEfficiency ?: [0],
            ],
            'completionTrend' => [
                'labels' => $trendLabels ?: ['No data'],
                'series' => $trendSeries ?: [0],
            ],
        ];
    }

    private function normalizeFilters(array $filters): array
    {
        if (blank($filters['date_from'] ?? null)) {
            $filters['date_from'] = now()->subDays(90)->toDateString();
        }

        if (blank($filters['date_to'] ?? null)) {
            $filters['date_to'] = now()->toDateString();
        }

        return $filters;
    }

    private function resolveViewer(array $filters): ?User
    {
        if (blank($filters['viewer_id'] ?? null)) {
            return null;
        }

        return User::query()->find($filters['viewer_id']);
    }

    private function baseProjectQuery(array $filters): Builder
    {
        $query = Project::query()->with(['company:id,name']);

        if ($viewer = $this->resolveViewer($filters)) {
            $query->visibleTo($viewer);
        }

        if (filled($filters['company_id'] ?? null)) {
            $query->where('company_id', $filters['company_id']);
        }

        if (filled($filters['project_id'] ?? null)) {
            $query->whereKey($filters['project_id']);
        }

        if (filled($filters['status'] ?? null)) {
            $query->where('status', $filters['status']);
        }

        if (filled($filters['team_id'] ?? null)) {
            $query->whereHas('projectTeamAssignments', fn (Builder $q) => $q->where('team_id', $filters['team_id']));
        }

        if (filled($filters['team_lead_id'] ?? null)) {
            $query->whereHas('projectTeamAssignments.team', fn (Builder $q) => $q->where('team_lead_id', $filters['team_lead_id']));
        }

        if (filled($filters['user_id'] ?? null)) {
            $userId = $filters['user_id'];
            $query->where(function (Builder $q) use ($userId, $filters) {
                $q->whereHas('tasks.assignments', fn (Builder $aq) => $aq->where('user_id', $userId))
                    ->orWhereHas('timeLogs', function (Builder $tq) use ($userId, $filters) {
                        $tq->where('user_id', $userId);
                        $this->applyTimeLogDateFilters($tq, $filters);
                    });
            });
        }

        return $query;
    }

    private function batchProjectMetrics(Collection $projectIds, array $filters): array
    {
        if ($projectIds->isEmpty()) {
            return [];
        }

        $ids = $projectIds->values()->all();
        $dateFrom = $filters['date_from'];
        $dateTo = $filters['date_to'];

        $taskCounts = Task::query()
            ->whereIn('project_id', $ids)
            ->selectRaw("project_id, status, COUNT(*) as count")
            ->groupBy('project_id', 'status')
            ->get()
            ->groupBy('project_id');

        $estimatedHours = Task::query()
            ->whereIn('project_id', $ids)
            ->selectRaw('project_id, SUM(estimate_hours) as total')
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $remainingHours = Task::query()
            ->whereIn('project_id', $ids)
            ->where('status', '!=', 'done')
            ->selectRaw('project_id, SUM(estimate_hours) as total')
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $loggedMinutes = TimeLog::query()
            ->whereIn('project_id', $ids)
            ->when($dateFrom, fn (Builder $q) => $q->whereDate('start_time', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $q) => $q->whereDate('start_time', '<=', $dateTo))
            ->selectRaw('project_id, SUM(total_minutes) as total')
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $lastActivity = TimeLog::query()
            ->whereIn('project_id', $ids)
            ->selectRaw('project_id, MAX(start_time) as last_activity')
            ->groupBy('project_id')
            ->pluck('last_activity', 'project_id');

        $blockedByProject = $this->blockedTaskQuery($projectIds, $filters)
            ->selectRaw('tasks.project_id, COUNT(DISTINCT tasks.id) as count')
            ->groupBy('tasks.project_id')
            ->pluck('count', 'project_id');

        $overdueByProject = $this->overdueTaskQuery($projectIds)
            ->selectRaw('tasks.project_id, COUNT(DISTINCT tasks.id) as count')
            ->groupBy('tasks.project_id')
            ->pluck('count', 'project_id');

        $activeContributors = TimeLog::query()
            ->whereIn('project_id', $ids)
            ->when($dateFrom, fn (Builder $q) => $q->whereDate('start_time', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $q) => $q->whereDate('start_time', '<=', $dateTo))
            ->selectRaw('project_id, COUNT(DISTINCT user_id) as count')
            ->groupBy('project_id')
            ->pluck('count', 'project_id');

        $activeContributorUsers = TimeLog::query()
            ->whereIn('project_id', $ids)
            ->when($dateFrom, fn (Builder $q) => $q->whereDate('start_time', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $q) => $q->whereDate('start_time', '<=', $dateTo))
            ->join('users', 'users.id', '=', 'time_logs.user_id')
            ->selectRaw('time_logs.project_id, users.id as user_id, users.name')
            ->distinct()
            ->get()
            ->groupBy('project_id');

        $assignedMembers = DB::table('project_team_assignments')
            ->join('team_members', 'team_members.team_id', '=', 'project_team_assignments.team_id')
            ->join('users', 'users.id', '=', 'team_members.user_id')
            ->whereIn('project_team_assignments.project_id', $ids)
            ->select('project_team_assignments.project_id', 'users.id as user_id', 'users.name')
            ->distinct()
            ->get()
            ->groupBy('project_id');

        $projects = Project::query()->whereIn('id', $ids)->get(['id', 'company_id']);
        $workingHoursByCompany = CompanySetting::query()
            ->whereIn('company_id', $projects->pluck('company_id')->unique())
            ->pluck('working_hours_per_day', 'company_id');

        $businessDays = $this->businessDaysBetween($dateFrom, $dateTo);
        $result = [];

        foreach ($ids as $projectId) {
            $counts = collect($taskCounts->get($projectId, collect()));
            $done = (int) $counts->where('status', 'done')->sum('count');
            $inProgress = (int) $counts->where('status', 'in_progress')->sum('count');
            $pending = (int) $counts->where('status', 'todo')->sum('count');
            $total = $done + $inProgress + $pending;
            $blocked = (int) ($blockedByProject[$projectId] ?? 0);
            $overdue = (int) ($overdueByProject[$projectId] ?? 0);
            $estimated = round((float) ($estimatedHours[$projectId] ?? 0), 2);
            $remaining = round((float) ($remainingHours[$projectId] ?? 0), 2);
            $logged = round(((float) ($loggedMinutes[$projectId] ?? 0)) / 60, 2);
            $variance = ProjectMetricsCalculator::varianceHours($logged, $estimated);
            $variancePct = ProjectMetricsCalculator::variancePercent($logged, $estimated);
            $openTasks = $total - $done;
            $assignedCount = ($assignedMembers->get($projectId) ?? collect())->count();
            $activeCount = (int) ($activeContributors[$projectId] ?? 0);
            $project = $projects->firstWhere('id', $projectId);
            $hoursPerDay = (float) ($workingHoursByCompany[$project?->company_id] ?? 8);
            $capacity = $assignedCount * $hoursPerDay * $businessDays;
            $lastActivityDate = filled($lastActivity[$projectId] ?? null)
                ? Carbon::parse($lastActivity[$projectId])
                : null;

            $result[$projectId] = [
                'total_tasks' => $total,
                'done_tasks' => $done,
                'in_progress_tasks' => $inProgress,
                'pending_tasks' => $pending,
                'blocked_tasks' => $blocked,
                'overdue_tasks' => $overdue,
                'estimated_hours' => $estimated,
                'remaining_hours' => $remaining,
                'logged_hours' => $logged,
                'variance_hours' => $variance,
                'variance_percent' => $variancePct,
                'assigned_members' => $assignedCount,
                'active_contributors' => $activeCount,
                'utilization_percent' => ProjectMetricsCalculator::utilizationPercent($logged, $capacity),
                'completion_percent' => ProjectMetricsCalculator::completionPercent($done, $total),
                'remaining_percent' => ProjectMetricsCalculator::remainingPercent(
                    ProjectMetricsCalculator::completionPercent($done, $total)
                ),
                'last_activity_date' => $lastActivityDate,
                'effort_consumption_percent' => ProjectMetricsCalculator::effortConsumptionPercent($logged, $estimated),
                'assigned_members_list' => ($assignedMembers->get($projectId) ?? collect())
                    ->map(fn ($row) => ['id' => $row->user_id, 'name' => $row->name])
                    ->unique('id')
                    ->values()
                    ->all(),
                'active_contributors_list' => ($activeContributorUsers->get($projectId) ?? collect())
                    ->map(fn ($row) => ['id' => $row->user_id, 'name' => $row->name])
                    ->unique('id')
                    ->values()
                    ->all(),
                'open_tasks' => $openTasks,
                'blocked_percent' => ProjectMetricsCalculator::blockedPercent($blocked, max(1, $openTasks)),
            ];
        }

        return $result;
    }

    private function formatProjectRow(Project $project, array $metrics): array
    {
        $displayStatus = ProjectMetricsCalculator::displayStatus(
            $project->status,
            $metrics['total_tasks'],
            $metrics['done_tasks'],
            $metrics['in_progress_tasks'],
            $metrics['overdue_tasks'],
            $metrics['last_activity_date'],
            $metrics['variance_percent']
        );

        $scheduleHealth = ProjectMetricsCalculator::scheduleHealth(
            $metrics['variance_percent'],
            $metrics['blocked_percent']
        );

        $riskLevel = ProjectMetricsCalculator::riskLevel(
            $metrics['overdue_tasks'],
            $metrics['blocked_tasks'],
            $metrics['variance_percent']
        );

        return [
            'id' => $project->id,
            'name' => $project->name,
            'code' => $project->code,
            'status' => $project->status,
            'display_status' => $displayStatus,
            'display_status_label' => ProjectMetricsCalculator::displayStatusLabel($displayStatus),
            'display_status_class' => ProjectMetricsCalculator::statusBadgeClass($displayStatus),
            'schedule_health' => $scheduleHealth,
            'schedule_health_label' => ProjectMetricsCalculator::scheduleHealthLabel($scheduleHealth),
            'schedule_health_class' => ProjectMetricsCalculator::healthBadgeClass($scheduleHealth),
            'risk_level' => $riskLevel,
            'risk_label' => ProjectMetricsCalculator::riskLabel($riskLevel),
            'risk_class' => ProjectMetricsCalculator::riskBadgeClass($riskLevel),
            'company_name' => $project->company?->name,
            ...$metrics,
        ];
    }

    private function blockedTaskQuery(Collection $projectIds, array $filters): Builder
    {
        return Task::query()
            ->whereIn('tasks.project_id', $projectIds)
            ->where('tasks.status', '!=', 'done')
            ->whereHas('dailyReports', function (Builder $q) use ($filters) {
                $q->whereNotNull('blocker')
                    ->where('blocker', '!=', '');

                if (filled($filters['date_from'] ?? null)) {
                    $q->whereDate('report_date', '>=', $filters['date_from']);
                }

                if (filled($filters['date_to'] ?? null)) {
                    $q->whereDate('report_date', '<=', $filters['date_to']);
                }
            });
    }

    private function overdueTaskQuery(Collection $projectIds): Builder
    {
        return Task::query()
            ->whereIn('tasks.project_id', $projectIds)
            ->where('tasks.status', '!=', 'done')
            ->whereHas('assignments', function (Builder $q) {
                $q->whereNotNull('assigned_at')
                    ->whereRaw(
                        'task_assignments.assigned_at < DATE_SUB(NOW(), INTERVAL GREATEST(1, CEIL(COALESCE(tasks.estimate_hours, 8) / 8)) DAY)'
                    );
            });
    }

    private function applyTimeLogDateFilters(Builder $query, array $filters): void
    {
        if (filled($filters['date_from'] ?? null)) {
            $query->whereDate('start_time', '>=', $filters['date_from']);
        }

        if (filled($filters['date_to'] ?? null)) {
            $query->whereDate('start_time', '<=', $filters['date_to']);
        }
    }

    private function businessDaysBetween(string $from, string $to): int
    {
        $period = CarbonPeriod::create(Carbon::parse($from), Carbon::parse($to));

        return collect($period)->filter(fn (Carbon $date) => ! $date->isWeekend())->count() ?: 1;
    }

    private function emptyMetrics(): array
    {
        return [
            'total_tasks' => 0,
            'done_tasks' => 0,
            'in_progress_tasks' => 0,
            'pending_tasks' => 0,
            'blocked_tasks' => 0,
            'overdue_tasks' => 0,
            'estimated_hours' => 0.0,
            'remaining_hours' => 0.0,
            'logged_hours' => 0.0,
            'variance_hours' => 0.0,
            'variance_percent' => 0.0,
            'assigned_members' => 0,
            'active_contributors' => 0,
            'utilization_percent' => 0.0,
            'completion_percent' => 0.0,
            'remaining_percent' => 100.0,
            'last_activity_date' => null,
            'effort_consumption_percent' => 0.0,
            'assigned_members_list' => [],
            'active_contributors_list' => [],
            'open_tasks' => 0,
            'blocked_percent' => 0.0,
        ];
    }
}
