@extends('layouts.app')

@section('title', 'Modules')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Modules</h5>
      @can('create', App\Models\ProjectModule::class)
      <a href="{{ route('project-modules.create') }}" class="btn btn-primary">Add Module</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('project-modules.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Company</th>
            <th>Project</th>
            <th>Module</th>
            <th>Start</th>
            <th>End</th>
            <th>Est. Hours</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($projectModules as $item)
          <tr>
            <td>{{ $item->project?->company?->name ?? '-' }}</td>
            <td>{{ $item->project?->name ?? '-' }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->start_date?->format('Y-m-d') ?? '-' }}</td>
            <td>{{ $item->end_date?->format('Y-m-d') ?? '-' }}</td>
            <td>{{ $item->estimated_hours !== null ? number_format((float) $item->estimated_hours, 2) : '-' }}</td>
            <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'project-modules'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $projectModules->links() }}</div>
  </div>
</div>
@endsection
