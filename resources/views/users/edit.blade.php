@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Edit User</h5></div>
    <div class="card-body">
      <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        @include('users._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection