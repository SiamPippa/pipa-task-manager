@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Tasks</h5>
      @can('create', App\Models\Task::class)
      <a href="{{ route('project-tasks.create') }}" class="btn btn-primary">Add Task</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('project-tasks.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Jira Task No</th>
            <th>Title</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($tasks as $item)
          <tr>
            <td>{{ $item->project?->name ?? '-' }}</td>
            <td>{{ $item->jira_task_no ?? '-' }}</td>
            <td>{{ $item->title ?? '-' }}</td>
            <td><span class="badge bg-info">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span></td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'project-tasks'])
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="20" class="text-center">No records found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $tasks->links() }}</div>
  </div>
</div>
@endsection