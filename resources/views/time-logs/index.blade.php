@extends('layouts.app')

@section('title', 'Time Logs')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Time Logs</h5>
      @can('create', App\Models\TimeLog::class)
      <a href="{{ route('time-logs.create') }}" class="btn btn-primary">Add Time Log</a>
      @endcan
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Project</th>
            <th>Task</th>
            <th>User</th>
            <th>Start</th>
            <th>End</th>
            <th>Minutes</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($timeLogs as $item)
          <tr>
            <td>{{ $item->project?->name ?? '-' }}</td>
            <td>{{ $item->task?->title ?? '-' }}</td>
            <td>{{ $item->user?->name ?? '-' }}</td>
            <td>{{ $item->start_time?->format('Y-m-d H:i') ?? '-' }}</td>
            <td>{{ $item->end_time?->format('Y-m-d H:i') ?? '-' }}</td>
            <td>{{ $item->total_minutes }}</td>
            <td>@include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'time-logs'])</td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $timeLogs->links() }}</div>
  </div>
</div>
@endsection
