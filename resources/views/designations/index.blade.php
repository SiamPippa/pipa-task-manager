@extends('layouts.app')

@section('title', 'Designations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Designations</h5>
      @can('create', App\Models\Designation::class)
      <a href="{{ route('designations.create') }}" class="btn btn-primary">Add Designation</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('designations.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>Name</th>
                <th>Title</th>
                <th>Code</th>
                <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($designations as $item)
          <tr>
                <td>{{ $item->company?->name ?? '-' }}</td>
                <td>{{ $item->title ?? '-' }}</td>
                <td>{{ $item->code ?? '-' }}</td>
                <td>@if($item->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'designations'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $designations->links() }}</div>
  </div>
</div>
@endsection