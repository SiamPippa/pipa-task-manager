@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Company Settings</h5>
      @can('create', App\Models\CompanySetting::class)
      <a href="{{ route('company-settings.create') }}" class="btn btn-primary">Add Company Setting</a>
      @endcan
    </div>
    @include('partials.list-filters', ['action' => route('company-settings.index'), 'fields' => $filterFields, 'filters' => $filters])
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
                <th>Name</th>
                <th>Office Start Time</th>
                <th>Office End Time</th>
                <th>Working Hours Per Day</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($companySettings as $item)
          <tr>
                <td>{{ $item->company?->name ?? '-' }}</td>
                <td>{{ $item->office_start_time ?? '-' }}</td>
                <td>{{ $item->office_end_time ?? '-' }}</td>
                <td>{{ $item->working_hours_per_day ?? '-' }}</td>
                        <td>
              @include('partials.resource-actions', ['model' => $item, 'routePrefix' => 'company-settings'])
            </td>
          </tr>
          @empty
          <tr><td colspan="20" class="text-center">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $companySettings->links() }}</div>
  </div>
</div>
@endsection