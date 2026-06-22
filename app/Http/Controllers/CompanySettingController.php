<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\CompanySettingServiceInterface;
use App\Http\Requests\CompanySetting\StoreCompanySettingRequest;
use App\Http\Requests\CompanySetting\UpdateCompanySettingRequest;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function __construct(
        private readonly CompanySettingServiceInterface $companySettingService,
        private readonly CompanyServiceInterface $companyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CompanySetting::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id']);

        return view('company-settings.index', [
            'companySettings' => $this->companySettingService->paginate($filters, 15, ['company']),
            'filters' => $filters,
            'canCreateSetting' => $this->companiesWithoutSettings()->isNotEmpty(),
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Company name or code', 'col' => 4],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 4, 'options' => $this->companyService->all()],
            ]),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $this->authorize('create', CompanySetting::class);

        $companies = $this->companiesWithoutSettings();

        if ($companies->isEmpty()) {
            return redirect()->route('company-settings.index')
                ->with('error', 'All available companies already have office hours configured.');
        }

        return view('company-settings.create', [
            'companies' => $companies,
        ]);
    }

    public function store(StoreCompanySettingRequest $request): RedirectResponse
    {
        $this->authorize('create', CompanySetting::class);

        $data = $request->validated();
        $data['allow_manual_time_log'] = $request->boolean('allow_manual_time_log');
        $data['require_daily_report'] = $request->boolean('require_daily_report');

        $this->companySettingService->create($data);

        return redirect()->route('company-settings.index')
            ->with('success', 'Company setting created successfully.');
    }

    public function show(int $company_setting): View
    {
        $companySetting = $this->companySettingService->findOrFail($company_setting, ['company']);
        $this->authorize('view', $companySetting);

        return view('company-settings.show', [
            'companySetting' => $companySetting,
        ]);
    }

    public function edit(int $company_setting): View
    {
        $companySetting = $this->companySettingService->findOrFail($company_setting);
        $this->authorize('update', $companySetting);

        return view('company-settings.edit', [
            'companySetting' => $companySetting,
            'companies' => $this->scopedForCompany($this->companyService->all()),
        ]);
    }

    private function companiesWithoutSettings(?int $exceptCompanyId = null): Collection
    {
        $existingCompanyIds = CompanySetting::query()
            ->when($exceptCompanyId, fn ($query) => $query->where('company_id', '!=', $exceptCompanyId))
            ->pluck('company_id');

        return $this->scopedForCompany($this->companyService->all())
            ->reject(fn ($company) => $existingCompanyIds->contains($company->id))
            ->values();
    }

    public function update(UpdateCompanySettingRequest $request, int $company_setting): RedirectResponse
    {
        $companySetting = $this->companySettingService->findOrFail($company_setting);
        $this->authorize('update', $companySetting);

        $data = $request->validated();
        $data['allow_manual_time_log'] = $request->boolean('allow_manual_time_log');
        $data['require_daily_report'] = $request->boolean('require_daily_report');

        $this->companySettingService->update($company_setting, $data);

        return redirect()->route('company-settings.index')
            ->with('success', 'Company setting updated successfully.');
    }

    public function destroy(int $company_setting): RedirectResponse
    {
        $companySetting = $this->companySettingService->findOrFail($company_setting);
        $this->authorize('delete', $companySetting);

        $this->companySettingService->delete($company_setting);

        return redirect()->route('company-settings.index')
            ->with('success', 'Company setting deleted successfully.');
    }
}
