<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Concerns\ValidatesContactFields;
use App\Http\Requests\Concerns\ValidatesCompanyNameUniqueness;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    use ValidatesCompanyNameUniqueness, ValidatesContactFields;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', $this->uniqueCompanyNameRule()],
            'code' => ['required', 'string', 'max:255', 'unique:companies,code'],
            ...$this->contactFieldRules(),
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
