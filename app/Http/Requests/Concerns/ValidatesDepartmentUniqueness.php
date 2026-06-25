<?php

namespace App\Http\Requests\Concerns;

use App\Models\Department;

trait ValidatesDepartmentUniqueness
{
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->input('name'))]);
        }

        if ($this->has('code')) {
            $this->merge(['code' => mb_strtoupper(trim($this->input('code')))]);
        }
    }

    protected function uniqueDepartmentNameRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $name = trim((string) $value);

            if ($name === '') {
                return;
            }

            $query = Department::query()
                ->where('company_id', $this->integer('company_id'))
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

            if ($departmentId = $this->route('department')) {
                $query->where('id', '!=', $departmentId);
            }

            if ($query->exists()) {
                $fail('Department name already exists for the selected company.');
            }
        };
    }

    protected function uniqueDepartmentCodeRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $code = mb_strtoupper(trim((string) $value));

            if ($code === '') {
                return;
            }

            $query = Department::query()
                ->where('company_id', $this->integer('company_id'))
                ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)]);

            if ($departmentId = $this->route('department')) {
                $query->where('id', '!=', $departmentId);
            }

            if ($query->exists()) {
                $fail('Department code already exists for the selected company.');
            }
        };
    }
}
