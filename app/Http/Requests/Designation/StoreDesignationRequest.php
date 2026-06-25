<?php

namespace App\Http\Requests\Designation;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesActiveCompanyForCreate;
use App\Http\Requests\Concerns\ValidatesDesignationUniqueness;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDesignationRequest extends FormRequest
{
    use EnforcesUserCompany,
        ValidatesActiveCompanyForCreate,
        ValidatesDesignationUniqueness {
            ValidatesDesignationUniqueness::prepareForValidation as normalizeDesignationInput;
            EnforcesUserCompany::prepareForValidation as enforceUserCompany;
        }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeDesignationInput();
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
                'Designation cannot be created under an inactive company.',
            );
        });
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'title' => ['required', 'string', 'max:255', $this->uniqueDesignationTitleRule()],
            'code' => ['required', 'string', 'max:255', $this->uniqueDesignationCodeRule()],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
