@extends('layouts.app')

@section('title', 'Task Assignments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Task Assignments</h5>
      @can('create', App\Models\TaskAssignment::class)
      <a href="{{ route('task-assignments.create') }}" class="btn btn-primary">Add Task Assignment</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('task-assignments.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>Project</th>
                <th>Module</th>
                <th>Task</th>
                <th>User</th>
                <th>Assigned At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($taskAssignments as $item)
          <tr>
                <td>{{ $item->task?->project?->name ?? '-' }}</td>
                <td>{{ $item->task?->module?->name ?? '-' }}</td>
                <td>{{ $item->task?->title ?? '-' }}</td>
                <td>{{ $item->user?->name ?? '-' }}</td>
                <td>{{ $item->assigned_at ?? '-' }}</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'task-assignments'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $taskAssignments->links() }}</div>
  </div>
</div>
@endsection