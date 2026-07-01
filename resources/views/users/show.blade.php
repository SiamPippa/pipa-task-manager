@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">User Details</h5>
      <div>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $user->name }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $user->email }}</dd>
        <dt class="col-sm-3">Employee ID</dt><dd class="col-sm-9">{{ $user->employee_id ?? '-' }}</dd>
        <dt class="col-sm-3">Company</dt><dd class="col-sm-9">{{ $user->company?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Designation</dt><dd class="col-sm-9">{{ $user->designation?->title ?? '-' }}</dd>
        <dt class="col-sm-3">Office Location</dt><dd class="col-sm-9">{{ $user->officeLocation?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Reporting Manager</dt><dd class="col-sm-9">{{ $user->reportingManager?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Roles</dt><dd class="col-sm-9">{{ $user->assignedRoleLabels() }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@if($user->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
