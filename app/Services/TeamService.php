<?php

namespace App\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class TeamService extends BaseService implements TeamServiceInterface
{
    public function __construct(
        TeamRepositoryInterface $repository,
        private readonly UserRepositoryInterface $userRepository
    ) {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $members = $this->extractMembers($data);
        $this->applyTeamLeadId($data, $members);

        $team = parent::create($data);
        $this->syncMembers($team, $members);
        $this->assignTeamLeadRole($team->team_lead_id);

        return $team->load(['members', 'teamLead']);
    }

    public function update(int $id, array $data): Model
    {
        $team = $this->findOrFail($id);
        $previousLeadId = $team->team_lead_id;
        $members = $this->extractMembers($data);
        $this->applyTeamLeadId($data, $members);

        $team = parent::update($id, $data);
        $this->syncMembers($team, $members);

        if ($previousLeadId !== $team->team_lead_id) {
            $this->revertTeamLeadRoleIfNeeded($previousLeadId);
        }

        $this->assignTeamLeadRole($team->team_lead_id);

        return $team->load(['members', 'teamLead']);
    }

    public function delete(int $id): bool
    {
        $team = $this->findOrFail($id);
        $leadId = $team->team_lead_id;

        if (! parent::delete($id)) {
            return false;
        }

        $this->revertTeamLeadRoleIfNeeded($leadId);

        return true;
    }

    private function extractMembers(array &$data): array
    {
        $members = $data['members'] ?? [];
        unset($data['members'], $data['member_ids'], $data['team_lead_id']);

        return collect($members)
            ->map(fn (array $member) => [
                'user_id' => (int) $member['user_id'],
                'is_team_lead' => (bool) ($member['is_team_lead'] ?? false),
                'status' => (bool) ($member['status'] ?? true),
            ])
            ->values()
            ->all();
    }

    private function applyTeamLeadId(array &$data, array $members): void
    {
        $lead = collect($members)->firstWhere('is_team_lead', true);
        $data['team_lead_id'] = $lead['user_id'] ?? null;
    }

    private function syncMembers(Team $team, array $members): void
    {
        $userIds = Arr::pluck($members, 'user_id');

        $users = $this->userRepository->filterQuery()
            ->whereIn('id', $userIds)
            ->get(['id', 'company_id']);

        $usersById = $users->keyBy('id');

        $syncData = collect($members)
            ->filter(fn (array $member) => $usersById->has($member['user_id']))
            ->mapWithKeys(function (array $member) use ($usersById) {
                $user = $usersById->get($member['user_id']);

                return [
                    $user->id => [
                        'company_id' => $user->company_id,
                        'is_team_lead' => $member['is_team_lead'],
                        'status' => $member['status'],
                    ],
                ];
            })
            ->all();

        $team->members()->sync($syncData);
    }

    private function assignTeamLeadRole(int $userId): void
    {
        $user = $this->userRepository->find($userId);

        if (! $user || $user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::COMPANY_ADMIN)) {
            return;
        }

        if (! $user->hasRole(UserRole::TEAM_LEAD)) {
            $user->assignRole(UserRole::TEAM_LEAD);
        }
    }

    private function revertTeamLeadRoleIfNeeded(int $userId): void
    {
        if (Team::query()->where('team_lead_id', $userId)->exists()) {
            return;
        }

        $isLeadOnPivot = Team::query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('users.id', $userId)
                    ->where('team_members.is_team_lead', true);
            })
            ->exists();

        if ($isLeadOnPivot) {
            return;
        }

        $user = $this->userRepository->find($userId);

        if (! $user || ! $user->hasRole(UserRole::TEAM_LEAD)) {
            return;
        }

        $user->removeRole(UserRole::TEAM_LEAD);

        if ($user->roleIds() === []) {
            $user->assignRole(UserRole::DEVELOPER);
        }
    }
}
