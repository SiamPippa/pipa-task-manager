@extends('layouts.app')

@section('title', 'Edit Company Setting')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')
  <div class="card">
    <div class="card-header"><h5 class="mb-0">Edit Company Setting</h5></div>
    <div class="card-body">
      <form action="{{ route('company-settings.update', $companySetting) }}" method="POST">
        @csrf @method('PUT')
        @include('company-settings._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('company-settings.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection