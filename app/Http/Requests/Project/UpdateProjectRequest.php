<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('projects', 'code')->ignore($this->route('project'))],
            'status' => ['required', 'string', 'in:active,inactive,completed'],
        ];
    }
}
