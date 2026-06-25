@extends('layouts.app')

@section('title', 'Daily Report Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Daily Report Details</h5>
      <div>
        <a href="{{ route('daily-reports.edit', $dailyReport) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('daily-reports.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">{{ $dailyReport->user?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Project</dt><dd class="col-sm-9">{{ $dailyReport->project?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Module</dt><dd class="col-sm-9">{{ $dailyReport->module?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Task</dt><dd class="col-sm-9">{{ $dailyReport->task?->title ?? '-' }}</dd>
        <dt class="col-sm-3">Report Date</dt><dd class="col-sm-9">{{ $dailyReport->report_date?->format('Y-m-d') ?? '-' }}</dd>
        <dt class="col-sm-3">Summary</dt><dd class="col-sm-9">{{ $dailyReport->summary }}</dd>
        <dt class="col-sm-3">Blocker</dt><dd class="col-sm-9">{{ $dailyReport->blocker ?? '-' }}</dd>
        <dt class="col-sm-3">Tomorrow Plan</dt><dd class="col-sm-9">{{ $dailyReport->tomorrow_plan ?? '-' }}</dd>
        <dt class="col-sm-3">Progress</dt><dd class="col-sm-9">{{ $dailyReport->progress_percent ?? 0 }}%</dd>
      </dl>

      @if ($dailyReport->timeLog)
      <hr>
      <h6 class="mb-3">Time Log</h6>
      <dl class="row mb-0">
        <dt class="col-sm-3">Start Time</dt><dd class="col-sm-9">{{ $dailyReport->timeLog->start_time?->format('Y-m-d H:i') ?? '-' }}</dd>
        <dt class="col-sm-3">End Time</dt><dd class="col-sm-9">{{ $dailyReport->timeLog->end_time?->format('Y-m-d H:i') ?? '-' }}</dd>
        <dt class="col-sm-3">Total Minutes</dt><dd class="col-sm-9">{{ $dailyReport->timeLog->total_minutes ?? 0 }}</dd>
        <dt class="col-sm-3">Note</dt><dd class="col-sm-9">{{ $dailyReport->timeLog->note ?? '-' }}</dd>
      </dl>
      @endif
    </div>
  </div>
</div>
@endsection
