<?php

namespace App\Repositories;

use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Models\Department;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    protected function model(): string
    {
        return Department::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        $this->applyExactFilter($query, 'company_id', $filters['company_id'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null, ['name', 'code']);
        $this->applyBooleanFilter($query, 'status', $filters['status'] ?? null);
    }
}
