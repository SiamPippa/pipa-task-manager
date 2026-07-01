@extends('layouts.app')

@section('title', 'Teams')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Teams</h5>
      @can('create', App\Models\Team::class)
      <a href="{{ route('teams.create') }}" class="btn btn-primary">Add Team</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('teams.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>Company</th>
                <th>Team Lead</th>
                <th>Members</th>
                <th>Name</th>
                <th>Code</th>
                <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($teams as $item)
          <tr>
                <td>{{ $item->company?->name ?? '-' }}</td>
                <td>{{ $item->teamLead?->name ?? '-' }}</td>
                <td>{{ $item->members->count() }}</td>
                <td>{{ $item->name ?? '-' }}</td>
                <td>{{ $item->code ?? '-' }}</td>
                <td>@if($item->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'teams'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $teams->links() }}</div>
  </div>
</div>
@endsection
