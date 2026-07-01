<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Concerns\EnforcesUserCompany;
use App\Http\Requests\Concerns\ValidatesTeam;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    use EnforcesUserCompany;
    use ValidatesTeam {
        EnforcesUserCompany::prepareForValidation as enforceUserCompanyScope;
    }

    public function authorize(): bool
    {
        $team = Team::query()->find($this->route('team'));

        return $team !== null && $this->user()->can('update', $team);
    }

    protected function prepareForValidation(): void
    {
        $this->enforceUserCompanyScope();
        $this->prepareTeamMembersForValidation();
    }

    public function rules(): array
    {
        return $this->teamRules((int) $this->route('team'));
    }

    public function withValidator($validator): void
    {
        $this->validateTeamMembers($validator);
    }
}
