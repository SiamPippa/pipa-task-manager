@extends('layouts.app')

@section('title', 'Team Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Team Details</h5>
      <div>
        @can('update', $team)
        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning btn-sm">Edit</a>
        @endcan
        <a href="{{ route('teams.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-4">
        <dt class="col-sm-3">Company</dt><dd class="col-sm-9">{{ $team->company?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $team->name }}</dd>
        <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $team->code ?: '-' }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">
          @if($team->status)
            <span class="badge bg-success">Active</span>
          @else
            <span class="badge bg-secondary">Inactive</span>
          @endif
        </dd>
      </dl>

      <h6 class="mb-3">Team Members</h6>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Team Lead</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($team->members as $member)
              <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>
                  @if($member->pivot->is_team_lead)
                    <span class="badge bg-label-primary">Team Lead</span>
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if($member->pivot->status ?? true)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center">No members found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
