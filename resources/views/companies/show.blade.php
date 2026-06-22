@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Company Details</h5>
      <div>
        <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $company->name }}</dd>
        <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $company->code }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $company->email ?? '-' }}</dd>
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $company->phone ?? '-' }}</dd>
        <dt class="col-sm-3">Logo</dt>
        <dd class="col-sm-9">
          @if ($company->logo_url)
            <img src="{{ $company->logo_url }}" alt="{{ $company->name }} logo" class="img-thumbnail" style="max-height: 100px;">
          @else
            -
          @endif
        </dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@if($company->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
