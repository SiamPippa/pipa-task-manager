<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectTeamAssignment;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ProjectTeamAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $projects = Project::query()
            ->where('status', '!=', 'inactive')
            ->orderBy('id')
            ->get(['id', 'company_id']);

        if ($projects->isEmpty()) {
            return;
        }

        foreach ($projects as $project) {
            $teams = Team::query()
                ->where('company_id', $project->company_id)
                ->where('status', true)
                ->inRandomOrder()
                ->take($faker->numberBetween(1, 2))
                ->get(['id', 'team_lead_id']);

            foreach ($teams as $team) {
                ProjectTeamAssignment::query()->firstOrCreate(
                    [
                        'project_id' => $project->id,
                        'team_id' => $team->id,
                    ],
                    [
                        'assigned_by' => $team->team_lead_id,
                        'assigned_at' => $faker->dateTimeBetween('-8 months', '-2 weeks'),
                    ]
                );
            }
        }
    }
}
