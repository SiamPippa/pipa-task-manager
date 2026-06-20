<?php

namespace App\Http\Requests\Department;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    use EnforcesUserCompany;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
