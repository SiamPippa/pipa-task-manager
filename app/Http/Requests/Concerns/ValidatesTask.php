<?php

namespace App\Http\Requests\Concerns;

use App\Enums\TaskType;
use App\Models\ProjectModule;
use App\Support\BranchNameGenerator;
use Illuminate\Validation\Rule;

trait ValidatesTask
{
    protected function taskRules(?int $taskId = null): array
    {
        $projectId = $this->integer('project_id');

        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'project_module_id' => [
                'required',
                'integer',
                Rule::exists('project_modules', 'id')->where(fn ($query) => $query->where('project_id', $projectId)),
            ],
            'jira_task_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tasks', 'jira_task_no')->ignore($taskId),
            ],
            'title' => ['required', 'string', 'max:255'],
            'branch_name' => [
                'required',
                'string',
                'max:60',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tasks', 'branch_name')
                    ->where(fn ($query) => $query->where('project_id', $projectId))
                    ->ignore($taskId),
            ],
            'type' => ['required', 'string', Rule::in(TaskType::values())],
            'description' => ['nullable', 'string'],
            'estimate_hours' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:todo,in_progress,done'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('title') && ! $this->filled('branch_name')) {
            $this->merge([
                'branch_name' => BranchNameGenerator::fromTitle($this->input('title')),
            ]);
        }

        if ($this->filled('branch_name')) {
            $this->merge([
                'branch_name' => strtolower(trim($this->input('branch_name'))),
            ]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $module = ProjectModule::query()->find($this->integer('project_module_id'));

            if ($module && $module->project_id !== $this->integer('project_id')) {
                $validator->errors()->add('project_module_id', 'The selected module does not belong to this project.');
            }
        });
    }
}
