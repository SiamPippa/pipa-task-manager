<?php

namespace App\Http\Requests\Concerns;

use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait ValidatesCompanySetting
{
    protected function companySettingRules(?int $settingId = null): array
    {
        $companyIdRules = [
            'required',
            'integer',
            Rule::unique('company_settings', 'company_id')->ignore($settingId),
            'exists:companies,id',
        ];

        return [
            'company_id' => $companyIdRules,
            'office_start_time' => ['required', 'date_format:H:i'],
            'office_end_time' => ['required', 'date_format:H:i'],
            'working_hours_per_day' => ['required', 'integer', 'min:1', 'max:24'],
            'allow_manual_time_log' => ['sometimes', 'boolean'],
            'require_daily_report' => ['sometimes', 'boolean'],
        ];
    }

    protected function companySettingMessages(): array
    {
        return [
            'company_id.unique' => 'This company already has office hours configured.',
            'working_hours_per_day.required' => 'The working hours per day field is required.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateActiveCompanyForCreate($validator);
            $this->validateOfficeHoursRange($validator);
        });
    }

    protected function validateActiveCompanyForCreate(Validator $validator): void
    {
        if ($this->route('company_setting')) {
            return;
        }

        $company = Company::query()->find($this->integer('company_id'));

        if ($company && ! $company->status) {
            $validator->errors()->add(
                'company_id',
                'Company Settings cannot be created for an inactive company.',
            );
        }
    }

    protected function validateOfficeHoursRange(Validator $validator): void
    {
        $start = $this->input('office_start_time');
        $end = $this->input('office_end_time');

        if (! filled($start) || ! filled($end)) {
            return;
        }

        if (strtotime($end) <= strtotime($start)) {
            $validator->errors()->add(
                'office_end_time',
                'Office end time must be greater than office start time.',
            );
        }
    }
}
