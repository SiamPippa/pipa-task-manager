<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->firstOrCreate(
            ['code' => 'PIPPA'],
            [
                'name' => 'Pippa',
                'email' => 'hello@pippa.com',
                'phone' => null,
                'logo' => null,
                'status' => true,
            ]
        );

        $officeLocation = OfficeLocation::query()->firstOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Head Office',
            ],
            [
                'code' => 'HO',
                'address' => null,
                'status' => true,
            ]
        );

        $users = [
            [
                'email' => 'admin@pippa.com',
                'name' => 'Siam M Zaque',
                'role' => UserRole::SUPER_ADMIN,
            ],
            [
                'email' => 'company_admin@pippa.com',
                'name' => 'Company Admin',
                'role' => UserRole::COMPANY_ADMIN,
            ],
            [
                'email' => 'project_manager@pippa.com',
                'name' => 'Project Manager',
                'role' => UserRole::PROJECT_MANAGER,
            ],
            [
                'email' => 'team_lead@pippa.com',
                'name' => 'Team Lead',
                'role' => UserRole::TEAM_LEAD,
            ],
            [
                'email' => 'developer@pippa.com',
                'name' => 'Developer',
                'role' => UserRole::DEVELOPER,
            ],
            [
                'email' => 'qa@pippa.com',
                'name' => 'QA',
                'role' => UserRole::QA,
            ],
            [
                'email' => 'viewer@pippa.com',
                'name' => 'Viewer',
                'role' => UserRole::VIEWER,
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'company_id' => $company->id,
                    'employee_id' => strtoupper(strtok($userData['email'], '@')),
                    'designation_id' => null,
                    'reporting_manager_id' => null,
                    'office_location_id' => $officeLocation->id,
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'status' => true,
                ]
            )->syncRoles([$userData['role']]);
        }
    }
}
