@php
  $dailyReport = $dailyReport ?? null;
  $timeLog = $timeLog ?? $dailyReport?->timeLog ?? null;
  $reportUser = $dailyReport?->user ?? auth()->user();
@endphp

<div class="mb-3">
  <label class="form-label">User</label>
  <input type="text" class="form-control" value="{{ $reportUser?->name ?? '-' }}" disabled readonly>
</div>
@include('partials.form.searchable-select', [
    'name' => 'project_id',
    'label' => 'Project',
    'required' => true,
    'placeholder' => 'Search project...',
    'emptyOption' => 'Select project',
    'options' => $projects,
    'selected' => $dailyReport?->project_id,
])
@include('partials.form.searchable-select', [
    'name' => 'task_id',
    'label' => 'Task',
    'required' => true,
    'placeholder' => 'Search task...',
    'emptyOption' => 'Select task',
    'options' => $tasks ?? collect(),
    'optionLabel' => 'title',
    'selected' => $dailyReport?->task_id,
    'dependsOn' => 'project_id',
    'lookup' => 'tasks',
])
<div class="mb-3">
  <label class="form-label" for="report_date">Report Date</label>
  <input type="date" class="form-control @error('report_date') is-invalid @enderror" id="report_date" name="report_date" value="{{ old('report_date', $dailyReport?->report_date?->format('Y-m-d') ?? '') }}" required>
  @error('report_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="summary">Summary</label>
  <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary" rows="3">{{ old('summary', $dailyReport?->summary ?? '') }}</textarea>
  @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="blocker">Blocker</label>
  <textarea class="form-control @error('blocker') is-invalid @enderror" id="blocker" name="blocker" rows="2">{{ old('blocker', $dailyReport?->blocker ?? '') }}</textarea>
  @error('blocker')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="tomorrow_plan">Tomorrow Plan</label>
  <textarea class="form-control @error('tomorrow_plan') is-invalid @enderror" id="tomorrow_plan" name="tomorrow_plan" rows="2">{{ old('tomorrow_plan', $dailyReport?->tomorrow_plan ?? '') }}</textarea>
  @error('tomorrow_plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="progress_percent">Progress %</label>
  <input type="number" min="0" max="100" class="form-control @error('progress_percent') is-invalid @enderror" id="progress_percent" name="progress_percent" value="{{ old('progress_percent', $dailyReport?->progress_percent ?? '') }}">
  @error('progress_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<hr class="my-4">
<h6 class="mb-3">Time Log</h6>

<div class="mb-3">
  <label class="form-label" for="start_time">Start Time</label>
  <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', isset($timeLog->start_time) ? $timeLog->start_time->format('Y-m-d\TH:i') : '') }}" required>
  @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="end_time">End Time</label>
  <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', isset($timeLog->end_time) ? $timeLog->end_time->format('Y-m-d\TH:i') : '') }}">
  @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="total_minutes">Total Minutes</label>
  <input type="number" class="form-control @error('total_minutes') is-invalid @enderror" id="total_minutes" name="total_minutes" value="{{ old('total_minutes', $timeLog->total_minutes ?? '') }}">
  @error('total_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="note">Time Log Note</label>
  <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note', $timeLog->note ?? '') }}</textarea>
  @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@include('partials.form.searchable-select-assets')
