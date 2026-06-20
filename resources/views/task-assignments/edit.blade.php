@extends('layouts.app')

@section('title', 'Edit Task Assignment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Edit Task Assignment</h5></div>
    <div class="card-body">
      <form action="{{ route('task-assignments.update', $taskAssignment) }}" method="POST">
        @csrf @method('PUT')
        @include('task-assignments._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('task-assignments.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection