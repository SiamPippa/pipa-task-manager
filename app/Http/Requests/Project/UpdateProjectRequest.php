<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesProjectNameUniqueness;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    use EnforcesUserCompany, ValidatesProjectNameUniqueness;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'department_id' => [
                'required',
                'integer',
                Rule::exists('departments', 'id')->where(fn ($query) => $query->where('company_id', $this->integer('company_id'))),
            ],
            'name' => ['required', 'string', 'max:255', $this->uniqueProjectNameRule()],
            'code' => ['required', 'string', 'max:255', Rule::unique('projects', 'code')->ignore($this->route('project'))],
            'client_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:active,inactive,completed'],
        ];
    }
}
