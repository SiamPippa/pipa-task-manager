@extends('layouts.app')

@section('title', 'Company Setting Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h5 class="mb-0">Company Setting Details</h5>
      <div>
        <a href="{{ route('company-settings.edit', $companySetting) }}" class="btn btn-warning btn-sm">Edit</a>
        <a href="{{ route('company-settings.index') }}" class="btn btn-secondary btn-sm">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-4">Company</dt><dd class="col-sm-8">{{ $companySetting->company?->name ?? '-' }}</dd>
        <dt class="col-sm-4">Office Start</dt><dd class="col-sm-8">{{ $companySetting->office_start_time ?? '-' }}</dd>
        <dt class="col-sm-4">Office End</dt><dd class="col-sm-8">{{ $companySetting->office_end_time ?? '-' }}</dd>
        <dt class="col-sm-4">Working Hours/Day</dt><dd class="col-sm-8">{{ $companySetting->working_hours_per_day }}</dd>
        <dt class="col-sm-4">Manual Time Log</dt><dd class="col-sm-8">{{ $companySetting->allow_manual_time_log ? 'Yes' : 'No' }}</dd>
        <dt class="col-sm-4">Daily Report Required</dt><dd class="col-sm-8">{{ $companySetting->require_daily_report ? 'Yes' : 'No' }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
