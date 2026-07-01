@extends('layouts.app')

@section('title', 'Module Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Module Details</h5>
      <div>
        <a href="{{ route('project-modules.edit', $projectModule) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('project-modules.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Company</dt><dd class="col-sm-9">{{ $projectModule->project?->company?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Project</dt><dd class="col-sm-9">{{ $projectModule->project?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Module Name</dt><dd class="col-sm-9">{{ $projectModule->name }}</dd>
        <dt class="col-sm-3">Details</dt><dd class="col-sm-9">{{ $projectModule->details ?: '-' }}</dd>
        <dt class="col-sm-3">Start Date</dt><dd class="col-sm-9">{{ $projectModule->start_date?->format('Y-m-d') ?? '-' }}</dd>
        <dt class="col-sm-3">End Date</dt><dd class="col-sm-9">{{ $projectModule->end_date?->format('Y-m-d') ?? '-' }}</dd>
        <dt class="col-sm-3">Estimated Hours</dt><dd class="col-sm-9">{{ $projectModule->estimated_hours !== null ? number_format((float) $projectModule->estimated_hours, 2) : '-' }}</dd>
        <dt class="col-sm-3">Project Total Hours</dt><dd class="col-sm-9">{{ $projectModule->project?->estimated_hours !== null ? number_format((float) $projectModule->project->estimated_hours, 2) : '-' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
