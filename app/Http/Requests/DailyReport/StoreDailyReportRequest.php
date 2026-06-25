<?php

namespace App\Http\Requests\DailyReport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'project_module_id' => [
                'required',
                'integer',
                Rule::exists('project_modules', 'id')->where(
                    fn ($query) => $query->where('project_id', $this->integer('project_id'))
                ),
            ],
            'task_id' => [
                'required',
                'integer',
                Rule::exists('tasks', 'id')->where(
                    fn ($query) => $query
                        ->where('project_id', $this->integer('project_id'))
                        ->where('project_module_id', $this->integer('project_module_id'))
                ),
            ],
            'report_date' => ['required', 'date'],
            'summary' => ['nullable', 'string'],
            'blocker' => ['nullable', 'string'],
            'tomorrow_plan' => ['nullable', 'string'],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'total_minutes' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }
}
