<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public const USERS_PER_DEPARTMENT = 5;

    public function run(): void
    {
        $faker = fake();
        $companies = Company::query()->orderBy('id')->get();

        if ($companies->isEmpty()) {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => 'admin@pippa.com'],
            [
                'company_id' => $companies->first()->id,
                'name' => 'Siam M Zaque',
                'password' => Hash::make('password'),
                'status' => true,
                'role' => UserRole::ADMIN,
            ]
        );

        $createdUsers = collect();

        foreach ($companies as $company) {
            $companyUserNumber = 0;
            $departments = Department::query()
                ->where('company_id', $company->id)
                ->orderBy('id')
                ->get();

            foreach ($departments as $department) {
                $designations = Designation::query()
                    ->where('company_id', $company->id)
                    ->orderBy('id')
                    ->get();

                for ($i = 0; $i < self::USERS_PER_DEPARTMENT; $i++) {
                    $designation = $designations->isEmpty()
                        ? null
                        : $designations[$i % $designations->count()];
                    $name = $faker->name();
                    $companyUserNumber++;

                    $user = User::query()->create([
                        'company_id' => $company->id,
                        'department_id' => $department->id,
                        'designation_id' => $designation?->id,
                        'reporting_manager_id' => null,
                        'name' => $name,
                        'email' => RealisticData::userEmail(
                            $company->name,
                            $companyUserNumber
                        ),
                        'password' => Hash::make('password'),
                        'status' => $faker->boolean(94),
                        'role' => UserRole::GENERAL,
                    ]);

                    $createdUsers->push($user);
                }
            }
        }

        $createdUsers->each(function (User $user) use ($createdUsers) {
            $departmentPeers = $createdUsers
                ->where('department_id', $user->department_id)
                ->where('id', '!=', $user->id);

            if ($departmentPeers->isEmpty()) {
                return;
            }

            $user->update([
                'reporting_manager_id' => $departmentPeers->random()->id,
            ]);
        });
    }
}
