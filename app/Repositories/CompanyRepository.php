<?php

namespace App\Repositories;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Models\Company;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    protected function model(): string
    {
        return Company::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        $this->applySearchFilter($query, $filters['search'] ?? null, ['name', 'code', 'email', 'phone']);
        $this->applyBooleanFilter($query, 'status', $filters['status'] ?? null);
    }
}
