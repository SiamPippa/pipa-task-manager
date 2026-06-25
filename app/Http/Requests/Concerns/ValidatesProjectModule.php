<?php

namespace App\Http\Requests\Concerns;

use App\Models\Project;
use App\Support\ModuleProjectValidator;
use Illuminate\Validation\Rule;

trait ValidatesProjectModule
{
    protected function projectModuleRules(?int $moduleId = null): array
    {
        $projectId = $this->integer('project_id');

        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_modules', 'name')
                    ->where(fn ($query) => $query->where('project_id', $projectId))
                    ->ignore($moduleId),
            ],
            'details' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $project = Project::query()
                ->with('company.settings')
                ->find($this->integer('project_id'));

            if (! $project) {
                return;
            }

            ModuleProjectValidator::apply(
                $validator,
                $project,
                $this->input('start_date'),
                $this->input('end_date'),
            );
        });
    }
}
