@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Projects</h5>
      @can('create', App\Models\Project::class)
      <a href="{{ route('projects.create') }}" class="btn btn-primary">Add Project</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('projects.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Company</th>
            <th>Department</th>
            <th>Name</th>
            <th>Code</th>
            <th>Start</th>
            <th>End</th>
            <th>Total Hours</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($projects as $item)
          <tr>
            <td>{{ $item->company?->name ?? '-' }}</td>
            <td>{{ $item->department?->name ?? '-' }}</td>
            <td>{{ $item->name ?? '-' }}</td>
            <td>{{ $item->code ?? '-' }}</td>
            <td>{{ $item->start_date?->format('Y-m-d') ?? '-' }}</td>
            <td>{{ $item->end_date?->format('Y-m-d') ?? '-' }}</td>
            <td>{{ $item->estimated_hours !== null ? number_format((float) $item->estimated_hours, 2) : '-' }}</td>
            <td><span class="badge bg-primary">{{ ucfirst($item->status) }}</span></td>
            <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'projects'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $projects->links() }}</div>
  </div>
</div>
@endsection