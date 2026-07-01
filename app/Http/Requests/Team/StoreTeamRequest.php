<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesTeam;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    use EnforcesUserCompany;
    use ValidatesTeam {
        EnforcesUserCompany::prepareForValidation as enforceUserCompanyScope;
    }

    public function authorize(): bool
    {
        return $this->user()->can('create', Team::class);
    }

    protected function prepareForValidation(): void
    {
        $this->enforceUserCompanyScope();
        $this->prepareTeamMembersForValidation();
    }

    public function rules(): array
    {
        return $this->teamRules();
    }

    public function withValidator($validator): void
    {
        $this->validateTeamMembers($validator);
    }
}
