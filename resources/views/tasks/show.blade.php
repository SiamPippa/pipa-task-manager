@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Task Details</h5>
      <div>
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Project</dt><dd class="col-sm-9">{{ $task->project?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Jira Task No</dt><dd class="col-sm-9">{{ $task->jira_task_no }}</dd>
        <dt class="col-sm-3">Title</dt><dd class="col-sm-9">{{ $task->title }}</dd>
        <dt class="col-sm-3">Description</dt><dd class="col-sm-9">{{ $task->description ?? '-' }}</dd>
        <dt class="col-sm-3">Estimate Hours</dt><dd class="col-sm-9">{{ $task->estimate_hours ?? '-' }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge bg-info">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span></dd>
      </dl>
    </div>
  </div>
</div>
@endsection
