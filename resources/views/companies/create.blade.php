@extends('layouts.app')

@section('title', 'Create Company')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Create Company</h5></div>
    <div class="card-body">
      <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('companies._form')
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection