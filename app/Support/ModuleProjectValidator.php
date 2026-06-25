<?php

namespace App\Support;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class ModuleProjectValidator
{
    public static function apply(Validator $validator, Project $project, string $startDate, string $endDate): void
    {
        if (! $project->start_date || ! $project->end_date || $project->estimated_hours === null) {
            $validator->errors()->add(
                'project_id',
                'The selected project must have start date, end date, and estimated hours.'
            );

            return;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();
        $projectStart = $project->start_date->copy()->startOfDay();
        $projectEnd = $project->end_date->copy()->startOfDay();

        if ($start->lt($projectStart)) {
            $validator->errors()->add(
                'start_date',
                'Module start date must be on or after the project start date ('.$projectStart->toDateString().').'
            );
        }

        if ($end->gt($projectEnd)) {
            $validator->errors()->add(
                'end_date',
                'Module end date must be on or before the project end date ('.$projectEnd->toDateString().').'
            );
        }

        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $settings = $project->company?->settings;
        $hoursPerDay = ProjectEstimatedHoursCalculator::resolveHoursPerDay($settings);
        $moduleHours = ProjectEstimatedHoursCalculator::estimatedHours($start, $end, $hoursPerDay);
        $projectHours = (float) $project->estimated_hours;

        if ($moduleHours > $projectHours) {
            $validator->errors()->add(
                'estimated_hours',
                'Module estimated hours ('.number_format($moduleHours, 2).') cannot exceed the project total ('.number_format($projectHours, 2).').'
            );
        }
    }
}
