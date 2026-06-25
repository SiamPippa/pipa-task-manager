@php
  $projectTeamAssignment = $projectTeamAssignment ?? null;
  $selectedCompanyId = old('company_id', $projectTeamAssignment?->project?->company_id);
  $selectedDepartmentId = old('department_id', $projectTeamAssignment?->project?->department_id);
@endphp

@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $selectedCompanyId,
    'required' => false,
])
@include('partials.form.searchable-select', [
    'name' => 'department_id',
    'label' => 'Department',
    'placeholder' => 'Search department...',
    'emptyOption' => 'Select department',
    'options' => $departments,
    'selected' => $selectedDepartmentId,
    'dependsOn' => 'company_id',
    'lookup' => 'departments',
])
@include('partials.form.searchable-select', [
    'name' => 'project_id',
    'label' => 'Project',
    'required' => true,
    'placeholder' => 'Search project...',
    'emptyOption' => 'Select project',
    'options' => $projects,
    'selected' => $projectTeamAssignment?->project_id,
    'dependsOn' => ['company_id', 'department_id'],
    'lookup' => 'projects',
])
@include('partials.form.searchable-select', [
    'name' => 'team_id',
    'label' => 'Team',
    'required' => true,
    'placeholder' => 'Search team...',
    'emptyOption' => 'Select team',
    'options' => $teams,
    'selected' => $projectTeamAssignment?->team_id,
    'dependsOn' => ['company_id', 'department_id'],
    'lookup' => 'teams',
])

@include('partials.form.searchable-select-assets')
