@php
  $team = $team ?? null;
  $selectedMembers = old('member_ids', $team?->members?->pluck('id')->all() ?? []);
@endphp

@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $team?->company_id,
])
@include('partials.form.searchable-select', [
    'name' => 'department_id',
    'label' => 'Department',
    'required' => true,
    'placeholder' => 'Search department...',
    'emptyOption' => 'Select department',
    'options' => $departments,
    'selected' => $team?->department_id,
    'dependsOn' => 'company_id',
    'lookup' => 'departments',
])
@include('partials.form.searchable-select', [
    'name' => 'team_lead_id',
    'label' => 'Team Lead',
    'required' => true,
    'placeholder' => 'Search team lead...',
    'emptyOption' => 'Select team lead',
    'options' => $users,
    'selected' => $team?->team_lead_id,
    'dependsOn' => ['company_id', 'department_id'],
    'lookup' => 'users',
])
@include('partials.form.searchable-select', [
    'name' => 'member_ids[]',
    'label' => 'Team Members',
    'placeholder' => 'Search members...',
    'emptyOption' => false,
    'options' => $users,
    'selected' => $selectedMembers,
    'dependsOn' => ['company_id', 'department_id'],
    'lookup' => 'users',
    'multiple' => true,
])
<div class="mb-3">
  <label class="form-label" for="name">Name</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $team?->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $team?->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $team?->status ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="status">Active</label>
</div>

@include('partials.form.searchable-select-assets')
