<?php

namespace App\Http\Requests\Department;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesActiveCompanyForCreate;
use App\Http\Requests\Concerns\ValidatesDepartmentUniqueness;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateDepartmentRequest extends FormRequest
{
    use EnforcesUserCompany,
        ValidatesActiveCompanyForCreate,
        ValidatesDepartmentUniqueness {
            ValidatesDepartmentUniqueness::prepareForValidation as normalizeDepartmentInput;
            EnforcesUserCompany::prepareForValidation as enforceUserCompany;
        }

    protected function prepareForValidation(): void
    {
        $this->normalizeDepartmentInput();
        $this->enforceUserCompany();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateActiveCompanyForCreate(
                $validator,
                'Department cannot be assigned to an inactive company.',
            );
        });
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255', $this->uniqueDepartmentNameRule()],
            'code' => ['required', 'string', 'max:255', $this->uniqueDepartmentCodeRule()],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
