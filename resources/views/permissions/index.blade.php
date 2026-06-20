@extends('layouts.app')

@section('title', 'RBAC Permissions')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Role-Based Access Control</h5>
      <p class="text-muted mb-0 mt-1">Permission matrix for all roles. Scoped access (company, department, team) is enforced in policies.</p>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table table-bordered table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th style="min-width: 220px;">Permission</th>
            @foreach ($matrix as $role)
              <th class="text-center" style="min-width: 120px;">{{ $role['label'] }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($groups as $groupName => $groupPermissions)
            <tr class="table-secondary">
              <td colspan="{{ count($matrix) + 1 }}"><strong>{{ $groupName }}</strong></td>
            </tr>
            @foreach ($groupPermissions as $permission)
              @if ($permission === App\Enums\Permission::RBAC_VIEW)
                @continue
              @endif
              <tr>
                <td>
                  <div>{{ $labels[$permission] ?? $permission }}</div>
                  <small class="text-muted">{{ $permission }}</small>
                </td>
                @foreach ($matrix as $roleId => $role)
                  <td class="text-center">
                    @if ($role['permissions'][$permission] ?? false)
                      <span class="badge bg-success">Yes</span>
                    @else
                      <span class="badge bg-secondary">No</span>
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      <div class="row g-3">
        @foreach ($matrix as $roleId => $role)
          <div class="col-md-4 col-lg">
            <a href="{{ route('permissions.show', $roleId) }}" class="btn btn-outline-primary w-100">
              {{ $role['label'] }} Details
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
