<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'jira_task_no' => ['required', 'string', 'max:255', Rule::unique('tasks', 'jira_task_no')->ignore($this->route('task'))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'estimate_hours' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:todo,in_progress,done'],
        ];
    }
}
