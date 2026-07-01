<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait ValidatesTeam
{
    protected function teamRules(?int $teamId = null): array
    {
        $companyId = $this->integer('company_id');

        $codeRule = Rule::unique('teams', 'code')
            ->where(fn ($query) => $query->where('company_id', $companyId));

        if ($teamId) {
            $codeRule->ignore($teamId);
        }

        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', $codeRule],
            'status' => ['sometimes', 'boolean'],
            'members' => ['required', 'array', 'min:1'],
            'members.*.user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)->where('status', true);
                }),
            ],
            'members.*.is_team_lead' => ['sometimes', 'boolean'],
            'members.*.status' => ['sometimes', 'boolean'],
        ];
    }

    protected function validateTeamMembers(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $members = collect($this->input('members', []));
            $userIds = $members->pluck('user_id')->filter();

            if ($userIds->count() !== $userIds->unique()->count()) {
                $validator->errors()->add('members', 'Each team member can only be added once.');
            }

            $leadCount = $members
                ->filter(fn (array $member) => $this->toBoolean($member['is_team_lead'] ?? false))
                ->count();

            if ($leadCount !== 1) {
                $validator->errors()->add('members', 'Exactly one team member must be marked as team lead.');
            }
        });
    }

    protected function prepareTeamMembersForValidation(): void
    {
        $members = collect($this->input('members', []))
            ->map(function (array $member) {
                return [
                    'user_id' => $member['user_id'] ?? null,
                    'is_team_lead' => $this->toBoolean($member['is_team_lead'] ?? false),
                    'status' => $this->toBoolean($member['status'] ?? true),
                ];
            })
            ->values()
            ->all();

        $this->merge(['members' => $members]);
    }

    private function toBoolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
