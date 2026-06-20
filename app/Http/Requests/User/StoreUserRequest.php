<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'designation_id' => ['nullable', 'integer', 'exists:designations,id'],
            'reporting_manager_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'integer', Rule::in(UserRole::values())],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
