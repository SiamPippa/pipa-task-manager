@include('partials.form.searchable-select', [
    'name' => 'project_id',
    'label' => 'Project',
    'required' => true,
    'placeholder' => 'Search project...',
    'emptyOption' => 'Select project',
    'options' => $projects,
    'selected' => $task->project_id ?? null,
])
<div class="mb-3">
  <label class="form-label" for="jira_task_no">Jira Task No</label>
  <input type="text" class="form-control @error('jira_task_no') is-invalid @enderror" id="jira_task_no" name="jira_task_no" value="{{ old('jira_task_no', $task->jira_task_no ?? '') }}" required>
  @error('jira_task_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="title">Title</label>
  <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title ?? '') }}" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="description">Description</label>
  <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $task->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="estimate_hours">Estimate Hours</label>
  <input type="number" step="0.01" class="form-control @error('estimate_hours') is-invalid @enderror" id="estimate_hours" name="estimate_hours" value="{{ old('estimate_hours', $task->estimate_hours ?? '') }}">
  @error('estimate_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
@include('partials.form.searchable-select', [
    'name' => 'status',
    'label' => 'Status',
    'required' => true,
    'placeholder' => 'Search status...',
    'emptyOption' => false,
    'options' => collect(['todo', 'in_progress', 'done'])->map(fn ($s) => (object) ['id' => $s, 'name' => str_replace('_', ' ', ucfirst($s))]),
    'selected' => $task->status ?? 'todo',
])

@include('partials.form.searchable-select-assets')
