<?php

namespace App\Http\Requests\Concerns;

use App\Models\Company;
use Illuminate\Validation\Validator;

trait ValidatesActiveCompanyForCreate
{
    protected function validateActiveCompanyForCreate(
        Validator $validator,
        string $message = 'Department cannot be created under an inactive company.',
    ): void {
        $company = Company::query()->find($this->integer('company_id'));

        if ($company && ! $company->status) {
            $validator->errors()->add('company_id', $message);
        }
    }
}
