<?php

namespace App\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

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
        $memberIds = $this->extractMemberIds($data);
        $team = parent::create($data);
        $this->syncMembers($team, $memberIds);
        $this->assignTeamLeadRole($team->team_lead_id);

        return $team->load(['members', 'teamLead']);
    }

    public function update(int $id, array $data): Model
    {
        $team = $this->findOrFail($id);
        $previousLeadId = $team->team_lead_id;
        $memberIds = $this->extractMemberIds($data);

        $team = parent::update($id, $data);
        $this->syncMembers($team, $memberIds);

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

    private function extractMemberIds(array &$data): array
    {
        $memberIds = $data['member_ids'] ?? [];
        unset($data['member_ids']);

        return array_values(array_unique(array_map('intval', $memberIds)));
    }

    private function syncMembers(Team $team, array $memberIds): void
    {
        $memberIds = array_values(array_unique(array_merge($memberIds, [$team->team_lead_id])));

        $users = $this->userRepository->filterQuery()
            ->whereIn('id', $memberIds)
            ->get(['id', 'company_id', 'department_id']);

        $syncData = $users->mapWithKeys(fn (User $user) => [
            $user->id => [
                'company_id' => $user->company_id,
                'department_id' => $user->department_id,
            ],
        ])->all();

        $team->members()->sync($syncData);
    }

    private function assignTeamLeadRole(int $userId): void
    {
        $user = $this->userRepository->find($userId);

        if (! $user || in_array($user->role, [UserRole::ADMIN, UserRole::DEPARTMENT_HEAD], true)) {
            return;
        }

        $this->userRepository->update($userId, ['role' => UserRole::TEAM_LEAD]);
    }

    private function revertTeamLeadRoleIfNeeded(int $userId): void
    {
        if (Team::query()->where('team_lead_id', $userId)->exists()) {
            return;
        }

        $user = $this->userRepository->find($userId);

        if (! $user || $user->role !== UserRole::TEAM_LEAD) {
            return;
        }

        $this->userRepository->update($userId, ['role' => UserRole::GENERAL]);
    }
}
