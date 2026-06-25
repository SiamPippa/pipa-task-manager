@extends('layouts.app')

@section('title', 'Daily Reports')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Daily Reports</h5>
      @can('create', App\Models\DailyReport::class)
      <a href="{{ route('daily-reports.create') }}" class="btn btn-primary">Add Daily Report</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('daily-reports.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>User</th>
                <th>Project</th>
                <th>Module</th>
                <th>Task</th>
                <th>Report Date</th>
                <th>Progress %</th>
                <th>Time (min)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($dailyReports as $item)
          <tr>
                <td>{{ $item->user?->name ?? '-' }}</td>
                <td>{{ $item->project?->name ?? '-' }}</td>
                <td>{{ $item->module?->name ?? '-' }}</td>
                <td>{{ $item->task?->title ?? '-' }}</td>
                <td>{{ $item->report_date ?? '-' }}</td>
                <td>{{ $item->progress_percent ?? '-' }}</td>
                <td>{{ $item->timeLog?->total_minutes ?? '-' }}</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'daily-reports'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $dailyReports->links() }}</div>
  </div>
</div>
@endsection