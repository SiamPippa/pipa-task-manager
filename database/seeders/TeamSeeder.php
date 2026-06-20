<?php

namespace Database\Seeders;

use App\Contracts\Services\TeamServiceInterface;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public const TEAMS_PER_DEPARTMENT = 2;

    public function run(): void
    {
        $faker = fake();
        $departments = Department::query()->orderBy('id')->get(['id', 'company_id']);

        if ($departments->isEmpty()) {
            return;
        }

        $teamIndex = 0;

        foreach ($departments as $department) {
            $departmentUsers = User::query()
                ->where('company_id', $department->company_id)
                ->where('department_id', $department->id)
                ->orderBy('id')
                ->get(['id']);

            if ($departmentUsers->count() < 2) {
                continue;
            }

            for ($i = 0; $i < self::TEAMS_PER_DEPARTMENT; $i++) {
                $teamLeadId = $departmentUsers[$i % $departmentUsers->count()]->id;
                $memberCount = min($departmentUsers->count(), $faker->numberBetween(3, 5));
                $memberIds = $departmentUsers->take($memberCount)->pluck('id')->all();

                $name = RealisticData::TEAM_NAMES[$teamIndex % count(RealisticData::TEAM_NAMES)];
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4));

                app(TeamServiceInterface::class)->create([
                    'company_id' => $department->company_id,
                    'department_id' => $department->id,
                    'team_lead_id' => $teamLeadId,
                    'member_ids' => $memberIds,
                    'name' => $name,
                    'code' => $baseCode.str_pad((string) ($teamIndex + 1), 3, '0', STR_PAD_LEFT),
                    'status' => $faker->boolean(95),
                ]);

                $teamIndex++;
            }
        }
    }
}
