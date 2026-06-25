<?php

namespace App\Services;

use App\Contracts\Repositories\CompanySettingRepositoryInterface;
use App\Contracts\Services\CompanySettingServiceInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CompanySettingService extends BaseService implements CompanySettingServiceInterface
{
    public function __construct(CompanySettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $this->ensureCompanyIsActiveForCreate($data['company_id'] ?? null);

        return parent::create($data);
    }

    private function ensureCompanyIsActiveForCreate(?int $companyId): void
    {
        if (! $companyId) {
            return;
        }

        $company = Company::query()->find($companyId);

        if ($company && ! $company->status) {
            throw ValidationException::withMessages([
                'company_id' => 'Company Settings cannot be created for an inactive company.',
            ]);
        }
    }
}
