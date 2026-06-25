<?php

namespace App\Http\Requests\CompanySetting;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesCompanySetting;
use App\Models\CompanySetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanySettingRequest extends FormRequest
{
    use EnforcesUserCompany, ValidatesCompanySetting;

    public function authorize(): bool
    {
        $companySetting = CompanySetting::query()->find($this->route('company_setting'));

        return $companySetting && $this->user()->can('update', $companySetting);
    }

    public function rules(): array
    {
        return $this->companySettingRules($this->route('company_setting'));
    }

    public function messages(): array
    {
        return $this->companySettingMessages();
    }
}
