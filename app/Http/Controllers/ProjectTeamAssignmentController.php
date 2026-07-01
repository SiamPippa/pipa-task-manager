<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ProjectTeamAssignmentServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Http\Requests\ProjectTeamAssignment\StoreProjectTeamAssignmentRequest;
use App\Http\Requests\ProjectTeamAssignment\UpdateProjectTeamAssignmentRequest;
use App\Models\ProjectTeamAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectTeamAssignmentController extends Controller
{
    public function __construct(
        private readonly ProjectTeamAssignmentServiceInterface $projectTeamAssignmentService,
        private readonly CompanyServiceInterface $companyService,
        private readonly ProjectServiceInterface $projectService,
        private readonly TeamServiceInterface $teamService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProjectTeamAssignment::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'project_id', 'team_id']);
        $filters['viewer_id'] = auth()->id();

        return view('project-team-assignments.index', [
            'projectTeamAssignments' => $this->projectTeamAssignmentService->paginate($filters, 15, ['project', 'team', 'assignedBy']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Project or team', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 2, 'options' => $this->projectService->all(), 'dependsOn' => 'company_id', 'lookup' => 'projects'],
                ['type' => 'select', 'name' => 'team_id', 'label' => 'Team', 'placeholder' => 'All teams', 'col' => 2, 'options' => $this->teamService->all(), 'dependsOn' => 'company_id', 'lookup' => 'teams'],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ProjectTeamAssignment::class);

        return view('project-team-assignments.create', [
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'projects' => collect(),
            'teams' => collect(),
        ]);
    }

    public function store(StoreProjectTeamAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', ProjectTeamAssignment::class);

        $data = $request->validated();
        $data['assigned_by'] = auth()->id();
        $data['assigned_at'] = now();

        $this->projectTeamAssignmentService->create($data);

        return redirect()->route('project-team-assignments.index')
            ->with('success', 'Project team assignment created successfully.');
    }

    public function show(int $project_team_assignment): View
    {
        $assignment = $this->projectTeamAssignmentService->findOrFail($project_team_assignment, ['project', 'team', 'assignedBy']);
        $this->authorize('view', $assignment);

        return view('project-team-assignments.show', [
            'projectTeamAssignment' => $assignment,
        ]);
    }

    public function edit(int $project_team_assignment): View
    {
        $assignment = $this->projectTeamAssignmentService->findOrFail($project_team_assignment, ['project']);
        $this->authorize('update', $assignment);

        return view('project-team-assignments.edit', [
            'projectTeamAssignment' => $assignment,
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'projects' => $this->scopedForCompany($this->projectService->all()),
            'teams' => $this->scopedForCompany($this->teamService->all()),
        ]);
    }

    public function update(UpdateProjectTeamAssignmentRequest $request, int $project_team_assignment): RedirectResponse
    {
        $assignment = $this->projectTeamAssignmentService->findOrFail($project_team_assignment);
        $this->authorize('update', $assignment);

        $this->projectTeamAssignmentService->update($project_team_assignment, $request->validated());

        return redirect()->route('project-team-assignments.index')
            ->with('success', 'Project team assignment updated successfully.');
    }

    public function destroy(int $project_team_assignment): RedirectResponse
    {
        $assignment = $this->projectTeamAssignmentService->findOrFail($project_team_assignment);
        $this->authorize('delete', $assignment);

        $this->projectTeamAssignmentService->delete($project_team_assignment);

        return redirect()->route('project-team-assignments.index')
            ->with('success', 'Project team assignment deleted successfully.');
    }
}
