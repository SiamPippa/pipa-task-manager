<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:companies,code'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
