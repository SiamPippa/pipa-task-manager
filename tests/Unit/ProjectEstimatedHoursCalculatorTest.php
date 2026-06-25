<?php

namespace Tests\Unit;

use App\Support\ProjectEstimatedHoursCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ProjectEstimatedHoursCalculatorTest extends TestCase
{
    public function test_counts_sun_through_thu_as_working_days(): void
    {
        $from = Carbon::parse('2026-06-01'); // Monday
        $to = Carbon::parse('2026-06-07');   // Sunday

        $this->assertSame(5, ProjectEstimatedHoursCalculator::workingDaysBetween($from, $to));
        $this->assertSame(40.0, ProjectEstimatedHoursCalculator::estimatedHours($from, $to, 8));
    }

    public function test_excludes_friday_and_saturday(): void
    {
        $from = Carbon::parse('2026-06-05'); // Friday
        $to = Carbon::parse('2026-06-06');   // Saturday

        $this->assertSame(0, ProjectEstimatedHoursCalculator::workingDaysBetween($from, $to));
        $this->assertSame(0.0, ProjectEstimatedHoursCalculator::estimatedHours($from, $to, 8));
    }

    public function test_uses_company_hours_per_day(): void
    {
        $from = Carbon::parse('2026-06-07'); // Sunday
        $to = Carbon::parse('2026-06-11');   // Thursday

        $this->assertSame(5, ProjectEstimatedHoursCalculator::workingDaysBetween($from, $to));
        $this->assertSame(35.0, ProjectEstimatedHoursCalculator::estimatedHours($from, $to, 7));
    }
}
