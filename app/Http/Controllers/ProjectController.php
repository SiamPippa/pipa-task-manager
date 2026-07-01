<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\User;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly CompanyServiceInterface $companyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'status']);
        $filters['viewer_id'] = auth()->id();

        return view('projects.index', [
            'projects' => $this->projectService->paginate($filters, 15, ['company', 'managers']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or code', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 3, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::projectStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        $companies = $this->scopedForCompany($this->companyService->all());

        return view('projects.create', [
            'project' => new Project,
            'companies' => $companies,
            'managers' => $this->managers(),
            'companyWorkingHours' => $this->companyWorkingHours($companies),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $this->projectService->create($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(int $project): View
    {
        $project = $this->projectService->findOrFail($project, ['company', 'managers']);
        $this->authorize('view', $project);

        return view('projects.show', [
            'project' => $project,
        ]);
    }

    public function edit(int $project): View
    {
        $projectModel = $this->projectService->findOrFail($project, ['managers']);
        $this->authorize('update', $projectModel);

        $companies = $this->scopedForCompany($this->companyService->all());

        return view('projects.edit', [
            'project' => $projectModel,
            'companies' => $companies,
            'managers' => $this->managers(),
            'companyWorkingHours' => $this->companyWorkingHours($companies),
        ]);
    }

    public function update(UpdateProjectRequest $request, int $project): RedirectResponse
    {
        $projectModel = $this->projectService->findOrFail($project);
        $this->authorize('update', $projectModel);

        $this->projectService->update($project, $request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(int $project): RedirectResponse
    {
        $projectModel = $this->projectService->findOrFail($project);
        $this->authorize('delete', $projectModel);

        $this->projectService->delete($project);

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function companyWorkingHours($companies): array
    {
        return CompanySetting::query()
            ->whereIn('company_id', $companies->pluck('id'))
            ->pluck('working_hours_per_day', 'company_id')
            ->all();
    }

    private function managers()
    {
        return $this->scopedForCompany(User::query()->where('status', true)->orderBy('name')->get());
    }
}
