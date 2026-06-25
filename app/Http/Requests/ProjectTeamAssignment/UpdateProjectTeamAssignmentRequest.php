<?php

namespace App\Http\Requests\ProjectTeamAssignment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectTeamAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'team_id' => [
                'required',
                'integer',
                'exists:teams,id',
                Rule::unique('project_team_assignments', 'team_id')
                    ->where(fn ($query) => $query->where('project_id', $this->integer('project_id')))
                    ->ignore($this->route('project_team_assignment')),
            ],
        ];
    }
}
