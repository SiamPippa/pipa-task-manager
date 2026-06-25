@php
    $task = $task ?? null;
@endphp

@include('partials.form.searchable-select', [
    'name' => 'project_id',
    'label' => 'Project',
    'required' => true,
    'placeholder' => 'Search project...',
    'emptyOption' => 'Select project',
    'options' => $projects,
    'selected' => old('project_id', $task?->project_id),
])
@include('partials.form.searchable-select', [
    'name' => 'project_module_id',
    'label' => 'Module',
    'required' => true,
    'placeholder' => 'Search module...',
    'emptyOption' => 'Select module',
    'options' => $modules ?? collect(),
    'selected' => old('project_module_id', $task?->project_module_id),
    'dependsOn' => 'project_id',
    'lookup' => 'project-modules',
])
@include('partials.form.searchable-select', [
    'name' => 'type',
    'label' => 'Type',
    'required' => true,
    'placeholder' => 'Search type...',
    'emptyOption' => 'Select type',
    'options' => collect(\App\Enums\TaskType::labels())->map(fn ($label, $value) => (object) ['id' => $value, 'name' => $label]),
    'selected' => old('type', $task?->type ?? \App\Enums\TaskType::FEATURE),
])
<div class="mb-3">
  <label class="form-label" for="title">Title @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task?->title ?? '') }}" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="branch_name">Branch Name @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" value="{{ old('branch_name', $task?->branch_name ?? '') }}" required pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$" maxlength="60">
  <div class="form-text">Auto-generated from title. Use lowercase letters, numbers, and hyphens only.</div>
  @error('branch_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="jira_task_no">Jira Task No</label>
  <input type="text" class="form-control @error('jira_task_no') is-invalid @enderror" id="jira_task_no" name="jira_task_no" value="{{ old('jira_task_no', $task?->jira_task_no ?? '') }}">
  @error('jira_task_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
@include('partials.form.rich-text-editor', [
    'name' => 'description',
    'value' => $task?->description ?? '',
])
<div class="mb-3">
  <label class="form-label" for="estimate_hours">Estimate Hours</label>
  <input type="number" step="0.01" class="form-control @error('estimate_hours') is-invalid @enderror" id="estimate_hours" name="estimate_hours" value="{{ old('estimate_hours', $task?->estimate_hours ?? '') }}">
  @error('estimate_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
@include('partials.form.searchable-select', [
    'name' => 'status',
    'label' => 'Status',
    'required' => true,
    'placeholder' => 'Search status...',
    'emptyOption' => false,
    'options' => collect(['todo', 'in_progress', 'done'])->map(fn ($s) => (object) ['id' => $s, 'name' => str_replace('_', ' ', ucfirst($s))]),
    'selected' => old('status', $task?->status ?? 'todo'),
])

@include('partials.form.searchable-select-assets')

@push('scripts')
    <script src="{{ asset('assets/js/task-form.js') }}"></script>
@endpush
