@extends('layouts.app')

@section('title', $roleLabel . ' Permissions')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0">{{ $roleLabel }} Permissions</h5>
        <p class="text-muted mb-0 mt-1">Role ID: {{ $roleId }}</p>
      </div>
      <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-sm">Back to Matrix</a>
    </div>
    <div class="card-body">
      @if (in_array('*', $rolePermissions, true))
        <div class="alert alert-info mb-4">This role has full access to all permissions.</div>
      @endif

      @foreach ($groups as $groupName => $groupPermissions)
        <h6 class="text-uppercase text-muted mt-4 mb-3">{{ $groupName }}</h6>
        <ul class="list-group mb-2">
          @foreach ($groupPermissions as $permission)
            @php
              $granted = in_array('*', $rolePermissions, true)
                || in_array($permission, $rolePermissions, true)
                || (str_ends_with($permission, '.view') && collect($rolePermissions)->contains(fn ($p) => str_starts_with($p, substr($permission, 0, -5).'.')));
            @endphp
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div>{{ $labels[$permission] ?? $permission }}</div>
                <small class="text-muted">{{ $permission }}</small>
              </div>
              @if ($granted)
                <span class="badge bg-success">Granted</span>
              @else
                <span class="badge bg-secondary">Denied</span>
              @endif
            </li>
          @endforeach
        </ul>
      @endforeach
    </div>
  </div>
</div>
@endsection
