<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\TaskAssignment;
use App\Models\TimeLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class DailyReportSeeder extends Seeder
{
    public const MONTHS_BACK = 6;

    public const TARGET_DAILY_REPORTS = 1500;

    public function run(): void
    {
        $faker = fake();
        $assignments = TaskAssignment::query()
            ->with(['task.project', 'user'])
            ->get();

        if ($assignments->isEmpty()) {
            return;
        }

        $rangeStart = now()->subMonths(self::MONTHS_BACK)->startOfDay();
        $rangeEnd = now()->startOfDay();
        $weekdays = collect(CarbonPeriod::create($rangeStart, $rangeEnd))
            ->filter(fn (Carbon $date) => ! $date->isWeekend())
            ->values();

        if ($weekdays->isEmpty()) {
            return;
        }

        $usedKeys = [];
        $created = 0;
        $attempts = 0;
        $maxAttempts = self::TARGET_DAILY_REPORTS * 8;

        while ($created < self::TARGET_DAILY_REPORTS && $attempts < $maxAttempts) {
            $attempts++;
            $assignment = $assignments->random();
            $task = $assignment->task;
            $project = $task->project;
            $user = $assignment->user;

            if (! $project || ! $user) {
                continue;
            }

            $assignedAt = Carbon::parse($assignment->assigned_at)->startOfDay();
            $reportEnd = RealisticData::reportEndDate($task->status, $assignedAt, $rangeEnd);

            $eligibleDays = $weekdays->filter(
                fn (Carbon $date) => $date->greaterThanOrEqualTo($assignedAt) && $date->lessThanOrEqualTo($reportEnd)
            );

            if ($eligibleDays->isEmpty()) {
                continue;
            }

            $reportDate = $eligibleDays->random()->copy();
            $dedupeKey = $user->id.'|'.$task->id.'|'.$reportDate->toDateString();

            if (isset($usedKeys[$dedupeKey])) {
                continue;
            }

            $usedKeys[$dedupeKey] = true;

            $startHour = $faker->numberBetween(8, 10);
            $startMinute = $faker->randomElement([0, 15, 30]);
            $durationMinutes = RealisticData::workDurationMinutes($faker, $reportDate);
            $startTime = $reportDate->copy()->setTime($startHour, $startMinute, 0);
            $progressPercent = RealisticData::progressPercent($task->status, $assignedAt, $reportDate, $reportEnd);

            $report = DailyReport::query()->create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'task_id' => $task->id,
                'report_date' => $reportDate->toDateString(),
                'summary' => RealisticData::reportSummary($faker, $task->title),
                'blocker' => RealisticData::BLOCKERS[array_rand(RealisticData::BLOCKERS)],
                'tomorrow_plan' => RealisticData::TOMORROW_PLANS[array_rand(RealisticData::TOMORROW_PLANS)],
                'progress_percent' => $progressPercent,
            ]);

            TimeLog::query()->create([
                'daily_report_id' => $report->id,
                'project_id' => $project->id,
                'task_id' => $task->id,
                'user_id' => $user->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes($durationMinutes),
                'total_minutes' => $durationMinutes,
                'note' => RealisticData::timeLogNote($faker),
            ]);

            $created++;
        }
    }
}
