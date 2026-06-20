<?php

namespace App\Http\Controllers\Concerns;

use App\Support\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait ScopesToUserCompany
{
    protected function scopedFilters(Request $request, array $keys): array
    {
        return CompanyContext::applyFilters($request->only($keys));
    }

    protected function scopedFilterFields(array $fields): array
    {
        return CompanyContext::withoutCompanyFilter($fields);
    }

    protected function scopedForCompany(Collection $items): Collection
    {
        return CompanyContext::filterByCompany($items);
    }
}
