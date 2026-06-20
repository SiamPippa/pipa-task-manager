<div class="container-xxl flex-grow-1 container-p-y">
  @include('partials.flash')

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Project Analytics</h4>
      <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}. Overview of project health and performance.</p>
    </div>
  </div>

  <div class="row">
    @include('analytics.partials.kpi-card', [
      'label' => 'Total Projects',
      'value' => $kpis['total_projects'],
      'icon' => 'bx-briefcase',
    ])
    @include('analytics.partials.kpi-card', [
      'label' => 'Avg Completion',
      'value' => number_format($kpis['avg_completion_percent'], 1).'%',
      'subtitle' => $kpis['total_done_tasks'].' / '.$kpis['total_tasks'].' tasks done',
      'icon' => 'bx-check-circle',
    ])
    @include('analytics.partials.kpi-card', [
      'label' => 'Logged Hours',
      'value' => number_format($kpis['total_logged_hours'], 1).'h',
      'subtitle' => 'Est. '.number_format($kpis['total_estimated_hours'], 1).'h',
      'icon' => 'bx-time',
    ])
    @include('analytics.partials.kpi-card', [
      'label' => 'At Risk',
      'value' => $kpis['at_risk_count'],
      'subtitle' => 'Projects needing attention',
      'icon' => 'bx-error',
    ])
  </div>

  <div class="row mb-4">
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">Task Status Overview</h5>
        </div>
        <div class="card-body">
          <div id="aggregateTaskStatusChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">Filters</h5>
        </div>
        @include('partials.list-filters', ['action' => route('dashboard'), 'fields' => $filterFields, 'filters' => $filters])
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Projects</h5>
    </div>
    <div class="table-responsive text-nowrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Project</th>
            <th>Status</th>
            <th>Progress</th>
            <th>Tasks</th>
            <th>Hours (Est / Logged)</th>
            <th>Variance</th>
            <th>Health</th>
            <th>Team</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($projects as $item)
          <tr>
            <td>
              <strong>{{ $item['name'] }}</strong>
              <br><small class="text-muted">{{ $item['code'] }} · {{ $item['department_name'] ?? '-' }}</small>
            </td>
            <td><span class="badge {{ $item['display_status_class'] }}">{{ $item['display_status_label'] }}</span></td>
            <td style="min-width: 140px;">
              @include('analytics.partials.progress-bar', ['percent' => $item['completion_percent'], 'showLabel' => true])
            </td>
            <td>
              <small>
                {{ $item['done_tasks'] }}/{{ $item['total_tasks'] }} done<br>
                {{ $item['in_progress_tasks'] }} in progress · {{ $item['blocked_tasks'] }} blocked
              </small>
            </td>
            <td>
              <small>{{ number_format($item['estimated_hours'], 1) }}h / {{ number_format($item['logged_hours'], 1) }}h</small>
            </td>
            <td>
              @php $variance = $item['variance_hours']; @endphp
              <span class="{{ $variance > 0 ? 'text-danger' : 'text-success' }}">
                {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 1) }}h
              </span>
            </td>
            <td><span class="badge {{ $item['schedule_health_class'] }}">{{ $item['schedule_health_label'] }}</span></td>
            <td>
              <small>{{ $item['assigned_members'] }} assigned<br>{{ $item['active_contributors'] }} active</small>
            </td>
            <td>
              <a href="{{ route('analytics.projects.show', $item['id']) }}" class="btn btn-sm btn-outline-primary">Details</a>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center">No projects match the selected filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
