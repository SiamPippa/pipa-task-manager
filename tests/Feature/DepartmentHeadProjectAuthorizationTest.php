<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DepartmentHeadProjectAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_head_can_view_projects_across_departments_in_same_company(): void
    {
        [$company, , $deptB, $departmentHead] = $this->departmentHeadInDepartment();

        $projectInOtherDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
        ]);

        $this->actingAs($departmentHead);

        $this->assertTrue($projectInOtherDepartment->isVisibleTo($departmentHead));
        $this->assertTrue(Gate::forUser($departmentHead)->allows('view', $projectInOtherDepartment));
    }

    public function test_department_head_can_view_but_cannot_update_or_delete_other_department_projects(): void
    {
        [$company, , $deptB, $departmentHead] = $this->departmentHeadInDepartment();

        $projectInOtherDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
        ]);

        $this->actingAs($departmentHead);

        $this->assertTrue(Gate::forUser($departmentHead)->allows('view', $projectInOtherDepartment));
        $this->assertFalse(Gate::forUser($departmentHead)->allows('update', $projectInOtherDepartment));
        $this->assertFalse(Gate::forUser($departmentHead)->allows('delete', $projectInOtherDepartment));
    }

    public function test_department_head_can_update_and_delete_projects_in_own_department(): void
    {
        [$company, $deptA, , $departmentHead] = $this->departmentHeadInDepartment();

        $projectInOwnDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptA->id,
        ]);

        $this->actingAs($departmentHead);

        $this->assertTrue(Gate::forUser($departmentHead)->allows('update', $projectInOwnDepartment));
        $this->assertTrue(Gate::forUser($departmentHead)->allows('delete', $projectInOwnDepartment));
    }

    public function test_department_head_index_lists_projects_from_all_departments(): void
    {
        [$company, , $deptB, $departmentHead] = $this->departmentHeadInDepartment();

        Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
            'name' => 'Cross Department Project For Head',
        ]);

        $this->actingAs($departmentHead)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Cross Department Project For Head');
    }

    /**
     * @return array{0: Company, 1: Department, 2: Department, 3: User}
     */
    private function departmentHeadInDepartment(): array
    {
        $company = Company::factory()->active()->create();
        $deptA = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Operations A',
            'code' => 'OPSA',
        ]);
        $deptB = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Operations B',
            'code' => 'OPSB',
        ]);

        $departmentHead = User::factory()->forOrganization($company, $deptA)->create();
        $departmentHead->syncRoles([UserRole::DEPARTMENT_HEAD]);

        return [$company, $deptA, $deptB, $departmentHead];
    }
}
