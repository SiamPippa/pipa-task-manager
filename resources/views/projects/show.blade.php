@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Project Details</h5>
      <div>
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Company</dt><dd class="col-sm-9">{{ $project->company?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Department</dt><dd class="col-sm-9">{{ $project->department?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $project->name }}</dd>
        <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $project->code }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge bg-primary">{{ ucfirst($project->status) }}</span></dd>
      </dl>
    </div>
  </div>
</div>
@endsection
