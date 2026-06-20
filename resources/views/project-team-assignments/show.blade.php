@extends('layouts.app')

@section('title', 'Project Team Assignment Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Project Team Assignment Details</h5>
      <div>
        <a href="{{ route('project-team-assignments.edit', $projectTeamAssignment) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('project-team-assignments.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Project</dt><dd class="col-sm-9">{{ $projectTeamAssignment->project?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Team</dt><dd class="col-sm-9">{{ $projectTeamAssignment->team?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Assigned By</dt><dd class="col-sm-9">{{ $projectTeamAssignment->assignedBy?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Assigned At</dt><dd class="col-sm-9">{{ $projectTeamAssignment->assigned_at?->format('Y-m-d H:i') ?? '-' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
