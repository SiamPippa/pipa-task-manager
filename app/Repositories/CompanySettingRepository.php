<?php

namespace App\Repositories;

use App\Contracts\Repositories\CompanySettingRepositoryInterface;
use App\Models\CompanySetting;

class CompanySettingRepository extends BaseRepository implements CompanySettingRepositoryInterface
{
    protected function model(): string
    {
        return CompanySetting::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        $this->applyExactFilter($query, 'company_id', $filters['company_id'] ?? null);

        if (filled($filters['search'] ?? null)) {
            $term = $filters['search'];
            $query->whereHas('company', function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                    ->orWhere('code', 'like', '%'.$term.'%');
            });
        }
    }
}
