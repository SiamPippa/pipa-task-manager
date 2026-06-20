@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Department Details</h5>
      <div>
        <a href="{{ route('departments.edit', $department) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('departments.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Company</dt><dd class="col-sm-9">{{ $department->company?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $department->name }}</dd>
        <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $department->code }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@if($department->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
