<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TaskAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $tasks = Task::query()->with('project')->orderBy('id')->get();
        $usersByCompany = User::query()->get()->groupBy('company_id');
        $projectMemberIds = $this->projectMemberMap();

        if ($tasks->isEmpty()) {
            return;
        }

        foreach ($tasks as $task) {
            $assigneePool = $this->assigneePool(
                $task->project_id,
                $task->project->company_id,
                $projectMemberIds,
                $usersByCompany
            );

            if ($assigneePool->isEmpty()) {
                continue;
            }

            $assigneeCount = min($assigneePool->count(), $faker->numberBetween(1, 2));
            $assignees = $assigneePool->random($assigneeCount);

            foreach ($assignees as $assignee) {
                $assignerPool = $assigneePool->where('id', '!=', $assignee->id);

                if ($assignerPool->isEmpty()) {
                    $assignerPool = $assigneePool;
                }

                TaskAssignment::query()->firstOrCreate(
                    [
                        'task_id' => $task->id,
                        'user_id' => $assignee->id,
                    ],
                    [
                        'assigned_by' => $assignerPool->random()->id,
                        'assigned_at' => $faker->dateTimeBetween('-7 months', '-1 week'),
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, Collection<int, User>>
     */
    private function projectMemberMap(): array
    {
        $map = [];

        Project::query()
            ->with(['assignedTeams.members:id'])
            ->orderBy('id')
            ->get(['id'])
            ->each(function (Project $project) use (&$map) {
                $memberIds = $project->assignedTeams
                    ->flatMap(fn ($team) => $team->members->pluck('id'))
                    ->unique()
                    ->values();

                $map[$project->id] = $memberIds;
            });

        return $map;
    }

    /**
     * @param  array<int, Collection<int, int>>  $projectMemberIds
     * @param  Collection<int, Collection<int, User>>  $usersByCompany
     * @return Collection<int, User>
     */
    private function assigneePool(
        int $projectId,
        int $companyId,
        array $projectMemberIds,
        Collection $usersByCompany
    ): Collection {
        $companyUsers = $usersByCompany->get($companyId, collect());
        $memberIds = $projectMemberIds[$projectId] ?? collect();

        if ($memberIds->isNotEmpty()) {
            $teamMembers = $companyUsers->whereIn('id', $memberIds);

            if ($teamMembers->isNotEmpty()) {
                return $teamMembers->values();
            }
        }

        return $companyUsers->values();
    }
}
