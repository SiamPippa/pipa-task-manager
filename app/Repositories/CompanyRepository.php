<?php

namespace App\Repositories;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

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

    public function allActive(array $with = []): Collection
    {
        $query = $this->query()->active()->orderBy('name');

        if ($with) {
            $query->with($with);
        }

        return $query->get();
    }
}
