<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            CompanySettingSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            UserSeeder::class,
            TeamSeeder::class,
            ProjectSeeder::class,
            ProjectTeamAssignmentSeeder::class,
            TaskSeeder::class,
            TaskAssignmentSeeder::class,
            DailyReportSeeder::class,
        ]);
    }
}
