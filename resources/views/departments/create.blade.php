@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Create Department</h5></div>
    <div class="card-body">
      <form action="{{ route('departments.store') }}" method="POST">
        @csrf
        @include('departments._form')
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('departments.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection