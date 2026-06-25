@extends('layouts.app')

@section('title', 'Task Assignment Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Task Assignment Details</h5>
      <div>
        @can('update', $taskAssignment)
        <a href="{{ route('task-assignments.edit', $taskAssignment) }}" class="btn btn-warning btn-sm">Edit</a>
        @endcan
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

  <div class="card mt-4">
    <div class="card-header"><h5 class="mb-0">Task Details</h5></div>
    <div class="card-body">
      @php($task = $taskAssignment->task)
      <dl class="row mb-0">
        <dt class="col-sm-3">Project</dt><dd class="col-sm-9">{{ $task?->project?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Module</dt><dd class="col-sm-9">{{ $task?->module?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Title</dt><dd class="col-sm-9">{{ $task?->title ?? '-' }}</dd>
        <dt class="col-sm-3">Branch Name</dt><dd class="col-sm-9"><code>{{ $task?->branch_name ?? '-' }}</code></dd>
        <dt class="col-sm-3">Jira Task No</dt><dd class="col-sm-9">{{ $task?->jira_task_no ?? '-' }}</dd>
        <dt class="col-sm-3">Description</dt><dd class="col-sm-9">{!! $task?->description ?: '-' !!}</dd>
        <dt class="col-sm-3">Estimate Hours</dt><dd class="col-sm-9">{{ $task?->estimate_hours ?? '-' }}</dd>
        <dt class="col-sm-3">Current Status</dt><dd class="col-sm-9"><span class="badge bg-info">{{ str_replace('_', ' ', ucfirst($task?->status ?? '-')) }}</span></dd>
      </dl>
    </div>
  </div>

  @can('update', $taskAssignment->task)
  <div class="card mt-4">
    <div class="card-header"><h5 class="mb-0">Update Task Status</h5></div>
    <div class="card-body">
      <form action="{{ route('task-assignments.task-status.update', $taskAssignment) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
              @foreach (['todo' => 'Todo', 'in_progress' => 'In Progress', 'done' => 'Done'] as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $taskAssignment->task?->status) === $key)>{{ $label }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">Update Status</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  @endcan

  <div class="card mt-4">
    <div class="card-header"><h5 class="mb-0">Task History</h5></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
          <thead>
            <tr>
              <th>When</th>
              <th>Action</th>
              <th>Status Change</th>
              <th>By</th>
            </tr>
          </thead>
          <tbody>
            @forelse($taskAssignment->task?->histories ?? [] as $history)
              <tr>
                <td>{{ $history->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($history->action)) }}</td>
                <td>
                  @if($history->from_status || $history->to_status)
                    {{ $history->from_status ?? '-' }} -> {{ $history->to_status ?? '-' }}
                  @else
                    -
                  @endif
                </td>
                <td>{{ $history->actor?->name ?? 'System' }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center">No history found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
