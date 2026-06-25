@extends('layouts.app')

@section('title', 'Assign User Roles')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Assign User Roles</h5>
      <p class="text-muted mb-0 mt-1">Assign one or more roles to a user.</p>
    </div>
    @include('partials.list-filters', ['action' => route('user-roles.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Department</th>
            <th>Assigned Roles</th>
            <th>Update Roles</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $item)
          <tr>
            <td>{{ $item->name ?? '-' }}</td>
            <td>{{ $item->email ?? '-' }}</td>
            <td>{{ $item->company?->name ?? '-' }}</td>
            <td>{{ $item->department?->name ?? '-' }}</td>
            <td>{{ $item->assignedRoleLabels() }}</td>
            <td>
              <form action="{{ route('user-roles.update', $item) }}" method="POST" class="d-flex gap-2 align-items-center">
                @csrf
                @method('PATCH')
                <select name="roles[]" class="form-select form-select-sm" style="min-width: 220px;" multiple size="{{ min(count($roles), 4) }}">
                  @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(in_array($value, $item->roleIds(), true))>{{ $label }}</option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Save</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center">No records found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $users->links() }}</div>
  </div>
</div>
@endsection
