<?php

namespace App\Http\Requests\CompanySetting;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesCompanySetting;
use App\Models\CompanySetting;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanySettingRequest extends FormRequest
{
    use EnforcesUserCompany, ValidatesCompanySetting;

    public function authorize(): bool
    {
        return $this->user()->can('create', CompanySetting::class);
    }

    public function rules(): array
    {
        return $this->companySettingRules();
    }

    public function messages(): array
    {
        return $this->companySettingMessages();
    }
}
