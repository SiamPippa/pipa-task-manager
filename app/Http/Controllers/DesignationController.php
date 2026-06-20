<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\DesignationServiceInterface;
use App\Http\Requests\Designation\StoreDesignationRequest;
use App\Http\Requests\Designation\UpdateDesignationRequest;
use App\Models\Designation;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignationController extends Controller
{
    public function __construct(
        private readonly DesignationServiceInterface $designationService,
        private readonly CompanyServiceInterface $companyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Designation::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'status']);

        return view('designations.index', [
            'designations' => $this->designationService->paginate($filters, 15, ['company']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Title or code', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 3, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::booleanStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Designation::class);

        return view('designations.create', [
            'companies' => $this->scopedForCompany($this->companyService->all()),
        ]);
    }

    public function store(StoreDesignationRequest $request): RedirectResponse
    {
        $this->authorize('create', Designation::class);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->designationService->create($data);

        return redirect()->route('designations.index')
            ->with('success', 'Designation created successfully.');
    }

    public function show(int $designation): View
    {
        $designation = $this->designationService->findOrFail($designation, ['company']);
        $this->authorize('view', $designation);

        return view('designations.show', [
            'designation' => $designation,
        ]);
    }

    public function edit(int $designation): View
    {
        $designationModel = $this->designationService->findOrFail($designation);
        $this->authorize('update', $designationModel);

        return view('designations.edit', [
            'designation' => $designationModel,
            'companies' => $this->scopedForCompany($this->companyService->all()),
        ]);
    }

    public function update(UpdateDesignationRequest $request, int $designation): RedirectResponse
    {
        $designationModel = $this->designationService->findOrFail($designation);
        $this->authorize('update', $designationModel);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->designationService->update($designation, $data);

        return redirect()->route('designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(int $designation): RedirectResponse
    {
        $designationModel = $this->designationService->findOrFail($designation);
        $this->authorize('delete', $designationModel);

        $this->designationService->delete($designation);

        return redirect()->route('designations.index')
            ->with('success', 'Designation deleted successfully.');
    }
}
