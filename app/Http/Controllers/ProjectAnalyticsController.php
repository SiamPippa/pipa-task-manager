<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\ProjectAnalyticsServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Models\Project;
use App\Support\FilterOptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectAnalyticsController extends Controller
{
    public function __construct(
        private readonly ProjectAnalyticsServiceInterface $analyticsService,
        private readonly CompanyServiceInterface $companyService,
        private readonly ProjectServiceInterface $projectService,
        private readonly TeamServiceInterface $teamService,
        private readonly UserServiceInterface $userService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $filters = $this->scopedFilters($request, [
            'company_id',
            'project_id',
            'team_id',
            'team_lead_id',
            'user_id',
            'status',
            'display_status',
            'date_from',
            'date_to',
        ]);
        $filters['viewer_id'] = auth()->id();

        $data = $this->analyticsService->dashboard($filters);

        return view('analytics.index', [
            'kpis' => $data['kpis'],
            'projects' => $data['projects'],
            'chartData' => [
                'aggregateTaskStatus' => $data['aggregateTaskStatus'],
            ],
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields($this->filterFields()),
        ]);
    }

    public function show(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        $filters = $this->scopedFilters($request, [
            'company_id',
            'team_id',
            'team_lead_id',
            'user_id',
            'date_from',
            'date_to',
        ]);
        $filters['viewer_id'] = auth()->id();
        $filters['project_id'] = $project->id;

        $detail = $this->analyticsService->projectDetail($project->id, $filters);

        abort_if(! $detail, 404);

        return view('analytics.show', [
            'project' => $detail['project'],
            'developers' => $detail['developers'],
            'teamAvgEfficiency' => $detail['team_avg_efficiency'],
            'chartData' => $detail['charts'],
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields($this->detailFilterFields()),
        ]);
    }

    private function filterFields(): array
    {
        return [
            ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
            ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 2, 'options' => $this->projectService->all(), 'dependsOn' => 'company_id', 'lookup' => 'projects'],
            ['type' => 'select', 'name' => 'team_id', 'label' => 'Team', 'placeholder' => 'All teams', 'col' => 2, 'options' => $this->teamService->all(), 'dependsOn' => 'company_id', 'lookup' => 'teams'],
            ['type' => 'select', 'name' => 'team_lead_id', 'label' => 'Team Lead', 'placeholder' => 'All team leads', 'col' => 2, 'options' => [], 'dependsOn' => 'company_id', 'lookup' => 'team-leads'],
            ['type' => 'select', 'name' => 'user_id', 'label' => 'Developer', 'placeholder' => 'All developers', 'col' => 2, 'options' => $this->userService->all(), 'dependsOn' => 'company_id', 'lookup' => 'users'],
            ['type' => 'select', 'name' => 'status', 'label' => 'Project Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::projectStatus()],
            ['type' => 'select', 'name' => 'display_status', 'label' => 'Health Status', 'placeholder' => 'All health statuses', 'col' => 2, 'options' => FilterOptions::projectDisplayStatus()],
            ['type' => 'date', 'name' => 'date_from', 'label' => 'From', 'col' => 2],
            ['type' => 'date', 'name' => 'date_to', 'label' => 'To', 'col' => 2],
        ];
    }

    private function detailFilterFields(): array
    {
        return [
            ['type' => 'select', 'name' => 'team_id', 'label' => 'Team', 'placeholder' => 'All teams', 'col' => 2, 'options' => $this->teamService->all(), 'dependsOn' => 'company_id', 'lookup' => 'teams'],
            ['type' => 'select', 'name' => 'user_id', 'label' => 'Developer', 'placeholder' => 'All developers', 'col' => 2, 'options' => $this->userService->all(), 'dependsOn' => 'company_id', 'lookup' => 'users'],
            ['type' => 'date', 'name' => 'date_from', 'label' => 'From', 'col' => 2],
            ['type' => 'date', 'name' => 'date_to', 'label' => 'To', 'col' => 2],
        ];
    }
}
