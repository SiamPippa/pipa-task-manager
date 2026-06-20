<?php

namespace App\Repositories;

use App\Contracts\Repositories\DesignationRepositoryInterface;
use App\Models\Designation;

class DesignationRepository extends BaseRepository implements DesignationRepositoryInterface
{
    protected function model(): string
    {
        return Designation::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        $this->applyExactFilter($query, 'company_id', $filters['company_id'] ?? null);
        $this->applySearchFilter($query, $filters['search'] ?? null, ['title', 'code']);
        $this->applyBooleanFilter($query, 'status', $filters['status'] ?? null);
    }
}
