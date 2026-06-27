<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
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

        $department = Department::query()->firstOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Engineering',
            ],
            [
                'code' => 'ENG',
                'status' => true,
            ]
        );

        $users = [
            [
                'email' => 'admin@pippa.com',
                'name' => 'Siam M Zaque',
                'role' => UserRole::ADMIN,
                'department_id' => null,
            ],
            [
                'email' => 'dept_head@pippa.com',
                'name' => 'Department Head',
                'role' => UserRole::DEPARTMENT_HEAD,
                'department_id' => $department->id,
            ],
            [
                'email' => 'manager@pippa.com',
                'name' => 'Manager',
                'role' => UserRole::MANAGER,
                'department_id' => $department->id,
            ],
            [
                'email' => 'team_lead@pippa.com',
                'name' => 'Team Lead',
                'role' => UserRole::TEAM_LEAD,
                'department_id' => $department->id,
            ],
            [
                'email' => 'general_user@pippa.com',
                'name' => 'General User',
                'role' => UserRole::GENERAL,
                'department_id' => $department->id,
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'company_id' => $company->id,
                    'department_id' => $userData['department_id'],
                    'designation_id' => null,
                    'reporting_manager_id' => null,
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'status' => true,
                ]
            )->syncRoles([$userData['role']]);
        }
    }
}
