@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Create Task</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('project-tasks.store') }}" method="POST">
        @csrf
        @include('tasks._form')
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('project-tasks.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection