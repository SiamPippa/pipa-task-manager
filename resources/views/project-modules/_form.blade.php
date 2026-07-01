@php
  $projectModule = $projectModule ?? null;
  $selectedCompanyId = old('company_id', $projectModule?->project?->company_id);
@endphp

@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $selectedCompanyId,
])
@include('partials.form.searchable-select', [
    'name' => 'project_id',
    'label' => 'Project',
    'required' => true,
    'placeholder' => 'Search project...',
    'emptyOption' => 'Select project',
    'options' => $projects,
    'selected' => old('project_id', $projectModule?->project_id),
    'dependsOn' => 'company_id',
    'lookup' => 'projects',
])
<div class="mb-3">
  <label class="form-label" for="name">Module Name @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $projectModule?->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="details">Details</label>
  <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="3">{{ old('details', $projectModule?->details ?? '') }}</textarea>
  @error('details')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label" for="start_date">Start Date @include('partials.form.required-marker')</label>
    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $projectModule?->start_date?->format('Y-m-d') ?? '') }}" required>
    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="end_date">End Date @include('partials.form.required-marker')</label>
    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $projectModule?->end_date?->format('Y-m-d') ?? '') }}" required>
    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>
<div class="mb-3">
  <label class="form-label" for="estimated_hours_display">Estimated Hours</label>
  <input type="text" class="form-control" id="estimated_hours_display" value="{{ old('estimated_hours', $projectModule?->estimated_hours ?? '') }}" readonly tabindex="-1">
  <div class="form-text" id="estimated_hours_hint"></div>
  @error('estimated_hours')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<script type="application/json" id="company-working-hours">@json($companyWorkingHours ?? [])</script>

@include('partials.form.searchable-select-assets')

@push('scripts')
<script src="{{ asset('assets/js/module-form.js') }}"></script>
@endpush
