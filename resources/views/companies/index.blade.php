@extends('layouts.app')

@section('title', 'Companies')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Companies</h5>
      @can('create', App\Models\Company::class)
      <a href="{{ route('companies.create') }}" class="btn btn-primary">Add Company</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('companies.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($companies as $item)
          <tr>
                <td>{{ $item->name ?? '-' }}</td>
                <td>{{ $item->code ?? '-' }}</td>
                <td>{{ $item->email ?? '-' }}</td>
                <td>{{ $item->phone ?? '-' }}</td>
                <td>@if($item->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'companies'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $companies->links() }}</div>
  </div>
</div>
@endsection