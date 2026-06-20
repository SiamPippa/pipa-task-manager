@props([
    'action',
    'fields' => [],
    'filters' => [],
])

@php
    use App\Support\CompanyContext;
@endphp

<div class="card-body border-bottom pb-3">
  <form method="GET" action="{{ $action }}" class="row g-3 align-items-end">
    @if (! CompanyContext::canSelectCompany() && CompanyContext::companyId() && auth()->user()?->company)
      <div class="col-md-2">
        <label class="form-label" for="filter_company_id">Company</label>
        <select
          class="form-select searchable-select"
          id="filter_company_id"
          name="company_id"
          disabled
          data-fixed-company="1">
          <option value="{{ CompanyContext::companyId() }}" selected>{{ auth()->user()->company->name }}</option>
        </select>
      </div>
    @endif
    @foreach ($fields as $field)
      @php
        $name = $field['name'];
        $col = $field['col'] ?? 3;
      @endphp
      <div class="col-md-{{ $col }}">
        <label class="form-label" for="filter_{{ $name }}">{{ $field['label'] }}</label>

        @if ($field['type'] === 'text')
          <input
            type="text"
            class="form-control"
            id="filter_{{ $name }}"
            name="{{ $name }}"
            value="{{ $filters[$name] ?? '' }}"
            placeholder="{{ $field['placeholder'] ?? '' }}">
        @elseif ($field['type'] === 'date')
          <input
            type="date"
            class="form-control"
            id="filter_{{ $name }}"
            name="{{ $name }}"
            value="{{ $filters[$name] ?? '' }}">
        @elseif ($field['type'] === 'select')
          <select
            class="form-select searchable-select"
            id="filter_{{ $name }}"
            name="{{ $name }}"
            data-placeholder="{{ $field['placeholder'] ?? 'All' }}"
            data-empty-option="{{ $field['placeholder'] ?? 'All' }}"
            @if (!empty($field['lookup'])) data-lookup="{{ $field['lookup'] }}" @endif
            @if (!empty($field['dependsOn'])) data-depends-on="{{ is_array($field['dependsOn']) ? implode(',', $field['dependsOn']) : $field['dependsOn'] }}" @endif
            @if (!empty($field['filterOn'])) data-filter-on="{{ is_array($field['filterOn']) ? implode(',', $field['filterOn']) : $field['filterOn'] }}" @endif>
            <option value="">{{ $field['placeholder'] ?? 'All' }}</option>
            @foreach ($field['options'] as $key => $option)
              @if (is_array($option))
                <option value="{{ $option['value'] }}" @selected((string) ($filters[$name] ?? '') === (string) $option['value'])>{{ $option['label'] }}</option>
              @elseif (is_object($option))
                @php
                  $valueKey = $field['optionValue'] ?? 'id';
                  $labelKey = $field['optionLabel'] ?? 'name';
                @endphp
                <option value="{{ $option->{$valueKey} }}" @selected((string) ($filters[$name] ?? '') === (string) $option->{$valueKey})>{{ $option->{$labelKey} }}</option>
              @else
                <option value="{{ $key }}" @selected((string) ($filters[$name] ?? '') === (string) $key)>{{ $option }}</option>
              @endif
            @endforeach
          </select>
        @endif
      </div>
    @endforeach

    <div class="col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-primary">Filter</button>
      <a href="{{ $action }}" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>
</div>

@include('partials.form.searchable-select-assets')
