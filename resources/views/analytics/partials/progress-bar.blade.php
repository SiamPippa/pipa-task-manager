<div class="progress" style="height: 8px;">
  <div
    class="progress-bar {{ ($percent ?? 0) >= 100 ? 'bg-success' : 'bg-primary' }}"
    role="progressbar"
    style="width: {{ min(100, $percent ?? 0) }}%;"
    aria-valuenow="{{ $percent ?? 0 }}"
    aria-valuemin="0"
    aria-valuemax="100">
  </div>
</div>
@if (!empty($showLabel))
  <small class="text-muted">{{ number_format($percent ?? 0, 1) }}% complete</small>
@endif
