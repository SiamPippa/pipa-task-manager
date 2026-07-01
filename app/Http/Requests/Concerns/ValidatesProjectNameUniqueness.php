<?php

namespace App\Http\Requests\Concerns;

use App\Models\Project;

trait ValidatesProjectNameUniqueness
{
    protected function uniqueProjectNameRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $query = Project::query()
                ->where('company_id', $this->integer('company_id'))
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $value)]);

            if ($projectId = $this->route('project')) {
                $query->where('id', '!=', $projectId);
            }

            if ($query->exists()) {
                $fail('A project with this name already exists in the selected company.');
            }
        };
    }
}
