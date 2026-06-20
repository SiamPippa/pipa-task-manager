@extends('layouts.app')

@section('title', 'Project Team Assignments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Project Team Assignments</h5>
      @can('create', App\Models\ProjectTeamAssignment::class)
      <a href="{{ route('project-team-assignments.create') }}" class="btn btn-primary">Add Assignment</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('project-team-assignments.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>Project</th>
                <th>Team</th>
                <th>Assigned By</th>
                <th>Assigned At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($projectTeamAssignments as $item)
          <tr>
                <td>{{ $item->project?->name ?? '-' }}</td>
                <td>{{ $item->team?->name ?? '-' }}</td>
                <td>{{ $item->assignedBy?->name ?? '-' }}</td>
                <td>{{ $item->assigned_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'project-team-assignments'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $projectTeamAssignments->links() }}</div>
  </div>
</div>
@endsection
