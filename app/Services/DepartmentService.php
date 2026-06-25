<?php

namespace App\Services;

use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class DepartmentService extends BaseService implements DepartmentServiceInterface
{
    public function __construct(DepartmentRepositoryInterface $repository)
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
                'company_id' => 'Department cannot be created under an inactive company.',
            ]);
        }
    }
}
