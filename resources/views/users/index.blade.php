@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Users</h5>
      @can('create', App\Models\User::class)
      <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('users.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Employee ID</th>
            <th>Company</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $item)
          <tr>
            <td>{{ $item->name ?? '-' }}</td>
            <td>{{ $item->email ?? '-' }}</td>
            <td>{{ $item->employee_id ?? '-' }}</td>
            <td>{{ $item->company?->name ?? '-' }}</td>
            <td>{{ $item->assignedRoleLabels() }}</td>
            <td>@if($item->status)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'users'])
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="20" class="text-center">No records found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $users->links() }}</div>
  </div>
</div>
@endsection