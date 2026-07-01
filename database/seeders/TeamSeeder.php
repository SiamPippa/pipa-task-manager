<?php

namespace Database\Seeders;

use App\Contracts\Services\TeamServiceInterface;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public const TEAMS_PER_COMPANY = 4;

    public function run(): void
    {
        $faker = fake();
        $companies = Company::query()->orderBy('id')->get(['id']);

        if ($companies->isEmpty()) {
            return;
        }

        $teamIndex = 0;

        foreach ($companies as $company) {
            $companyUsers = User::query()
                ->where('company_id', $company->id)
                ->orderBy('id')
                ->get(['id']);

            if ($companyUsers->count() < 2) {
                continue;
            }

            for ($i = 0; $i < self::TEAMS_PER_COMPANY; $i++) {
                $teamLeadId = $companyUsers[$i % $companyUsers->count()]->id;
                $memberCount = min($companyUsers->count(), $faker->numberBetween(3, 5));
                $memberIds = $companyUsers->take($memberCount)->pluck('id')->all();

                $members = collect($memberIds)
                    ->map(fn (int $userId) => [
                        'user_id' => $userId,
                        'is_team_lead' => $userId === $teamLeadId,
                        'status' => true,
                    ])
                    ->values()
                    ->all();

                $name = RealisticData::TEAM_NAMES[$teamIndex % count(RealisticData::TEAM_NAMES)];
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4));

                app(TeamServiceInterface::class)->create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'code' => $baseCode.str_pad((string) ($teamIndex + 1), 3, '0', STR_PAD_LEFT),
                    'status' => $faker->boolean(95),
                    'members' => $members,
                ]);

                $teamIndex++;
            }
        }
    }
}
