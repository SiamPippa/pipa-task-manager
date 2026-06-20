@extends('layouts.app')

@section('title', 'Task Assignment Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Task Assignment Details</h5>
      <div>
        <a href="{{ route('task-assignments.edit', $taskAssignment) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('task-assignments.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Task</dt><dd class="col-sm-9">{{ $taskAssignment->task?->title ?? '-' }}</dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">{{ $taskAssignment->user?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Assigned By</dt><dd class="col-sm-9">{{ $taskAssignment->assignedBy?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Assigned At</dt><dd class="col-sm-9">{{ $taskAssignment->assigned_at?->format('Y-m-d H:i') ?? '-' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
