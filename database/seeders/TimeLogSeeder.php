<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Time logs are created alongside daily reports in DailyReportSeeder
 * because each time log requires a linked daily report record.
 */
class TimeLogSeeder extends Seeder
{
    public function run(): void
    {
        // Intentionally empty — see DailyReportSeeder.
    }
}
