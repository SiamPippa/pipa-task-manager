<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyServiceInterface $companyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Company::class);

        $filters = $request->only(['search', 'status']);

        return view('companies.index', [
            'companies' => $this->companyService->paginate($filters),
            'filters' => $filters,
            'filterFields' => [
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name, code, email, phone', 'col' => 4],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 3, 'options' => FilterOptions::booleanStatus()],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Company::class);

        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->companyService->create($data);

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(int $company): View
    {
        $company = $this->companyService->findOrFail($company);
        $this->authorize('view', $company);

        return view('companies.show', [
            'company' => $company,
        ]);
    }

    public function edit(int $company): View
    {
        $company = $this->companyService->findOrFail($company);
        $this->authorize('update', $company);

        return view('companies.edit', [
            'company' => $company,
        ]);
    }

    public function update(UpdateCompanyRequest $request, int $company): RedirectResponse
    {
        $companyModel = $this->companyService->findOrFail($company);
        $this->authorize('update', $companyModel);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->companyService->update($company, $data);

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(int $company): RedirectResponse
    {
        $companyModel = $this->companyService->findOrFail($company);
        $this->authorize('delete', $companyModel);

        $this->companyService->delete($company);

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
