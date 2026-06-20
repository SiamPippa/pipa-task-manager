<?php

namespace App\Http\Requests\CompanySetting;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanySettingRequest extends FormRequest
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
            'office_start_time' => ['nullable', 'date_format:H:i'],
            'office_end_time' => ['nullable', 'date_format:H:i'],
            'working_hours_per_day' => ['nullable', 'integer', 'min:1', 'max:24'],
            'allow_manual_time_log' => ['sometimes', 'boolean'],
            'require_daily_report' => ['sometimes', 'boolean'],
        ];
    }
}
