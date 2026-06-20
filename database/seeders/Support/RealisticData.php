<?php

namespace Database\Seeders\Support;

use Faker\Generator;

class RealisticData
{
    public const COMPANY_PREFIXES = [
        'Meridian', 'Summit', 'Northbridge', 'Atlas', 'Pinnacle', 'Horizon',
        'Vertex', 'Catalyst', 'Apex', 'Sterling', 'Bluewave', 'Ironclad',
        'Clearpath', 'Evergreen', 'Lighthouse', 'Redwood', 'Silverline',
        'Trident', 'Vanguard', 'Westfield', 'Brighton', 'Crestview',
        'Delta', 'Eaglepoint', 'Frontier', 'Granite', 'Highland', 'Integra',
        'Juniper', 'Keystone', 'Landmark', 'Mosaic', 'Nova', 'Orion',
        'Prism', 'Quantum', 'Riverstone', 'Sapphire', 'Titan', 'Unity',
    ];

    public const COMPANY_SUFFIXES = [
        'Technologies', 'Software', 'Digital Solutions', 'Systems',
        'Analytics', 'Consulting Group', 'Innovation Labs', 'Cloud Services',
        'Data Systems', 'Engineering', 'Networks', 'Platforms',
        'Automation', 'Intelligence', 'Logistics', 'Financial Services',
        'Health Informatics', 'Cybersecurity', 'Media Group', 'Retail Tech',
    ];

    public const DEPARTMENTS = [
        'Engineering', 'Product Management', 'Quality Assurance', 'DevOps',
        'Human Resources', 'Finance', 'Sales', 'Marketing', 'Customer Success',
        'Legal', 'Operations', 'Business Intelligence', 'Security',
        'Technical Support', 'Research and Development', 'Design',
        'Procurement', 'Corporate Communications', 'Data Science', 'IT Infrastructure',
    ];

    public const TEAM_NAMES = [
        'Platform Core', 'Mobile Experience', 'Payments', 'Identity and Access',
        'Growth Engineering', 'Infrastructure', 'Release Engineering',
        'Frontend Guild', 'Backend Services', 'API Platform', 'Search and Discovery',
        'Notifications', 'Billing', 'Onboarding', 'Integrations', 'Compliance',
        'Performance', 'Observability', 'Developer Experience', 'Enterprise Accounts',
    ];

    public const DESIGNATIONS = [
        'Software Engineer', 'Senior Software Engineer', 'Staff Engineer',
        'Engineering Manager', 'Product Manager', 'Senior Product Manager',
        'QA Engineer', 'Senior QA Engineer', 'DevOps Engineer', 'Site Reliability Engineer',
        'UI/UX Designer', 'Business Analyst', 'Scrum Master', 'Technical Lead',
        'Data Analyst', 'Data Engineer', 'Security Analyst', 'Support Engineer',
        'Account Executive', 'Customer Success Manager',
    ];

    public const PROJECT_NAMES = [
        'Customer Portal Modernization', 'Mobile App Rewrite', 'Billing Engine Upgrade',
        'Single Sign-On Rollout', 'Reporting Dashboard', 'Inventory Sync Service',
        'Partner API Gateway', 'Notification Service', 'Data Warehouse Migration',
        'Checkout Optimization', 'Admin Console Redesign', 'Search Relevance Tuning',
        'Compliance Audit Toolkit', 'Onboarding Flow Revamp', 'Payment Reconciliation',
        'Legacy Monolith Decomposition', 'Observability Stack', 'Feature Flag Platform',
        'Document Management System', 'Sales Forecasting Tool',
    ];

    public const TASK_TITLES = [
        'Implement authentication middleware for partner routes',
        'Refactor order processing pipeline for idempotency',
        'Add pagination and filtering to project listing API',
        'Fix race condition in concurrent task assignment updates',
        'Write integration tests for daily report submission',
        'Optimize database indexes on time log queries',
        'Design schema changes for multi-company user roles',
        'Migrate legacy CSV import to background job queue',
        'Update dashboard charts to use aggregated metrics',
        'Resolve memory leak in websocket notification worker',
        'Add validation for overlapping time log entries',
        'Create admin export for monthly utilization report',
        'Implement retry policy for failed webhook deliveries',
        'Review and tighten API rate limiting rules',
        'Build team capacity view for project managers',
        'Patch XSS vulnerability in comment rendering',
        'Align task status workflow with Jira transitions',
        'Add audit trail for company settings changes',
        'Improve error messaging on login failure paths',
        'Document deployment runbook for staging releases',
    ];

    public const REPORT_SUMMARIES = [
        'Completed API endpoint for user profile updates and merged two related pull requests after code review.',
        'Finished regression testing on the billing module and logged three edge-case defects for next sprint.',
        'Deployed hotfix for session timeout issue and monitored production metrics for two hours post-release.',
        'Held architecture review for notification service and drafted sequence diagrams for the team.',
        'Implemented pagination on department listing and updated corresponding feature tests.',
        'Pair-programmed on checkout flow refactor and unblocked QA with updated test credentials.',
        'Resolved production incident related to delayed queue workers and documented root cause.',
        'Prepared sprint demo slides and validated staging data for stakeholder walkthrough.',
        'Refactored repository layer for task assignments to reduce duplicate query patterns.',
        'Coordinated with design on updated empty states for the daily reports screen.',
    ];

