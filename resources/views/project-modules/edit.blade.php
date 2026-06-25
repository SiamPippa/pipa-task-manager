@extends('layouts.app')

@section('title', 'Edit Module')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Edit Module</h5></div>
    <div class="card-body">
      <form action="{{ route('project-modules.update', $projectModule) }}" method="POST">
        @csrf @method('PUT')
        @include('project-modules._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('project-modules.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection
