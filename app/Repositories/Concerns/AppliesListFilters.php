<?php

namespace App\Repositories\Concerns;

trait AppliesListFilters
{
    protected function applySearchFilter($query, ?string $term, array $columns): void
    {
        if (blank($term)) {
            return;
        }

        $query->where(function ($q) use ($term, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', '%'.$term.'%');
            }
        });
    }

    protected function applyExactFilter($query, string $column, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query->where($column, $value);
    }

    protected function applyBooleanFilter($query, string $column, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query->where($column, filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    protected function applyDateFromFilter($query, string $column, ?string $from): void
    {
        if (blank($from)) {
            return;
        }

        $query->whereDate($column, '>=', $from);
    }

    protected function applyDateToFilter($query, string $column, ?string $to): void
    {
        if (blank($to)) {
            return;
        }

        $query->whereDate($column, '<=', $to);
    }
}
