@extends('layouts.app')

@section('title', 'Edit Office Location')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Edit Office Location</h5></div>
    <div class="card-body">
      <form action="{{ route('office-locations.update', $officeLocation) }}" method="POST">
        @csrf
        @method('PUT')
        @include('office-locations._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('office-locations.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection
