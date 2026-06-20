<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
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
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'team_lead_id' => ['required', 'integer', 'exists:users,id'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where('company_id', $this->integer('company_id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
