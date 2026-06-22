<?php

namespace App\Http\Requests\CompanySetting;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanySettingRequest extends FormRequest
{
    use EnforcesUserCompany;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id', Rule::unique('company_settings', 'company_id')],
            'office_start_time' => ['nullable', 'date_format:H:i'],
            'office_end_time' => ['nullable', 'date_format:H:i', 'after:office_start_time'],
            'working_hours_per_day' => ['nullable', 'integer', 'min:1', 'max:24'],
            'allow_manual_time_log' => ['sometimes', 'boolean'],
            'require_daily_report' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.unique' => 'This company already has office hours configured.',
            'office_end_time.after' => 'Office end time must be after office start time.',
        ];
    }
}
