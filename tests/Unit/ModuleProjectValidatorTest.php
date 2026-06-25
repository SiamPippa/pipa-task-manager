<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Support\ModuleProjectValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ModuleProjectValidatorTest extends TestCase
{
    public function test_rejects_module_hours_exceeding_project_total(): void
    {
        $project = new Project([
            'estimated_hours' => 40,
        ]);
        $project->start_date = Carbon::parse('2026-06-01');
        $project->end_date = Carbon::parse('2026-06-30');

        $validator = Validator::make(
            ['start_date' => '2026-06-01', 'end_date' => '2026-06-30'],
            ['start_date' => 'required', 'end_date' => 'required']
        );

        ModuleProjectValidator::apply($validator, $project, '2026-06-01', '2026-06-30');

        $this->assertTrue($validator->errors()->has('estimated_hours'));
    }

    public function test_rejects_module_end_date_after_project_end(): void
    {
        $project = new Project([
            'estimated_hours' => 200,
        ]);
        $project->start_date = Carbon::parse('2026-06-01');
        $project->end_date = Carbon::parse('2026-06-30');

        $validator = Validator::make(
            ['start_date' => '2026-06-01', 'end_date' => '2026-07-15'],
            ['start_date' => 'required', 'end_date' => 'required']
        );

        ModuleProjectValidator::apply($validator, $project, '2026-06-01', '2026-07-15');

        $this->assertTrue($validator->errors()->has('end_date'));
    }

    public function test_allows_parallel_modules_within_project_cap(): void
    {
        $project = new Project([
            'estimated_hours' => 100,
        ]);
        $project->start_date = Carbon::parse('2026-06-01');
        $project->end_date = Carbon::parse('2026-06-30');

        $validator = Validator::make(
            ['start_date' => '2026-06-01', 'end_date' => '2026-06-15'],
            ['start_date' => 'required', 'end_date' => 'required']
        );

        ModuleProjectValidator::apply($validator, $project, '2026-06-01', '2026-06-15');

        $this->assertFalse($validator->errors()->has('estimated_hours'));
    }
}
