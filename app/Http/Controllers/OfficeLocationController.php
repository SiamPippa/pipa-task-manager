<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\OfficeLocation;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class OfficeLocationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', OfficeLocation::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'status']);

        $query = OfficeLocation::query()->with('company')->orderBy('name');

        if (filled($filters['company_id'] ?? null)) {
            $query->where('company_id', $filters['company_id']);
        }

        if (filled($filters['search'] ?? null)) {
            $query->where(function ($subQuery) use ($filters) {
                $subQuery
                    ->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('code', 'like', '%'.$filters['search'].'%');
            });
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== null && $filters['status'] !== '') {
            $query->where('status', (bool) $filters['status']);
        }

        return view('office-locations.index', [
            'officeLocations' => $query->paginate(15)->withQueryString(),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or code', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 3, 'options' => Company::query()->active()->orderBy('name')->get()],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::booleanStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', OfficeLocation::class);

        return view('office-locations.create', [
            'officeLocation' => new OfficeLocation,
            'companies' => $this->scopedForCompany(Company::query()->active()->orderBy('name')->get()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', OfficeLocation::class);

        OfficeLocation::query()->create($this->validated($request));

        return redirect()->route('office-locations.index')->with('success', 'Office location created successfully.');
    }

    public function show(OfficeLocation $officeLocation): View
    {
        $this->authorize('view', $officeLocation);

        return view('office-locations.show', compact('officeLocation'));
    }

    public function edit(OfficeLocation $officeLocation): View
    {
        $this->authorize('update', $officeLocation);

        return view('office-locations.edit', [
            'officeLocation' => $officeLocation,
            'companies' => $this->scopedForCompany(Company::query()->active()->orderBy('name')->get()),
        ]);
    }

    public function update(Request $request, OfficeLocation $officeLocation): RedirectResponse
    {
        $this->authorize('update', $officeLocation);

        $officeLocation->update($this->validated($request, $officeLocation));

        return redirect()->route('office-locations.index')->with('success', 'Office location updated successfully.');
    }

    public function destroy(OfficeLocation $officeLocation): RedirectResponse
    {
        $this->authorize('delete', $officeLocation);
        $officeLocation->delete();

        return redirect()->route('office-locations.index')->with('success', 'Office location deleted successfully.');
    }

    private function validated(Request $request, ?OfficeLocation $officeLocation = null): array
    {
        if (! \App\Support\CompanyContext::canSelectCompany($request->user())) {
            $request->merge(['company_id' => \App\Support\CompanyContext::companyId($request->user())]);
        }

        $companyId = $request->integer('company_id');

        return $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('office_locations', 'name')->where('company_id', $companyId)->ignore($officeLocation)],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('office_locations', 'code')->where('company_id', $companyId)->ignore($officeLocation)],
            'address' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ]) + ['status' => $request->boolean('status')];
    }
}
