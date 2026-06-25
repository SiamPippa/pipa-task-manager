<?php

namespace App\Http\Requests\Concerns;

use App\Models\Designation;

trait ValidatesDesignationUniqueness
{
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => trim($this->input('title'))]);
        }

        if ($this->has('code')) {
            $this->merge(['code' => mb_strtoupper(trim($this->input('code')))]);
        }
    }

    protected function uniqueDesignationTitleRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $title = trim((string) $value);

            if ($title === '') {
                return;
            }

            $query = Designation::query()
                ->where('company_id', $this->integer('company_id'))
                ->whereRaw('LOWER(title) = ?', [mb_strtolower($title)]);

            if ($designationId = $this->route('designation')) {
                $query->where('id', '!=', $designationId);
            }

            if ($query->exists()) {
                $fail('Designation title already exists for the selected company.');
            }
        };
    }

    protected function uniqueDesignationCodeRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $code = mb_strtoupper(trim((string) $value));

            if ($code === '') {
                return;
            }

            $query = Designation::query()
                ->where('company_id', $this->integer('company_id'))
                ->whereRaw('LOWER(code) = ?', [mb_strtolower($code)]);

            if ($designationId = $this->route('designation')) {
                $query->where('id', '!=', $designationId);
            }

            if ($query->exists()) {
                $fail('Designation code already exists for the selected company.');
            }
        };
    }
}
