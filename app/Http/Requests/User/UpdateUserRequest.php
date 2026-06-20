<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'reporting_manager_id' => ['nullable', 'integer', 'exists:users,id', Rule::notIn([(int) $this->route('user')])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'integer', Rule::in(UserRole::values())],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
