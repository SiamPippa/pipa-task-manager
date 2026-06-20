<?php

namespace App\Http\Requests\DailyReport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
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
