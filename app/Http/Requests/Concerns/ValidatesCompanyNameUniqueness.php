<?php

namespace App\Http\Requests\Concerns;

use App\Models\Company;

/** @mixin \Illuminate\Foundation\Http\FormRequest */
trait ValidatesCompanyNameUniqueness
{
    protected function uniqueCompanyNameRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $query = Company::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $value)]);

            if ($companyId = $this->route('company')) {
                $query->where('id', '!=', $companyId);
            }

            if ($query->exists()) {
                $fail('A company with this name already exists.');
            }
        };
    }
}
