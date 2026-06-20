<?php

namespace App\Support;

use Carbon\Carbon;

class ProjectMetricsCalculator
{
    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_ON_HOLD = 'on_hold';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DELAYED = 'delayed';

    public const HEALTH_ON_TRACK = 'on_track';

    public const HEALTH_AT_RISK = 'at_risk';

    public const HEALTH_DELAYED = 'delayed';

    public const RISK_LOW = 'low';

    public const RISK_MEDIUM = 'medium';

    public const RISK_HIGH = 'high';

    public static function completionPercent(int $done, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($done / $total) * 100, 1);
    }

    public static function remainingPercent(float $completionPercent): float
    {
        return round(max(0, 100 - $completionPercent), 1);
    }

    public static function varianceHours(float $loggedHours, float $estimatedHours): float
    {
        return round($loggedHours - $estimatedHours, 2);
    }

    public static function variancePercent(float $loggedHours, float $estimatedHours): float
    {
        if ($estimatedHours <= 0) {
            return 0.0;
        }

        return round((($loggedHours - $estimatedHours) / $estimatedHours) * 100, 1);
    }

    public static function utilizationPercent(float $loggedHours, float $capacityHours): float
    {
        if ($capacityHours <= 0) {
            return 0.0;
        }

        return round(min(100, ($loggedHours / $capacityHours) * 100), 1);
    }

    public static function efficiencyScore(float $estimatedHours, float $loggedHours): ?float
    {
        if ($loggedHours <= 0 || $estimatedHours <= 0) {
            return null;
        }

        return round($estimatedHours / $loggedHours, 2);
    }

    public static function completionRate(int $completed, int $assigned): float
    {
        if ($assigned === 0) {
            return 0.0;
        }

        return round(($completed / $assigned) * 100, 1);
    }

    public static function blockedPercent(int $blocked, int $openTasks): float
    {
        if ($openTasks === 0) {
            return 0.0;
        }

        return round(($blocked / $openTasks) * 100, 1);
    }

    public static function effortConsumptionPercent(float $loggedHours, float $estimatedHours): float
    {
        if ($estimatedHours <= 0) {
            return 0.0;
        }

        return round(min(999, ($loggedHours / $estimatedHours) * 100), 1);
    }

    public static function displayStatus(
        string $projectStatus,
        int $totalTasks,
        int $doneTasks,
        int $inProgressTasks,
        int $overdueTasks,
        ?Carbon $lastActivityDate,
        float $variancePercent
    ): string {
        if ($projectStatus === 'completed' || ($totalTasks > 0 && $doneTasks === $totalTasks)) {
            return self::STATUS_COMPLETED;
        }

        if ($projectStatus === 'inactive') {
            return self::STATUS_ON_HOLD;
        }

        if ($totalTasks === 0 || ($doneTasks === 0 && $inProgressTasks === 0 && ! $lastActivityDate)) {
            return self::STATUS_NOT_STARTED;
        }

        if ($overdueTasks > 0 || $variancePercent > 25) {
            return self::STATUS_DELAYED;
        }

        if ($lastActivityDate && $lastActivityDate->lt(now()->subDays(14))) {
            return self::STATUS_ON_HOLD;
        }

        return self::STATUS_IN_PROGRESS;
    }

    public static function scheduleHealth(float $variancePercent, float $blockedPercent): string
    {
        if ($variancePercent > 25 || $blockedPercent > 20) {
            return self::HEALTH_DELAYED;
        }

        if ($variancePercent > 10 || $blockedPercent > 10) {
            return self::HEALTH_AT_RISK;
        }

        return self::HEALTH_ON_TRACK;
    }

    public static function riskLevel(int $overdueCount, int $blockedCount, float $variancePercent): string
    {
        $score = 0;

        if ($overdueCount > 3) {
            $score += 2;
        } elseif ($overdueCount > 0) {
            $score += 1;
        }

        if ($blockedCount > 3) {
            $score += 2;
        } elseif ($blockedCount > 0) {
            $score += 1;
        }

        if ($variancePercent > 25) {
            $score += 2;
        } elseif ($variancePercent > 10) {
            $score += 1;
        }

        return match (true) {
            $score >= 4 => self::RISK_HIGH,
            $score >= 2 => self::RISK_MEDIUM,
            default => self::RISK_LOW,
        };
    }

    public static function displayStatusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_DELAYED => 'Delayed',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function scheduleHealthLabel(string $health): string
    {
        return match ($health) {
            self::HEALTH_ON_TRACK => 'On Track',
            self::HEALTH_AT_RISK => 'At Risk',
            self::HEALTH_DELAYED => 'Delayed',
            default => ucfirst(str_replace('_', ' ', $health)),
        };
    }

    public static function riskLabel(string $risk): string
    {
        return ucfirst($risk);
    }

    public static function statusBadgeClass(string $status): string
    {
        return match ($status) {
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_IN_PROGRESS => 'bg-primary',
            self::STATUS_NOT_STARTED => 'bg-secondary',
            self::STATUS_ON_HOLD => 'bg-warning',
            self::STATUS_DELAYED => 'bg-danger',
            default => 'bg-label-secondary',
        };
    }

    public static function healthBadgeClass(string $health): string
    {
        return match ($health) {
            self::HEALTH_ON_TRACK => 'bg-success',
            self::HEALTH_AT_RISK => 'bg-warning',
            self::HEALTH_DELAYED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public static function riskBadgeClass(string $risk): string
    {
        return match ($risk) {
            self::RISK_LOW => 'bg-success',
            self::RISK_MEDIUM => 'bg-warning',
            self::RISK_HIGH => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
