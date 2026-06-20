<div class="col-sm-6 col-lg-3 mb-4">
  <div class="card h-100">
    <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <span class="d-block mb-1 text-muted">{{ $label }}</span>
          <h4 class="card-title mb-0">{{ $value }}</h4>
          @if (!empty($subtitle))
            <small class="text-muted">{{ $subtitle }}</small>
          @endif
        </div>
        @if (!empty($icon))
          <span class="badge bg-label-primary p-2"><i class="bx {{ $icon }}"></i></span>
        @endif
      </div>
    </div>
  </div>
</div>
