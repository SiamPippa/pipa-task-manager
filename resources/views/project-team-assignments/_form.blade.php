@php
  $projectTeamAssignment = $projectTeamAssignment ?? null;
  $selectedCompanyId = old('company_id', $projectTeamAssignment?->project?->company_id);
  $selectedDepartmentId = old('department_id', $projectTeamAssignment?->project?->department_id);
@endphp

@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $selectedCompanyId,
])
@include('partials.form.searchable-select', [
    'name' => 'department_id',
    'label' => 'Department',
    'required' => true,
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
@include('partials.form.searchable-select', [
    'name' => 'assigned_by',
    'label' => 'Assigned By',
    'placeholder' => 'Search assigner...',
    'emptyOption' => 'Current user (default)',
    'options' => $users,
    'selected' => $projectTeamAssignment?->assigned_by,
])
<div class="mb-3">
  <label class="form-label" for="assigned_at">Assigned At</label>
  <input type="datetime-local" class="form-control @error('assigned_at') is-invalid @enderror" id="assigned_at" name="assigned_at" value="{{ old('assigned_at', $projectTeamAssignment?->assigned_at?->format('Y-m-d\TH:i') ?? '') }}">
  @error('assigned_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@include('partials.form.searchable-select-assets')
