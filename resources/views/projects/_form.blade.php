@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $project->company_id ?? null,
])
@include('partials.form.searchable-select', [
    'name' => 'manager_ids[]',
    'label' => 'Project Managers',
    'placeholder' => 'Search project managers...',
    'emptyOption' => false,
    'options' => $managers,
    'selected' => old('manager_ids', $project?->managers?->pluck('id')->all() ?? []),
    'dependsOn' => 'company_id',
    'lookup' => 'users',
    'multiple' => true,
])
<div class="mb-3">
  <label class="form-label" for="client_name">Client Name @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" value="{{ old('client_name', $project->client_name ?? '') }}" required>
  @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="name">Name @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $project->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $project->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="description">Description</label>
  <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $project->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label" for="start_date">Start Date @include('partials.form.required-marker')</label>
    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d') ?? '') }}" required>
    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="end_date">End Date @include('partials.form.required-marker')</label>
    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d') ?? '') }}" required>
    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>
<div class="mb-3">
  <label class="form-label" for="estimated_hours_display">Estimated Hours</label>
  <input type="text" class="form-control" id="estimated_hours_display" value="{{ old('estimated_hours', $project->estimated_hours ?? '') }}" readonly tabindex="-1">
  <div class="form-text" id="estimated_hours_hint"></div>
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

<script type="application/json" id="company-working-hours">@json($companyWorkingHours ?? [])</script>

@include('partials.form.searchable-select-assets')

@push('scripts')
<script src="{{ asset('assets/js/project-form.js') }}"></script>
@endpush
