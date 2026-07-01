@extends('layouts.app')

@section('title', 'Time Log Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Time Log Details</h5>
      <div>
        @can('update', $timeLog)
        <a href="{{ route('time-logs.edit', $timeLog) }}" class="btn btn-warning btn-sm">Edit</a>
        @endcan
        <a href="{{ route('time-logs.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Project</dt><dd class="col-sm-9">{{ $timeLog->project?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Task</dt><dd class="col-sm-9">{{ $timeLog->task?->title ?? '-' }}</dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">{{ $timeLog->user?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Start Time</dt><dd class="col-sm-9">{{ $timeLog->start_time?->format('Y-m-d H:i') ?? '-' }}</dd>
        <dt class="col-sm-3">End Time</dt><dd class="col-sm-9">{{ $timeLog->end_time?->format('Y-m-d H:i') ?? '-' }}</dd>
        <dt class="col-sm-3">Total Minutes</dt><dd class="col-sm-9">{{ $timeLog->total_minutes }}</dd>
        <dt class="col-sm-3">Note</dt><dd class="col-sm-9">{{ $timeLog->note ?: '-' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
