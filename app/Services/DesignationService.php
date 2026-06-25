<?php

namespace App\Services;

use App\Contracts\Repositories\DesignationRepositoryInterface;
use App\Contracts\Services\DesignationServiceInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class DesignationService extends BaseService implements DesignationServiceInterface
{
    public function __construct(DesignationRepositoryInterface $repository)
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
                'company_id' => 'Designation cannot be created under an inactive company.',
            ]);
        }
    }
}
