@php $timeLog = $timeLog ?? null; @endphp

@include('partials.form.searchable-select', [
    'name' => 'task_id',
    'label' => 'Task',
    'required' => true,
    'placeholder' => 'Search task...',
    'emptyOption' => 'Select task',
    'options' => $tasks,
    'selected' => old('task_id', $timeLog?->task_id),
])

@if(auth()->user()->actingCan(\App\Enums\Permission::TIME_LOGS_MANAGE))
@include('partials.form.searchable-select', [
    'name' => 'user_id',
    'label' => 'User',
    'required' => true,
    'placeholder' => 'Search user...',
    'emptyOption' => 'Select user',
    'options' => $users,
    'selected' => old('user_id', $timeLog?->user_id ?? auth()->id()),
])
@else
  <input type="hidden" name="user_id" value="{{ auth()->id() }}">
@endif

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label" for="start_time">Start Time @include('partials.form.required-marker')</label>
    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $timeLog?->start_time?->format('Y-m-d\TH:i')) }}" required>
    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="end_time">End Time</label>
    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $timeLog?->end_time?->format('Y-m-d\TH:i')) }}">
    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="mb-3">
  <label class="form-label" for="note">Note</label>
  <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note', $timeLog?->note ?? '') }}</textarea>
  @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@include('partials.form.searchable-select-assets')
