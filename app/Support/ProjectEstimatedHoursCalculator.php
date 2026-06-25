<?php

namespace App\Support;

use App\Models\CompanySetting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ProjectEstimatedHoursCalculator
{
    private const WEEKEND_DAYS = [
        Carbon::FRIDAY,
        Carbon::SATURDAY,
    ];

    public const DEFAULT_HOURS_PER_DAY = 8;

    public static function isWorkingDay(Carbon $date): bool
    {
        return ! in_array($date->dayOfWeek, self::WEEKEND_DAYS, true);
    }

    public static function workingDaysBetween(Carbon $from, Carbon $to): int
    {
        $period = CarbonPeriod::create(
            $from->copy()->startOfDay(),
            $to->copy()->startOfDay()
        );

        return collect($period)
            ->filter(fn (Carbon $date) => self::isWorkingDay($date))
            ->count();
    }

    public static function resolveHoursPerDay(?CompanySetting $settings): int
    {
        return (int) ($settings?->working_hours_per_day ?? self::DEFAULT_HOURS_PER_DAY);
    }

    public static function estimatedHours(Carbon $from, Carbon $to, int $hoursPerDay): float
    {
        return round(self::workingDaysBetween($from, $to) * $hoursPerDay, 2);
    }
}
