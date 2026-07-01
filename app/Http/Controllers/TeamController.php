<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Models\Team;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct(
        private readonly TeamServiceInterface $teamService,
        private readonly CompanyServiceInterface $companyService,
        private readonly UserServiceInterface $userService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Team::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'project_id', 'status']);
        $filters['viewer_id'] = auth()->id();

        return view('teams.index', [
            'teams' => $this->teamService->paginate($filters, 15, ['company', 'teamLead', 'members']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or code', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 3, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 3, 'options' => [], 'dependsOn' => 'company_id', 'lookup' => 'projects'],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::booleanStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Team::class);

        return view('teams.create', [
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'users' => $this->scopedForCompany($this->userService->all()),
        ]);
    }

    public function store(StoreTeamRequest $request): RedirectResponse
    {
        $this->authorize('create', Team::class);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->teamService->create($data);

        return redirect()->route('teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function show(int $team): View
    {
        $team = $this->teamService->findOrFail($team, ['company', 'teamLead', 'members']);
        $this->authorize('view', $team);

        return view('teams.show', [
            'team' => $team,
        ]);
    }

    public function edit(int $team): View
    {
        $teamModel = $this->teamService->findOrFail($team, ['members']);
        $this->authorize('update', $teamModel);

        return view('teams.edit', [
            'team' => $teamModel,
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'users' => $this->scopedForCompany($this->userService->all()),
        ]);
    }

    public function update(UpdateTeamRequest $request, int $team): RedirectResponse
    {
        $teamModel = $this->teamService->findOrFail($team);
        $this->authorize('update', $teamModel);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->teamService->update($team, $data);

        return redirect()->route('teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(int $team): RedirectResponse
    {
        $teamModel = $this->teamService->findOrFail($team);
        $this->authorize('delete', $teamModel);

        $this->teamService->delete($team);

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully.');
    }
}