    public const BLOCKERS = [
        'Waiting for security review approval on OAuth scope changes.',
        'Staging environment database refresh delayed until tonight.',
        'Third-party payment sandbox credentials expired and renewal is in progress.',
        'Dependency upgrade blocked by compatibility issue in legacy PDF exporter.',
        'Need product decision on default team lead assignment rules.',
        null, null, null,
    ];

    public const TOMORROW_PLANS = [
        'Continue integration testing and open PR for review.',
        'Start implementation of export CSV endpoint for time logs.',
        'Meet with infrastructure team about Redis cluster sizing.',
        'Complete migration script validation on anonymized dataset.',
        'Finalize unit tests for designation CRUD service layer.',
        'Review open bug backlog and prioritize P1 items.',
        null, null,
    ];

    public static function companyName(Generator $faker, int $index): string
    {
        $prefix = self::COMPANY_PREFIXES[$index % count(self::COMPANY_PREFIXES)];
        $suffix = self::COMPANY_SUFFIXES[intdiv($index, count(self::COMPANY_PREFIXES)) % count(self::COMPANY_SUFFIXES)];

        return "{$prefix} {$suffix}";
    }

    public static function companyCode(string $name, int $index): string
    {
        $words = preg_split('/\s+/', $name);
        $code = '';

        foreach ($words as $word) {
            $code .= strtoupper(substr($word, 0, 1));
        }

        return substr($code, 0, 4).str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
    }

    public static function companyEmail(string $name): string
    {
        return 'contact@'.self::companyDomain($name);
    }

    public static function companyPhone(Generator $faker): string
    {
        return '+1-'.$faker->numberBetween(200, 989).'-'.$faker->numberBetween(200, 999).'-'.$faker->numberBetween(1000, 9999);
    }

    public static function slug(string $value): string
    {
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($value));

        return trim($slug, '-');
    }

    public static function companyDomain(string $name): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $name));

        return substr($slug, 0, 24).'.com';
    }

    public static function userEmail(string $companyName, int $userNumber): string
    {
        $userPart = 'user'.str_pad((string) $userNumber, 3, '0', STR_PAD_LEFT);

        return "{$userPart}@".self::companyDomain($companyName);
    }

    public static function jiraTaskNo(string $projectCode, int $number): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $projectCode), 0, 4));

        return $prefix.'-'.$number;
    }

    public static function timeLogNote(Generator $faker): string
    {
        $activities = [
            'Sprint planning preparation and backlog refinement.',
            'Code review for pull request on authentication module.',
            'Debugging intermittent timeout in report generation job.',
            'Client call to clarify daily report field requirements.',
            'Writing unit tests for team assignment edge cases.',
            'Database query optimization for dashboard widgets.',
            'Documentation update for internal API endpoints.',
            'Pairing session on service-repository refactor.',
        ];

        return $faker->randomElement($activities);
    }

    public static function taskStatusForIndex(int $projectIndex, int $taskIndex, int $taskCount, string $projectStatus): string
    {
        if ($projectStatus === 'completed') {
            return 'done';
        }

        if ($projectStatus === 'inactive') {
            return fake()->randomElement(['todo', 'todo', 'in_progress']);
        }

        $completionRatio = ($projectIndex % 5) / 4;
        $positionRatio = $taskCount > 1 ? $taskIndex / ($taskCount - 1) : 0;
        $score = ($completionRatio * 0.6) + ($positionRatio * 0.4);

        if ($score < 0.25) {
            return 'todo';
        }

        if ($score < 0.65) {
            return 'in_progress';
        }

        return fake()->boolean(88) ? 'done' : 'in_progress';
    }

    public static function reportEndDate(string $taskStatus, \Carbon\Carbon $assignedAt, \Carbon\Carbon $rangeEnd): \Carbon\Carbon
    {
        if ($taskStatus === 'todo') {
            return $rangeEnd->copy()->subDays(fake()->numberBetween(7, 21));
        }

        if ($taskStatus === 'in_progress') {
            return $rangeEnd->copy()->subDays(fake()->numberBetween(0, 7));
        }

        return $assignedAt->copy()->addDays(fake()->numberBetween(21, 120))->min($rangeEnd);
    }

    public static function workDurationMinutes(Generator $faker, \Carbon\Carbon $reportDate): int
    {
        $month = (int) $reportDate->format('n');
        $seasonalBoost = in_array($month, [3, 4, 9, 10], true) ? 15 : 0;

        return min(480, $faker->numberBetween(180, 420) + $seasonalBoost);
    }

    public static function progressPercent(string $taskStatus, \Carbon\Carbon $assignedAt, \Carbon\Carbon $reportDate, \Carbon\Carbon $reportEnd): int
    {
        $totalDays = max(1, $assignedAt->diffInDays($reportEnd));
        $elapsedDays = min($totalDays, $assignedAt->diffInDays($reportDate));
        $timelineProgress = (int) round(($elapsedDays / $totalDays) * 100);

        return match ($taskStatus) {
            'done' => min(100, max(85, $timelineProgress)),
            'todo' => min(35, max(5, (int) round($timelineProgress * 0.35))),
            default => min(90, max(20, $timelineProgress)),
        };
    }

    public static function reportSummary(Generator $faker, string $taskTitle): string
    {
        $templates = [
            'Made progress on "%s" and pushed updates after internal review.',
            'Continued implementation work for "%s" and closed two related subtasks.',
            'Validated "%s" changes in staging and documented test evidence.',
            'Unblocked "%s" by pairing with a teammate and refining the acceptance criteria.',
            'Completed a focused working session on "%s" and prepared the next pull request.',
        ];

        return sprintf($faker->randomElement($templates), $taskTitle);
    }
}
