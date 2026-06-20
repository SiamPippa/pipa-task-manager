@include('partials.form.searchable-select', [
    'name' => 'task_id',
    'label' => 'Task',
    'required' => true,
    'placeholder' => 'Search task...',
    'emptyOption' => 'Select task',
    'options' => $tasks,
    'optionLabel' => 'title',
    'selected' => $taskAssignment->task_id ?? null,
])
@include('partials.form.searchable-select', [
    'name' => 'user_id',
    'label' => 'User',
    'required' => true,
    'placeholder' => 'Search user...',
    'emptyOption' => 'Select user',
    'options' => collect(),
    'selected' => $taskAssignment->user_id ?? null,
    'dependsOn' => 'task_id',
    'lookup' => 'users',
])

@include('partials.form.searchable-select-assets')
