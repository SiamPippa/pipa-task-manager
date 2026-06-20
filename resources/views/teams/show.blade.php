@extends('layouts.app')

@section('title', 'Team Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Team Details</h5>
      <div>
        <a href="{{ route('teams.edit', $team) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('teams.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Company</dt><dd class="col-sm-9">{{ $team->company?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Department</dt><dd class="col-sm-9">{{ $team->department?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Team Lead</dt><dd class="col-sm-9">{{ $team->teamLead?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $team->name }}</dd>
        <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $team->code }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@if($team->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</dd>
        <dt class="col-sm-3">Members</dt>
        <dd class="col-sm-9">
          @forelse ($team->members as $member)
            <span class="badge bg-label-primary me-1 mb-1">{{ $member->name }}</span>
          @empty
            -
          @endforelse
        </dd>
      </dl>
    </div>
  </div>
</div>
@endsection
