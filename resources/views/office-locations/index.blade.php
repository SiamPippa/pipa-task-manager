@extends('layouts.app')

@section('title', 'Office Locations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Office Locations</h5>
      @can('create', App\Models\OfficeLocation::class)
      <a href="{{ route('office-locations.create') }}" class="btn btn-primary">Add Office Location</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('office-locations.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Company</th>
            <th>Name</th>
            <th>Code</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($officeLocations as $item)
          <tr>
            <td>{{ $item->company?->name ?? '-' }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->code ?: '-' }}</td>
            <td>@if($item->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
            <td>@include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'office-locations'])</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $officeLocations->links() }}</div>
  </div>
</div>
@endsection
