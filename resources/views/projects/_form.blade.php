@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $project->company_id ?? null,
])
@include('partials.form.searchable-select', [
    'name' => 'department_id',
    'label' => 'Department',
    'required' => true,
    'placeholder' => 'Search department...',
    'emptyOption' => 'Select department',
    'options' => $departments,
    'selected' => $project->department_id ?? null,
    'dependsOn' => 'company_id',
    'lookup' => 'departments',
])
<div class="mb-3">
  <label class="form-label" for="name">Name</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $project->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $project->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
@include('partials.form.searchable-select', [
    'name' => 'status',
    'label' => 'Status',
    'required' => true,
    'placeholder' => 'Search status...',
    'emptyOption' => false,
    'options' => collect(['active', 'inactive', 'completed'])->map(fn ($s) => (object) ['id' => $s, 'name' => ucfirst($s)]),
    'selected' => $project->status ?? 'active',
])

@include('partials.form.searchable-select-assets')
