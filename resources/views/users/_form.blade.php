@php
    use App\Enums\UserRole;

    $user = $user ?? null;
@endphp

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label" for="name">Name @include('partials.form.required-marker')</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user?->name) }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="email">Email @include('partials.form.required-marker')</label>
    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user?->email) }}" required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="password">Password @unless($user) @include('partials.form.required-marker') @endunless</label>
    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ $user ? '' : 'required' }}>
    @if($user)<small class="text-muted">Leave blank to keep current password</small>@endif
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  @include('partials.form.company-field', [
      'companies' => $companies,
      'selected' => $user?->company_id,
      'wrapperClass' => 'col-md-6 mb-3',
  ])
  @include('partials.form.searchable-select', [
      'name' => 'department_id',
      'label' => 'Department',
      'placeholder' => 'Search department...',
      'emptyOption' => 'None',
      'options' => $departments,
      'selected' => $user?->department_id,
      'wrapperClass' => 'col-md-6 mb-3',
      'dependsOn' => 'company_id',
      'lookup' => 'departments',
  ])
  @include('partials.form.searchable-select', [
      'name' => 'designation_id',
      'label' => 'Designation',
      'placeholder' => 'Search designation...',
      'emptyOption' => 'None',
      'options' => $designations,
      'optionLabel' => 'title',
      'selected' => $user?->designation_id,
      'wrapperClass' => 'col-md-6 mb-3',
      'dependsOn' => 'company_id',
      'lookup' => 'designations',
  ])
  @include('partials.form.searchable-select', [
      'name' => 'reporting_manager_id',
      'label' => 'Reporting Manager',
      'placeholder' => 'Search manager...',
      'emptyOption' => 'None',
      'options' => $managers,
      'selected' => $user?->reporting_manager_id,
      'wrapperClass' => 'col-md-6 mb-3',
      'dependsOn' => 'company_id',
      'lookup' => 'users',
  ])
  @include('partials.form.searchable-select', [
      'name' => 'roles[]',
      'label' => 'Roles',
      'required' => true,
      'placeholder' => 'Search roles...',
      'emptyOption' => false,
      'options' => $roles,
      'selected' => old('roles', $user?->roleIds() ?? [UserRole::GENERAL]),
      'wrapperClass' => 'col-md-6 mb-3',
      'multiple' => true,
  ])
  <div class="col-12">
    <div class="form-check mb-3">
      <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $user?->status ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="status">Active</label>
    </div>
  </div>
</div>

@if ($user)
  <meta name="lookup-exclude-user-id" content="{{ $user->id }}">
@endif

@include('partials.form.searchable-select-assets')
