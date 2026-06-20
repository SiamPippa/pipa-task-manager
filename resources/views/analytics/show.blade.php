@extends('layouts.app')

@section('title', $project['name'].' Analytics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
      <a href="{{ route('dashboard') }}" class="text-muted"><i class="bx bx-chevron-left"></i> Back to dashboard</a>
      <h4 class="mb-1 mt-2">{{ $project['name'] }}</h4>
      <p class="text-muted mb-0">
        {{ $project['code'] }} · {{ $project['department_name'] ?? '-' }} · {{ $project['company_name'] ?? '-' }}
      </p>
    </div>
    <span class="badge {{ $project['display_status_class'] }} fs-6">{{ $project['display_status_label'] }}</span>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h6 class="mb-2">Overall Progress</h6>
          @include('analytics.partials.progress-bar', ['percent' => $project['completion_percent'], 'showLabel' => true])
          <small class="text-muted">{{ number_format($project['remaining_percent'], 1) }}% remaining</small>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
          <span class="badge {{ $project['schedule_health_class'] }} me-1">{{ $project['schedule_health_label'] }}</span>
          <span class="badge {{ $project['risk_class'] }}">{{ $project['risk_label'] }} Risk</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    @include('analytics.partials.kpi-card', [
      'label' => 'Completion',
      'value' => number_format($project['completion_percent'], 1).'%',
      'subtitle' => $project['done_tasks'].' of '.$project['total_tasks'].' tasks',
      'icon' => 'bx-task',
    ])
    @include('analytics.partials.kpi-card', [
      'label' => 'Hours Variance',
      'value' => ($project['variance_hours'] > 0 ? '+' : '').number_format($project['variance_hours'], 1).'h',
      'subtitle' => number_format($project['variance_percent'], 1).'% vs estimate',
      'icon' => 'bx-line-chart',
    ])
    @include('analytics.partials.kpi-card', [
      'label' => 'Utilization',
      'value' => number_format($project['utilization_percent'], 1).'%',
      'subtitle' => $project['active_contributors'].' active of '.$project['assigned_members'].' assigned',
      'icon' => 'bx-group',
    ])
    @include('analytics.partials.kpi-card', [
      'label' => 'Team Efficiency',
      'value' => $teamAvgEfficiency ? number_format($teamAvgEfficiency, 2) : 'N/A',
      'subtitle' => 'Avg efficiency score',
      'icon' => 'bx-trophy',
    ])
  </div>

  <div class="card mb-4">
    @include('partials.list-filters', ['action' => route('analytics.projects.show', $project['id']), 'fields' => $filterFields, 'filters' => $filters])
  </div>

  <div class="row mb-4">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Task Distribution</h5></div>
        <div class="card-body"><div id="taskStatusChart"></div></div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Estimated vs Actual Hours</h5></div>
        <div class="card-body"><div id="hoursComparisonChart"></div></div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Team Workload</h5></div>
        <div class="card-body"><div id="workloadChart"></div></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Developer Efficiency</h5></div>
        <div class="card-body"><div id="efficiencyChart"></div></div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Completion Trend</h5></div>
        <div class="card-body"><div id="completionTrendChart"></div></div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Team Performance</h5></div>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Developer</th>
                <th>Hours Logged</th>
                <th>Tasks Done</th>
                <th>Completion Rate</th>
                <th>Efficiency</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($developers as $dev)
              <tr>
                <td>{{ $dev['name'] }}</td>
                <td>{{ number_format($dev['logged_hours'], 1) }}h</td>
                <td>{{ $dev['completed_tasks'] }}/{{ $dev['assigned_tasks'] }}</td>
                <td>{{ number_format($dev['completion_rate'], 1) }}%</td>
                <td>{{ $dev['efficiency_score'] ? number_format($dev['efficiency_score'], 2) : 'N/A' }}</td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center">No developer activity in this period.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Project Health</h5></div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-6">Schedule</dt>
            <dd class="col-6"><span class="badge {{ $project['schedule_health_class'] }}">{{ $project['schedule_health_label'] }}</span></dd>
            <dt class="col-6">Effort Used</dt>
            <dd class="col-6">{{ number_format($project['effort_consumption_percent'], 1) }}%</dd>
            <dt class="col-6">Blocked Tasks</dt>
            <dd class="col-6">{{ $project['blocked_tasks'] }}</dd>
            <dt class="col-6">Overdue Tasks</dt>
            <dd class="col-6">{{ $project['overdue_tasks'] }}</dd>
            <dt class="col-6">Remaining Hours</dt>
            <dd class="col-6">{{ number_format($project['remaining_hours'], 1) }}h</dd>
            <dt class="col-6">Risk Level</dt>
            <dd class="col-6"><span class="badge {{ $project['risk_class'] }}">{{ $project['risk_label'] }}</span></dd>
          </dl>
          <hr>
          <h6 class="mb-2">Assigned Members ({{ count($project['assigned_members_list']) }})</h6>
          <ul class="list-unstyled mb-3">
            @foreach ($project['assigned_members_list'] as $member)
              <li><small>{{ $member['name'] }}</small></li>
            @endforeach
          </ul>
          <h6 class="mb-2">Active Contributors ({{ count($project['active_contributors_list']) }})</h6>
          <ul class="list-unstyled mb-0">
            @foreach ($project['active_contributors_list'] as $member)
              <li><small>{{ $member['name'] }}</small></li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script>
    window.projectAnalyticsData = @json($chartData);
  </script>
  <script src="{{ asset('assets/js/project-analytics.js') }}"></script>
@endpush
