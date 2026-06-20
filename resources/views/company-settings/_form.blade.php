@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $companySetting->company_id ?? null,
])
<div class="mb-3">
  <label class="form-label" for="office_start_time">Office Start Time</label>
  <input type="time" class="form-control @error('office_start_time') is-invalid @enderror" id="office_start_time" name="office_start_time" value="{{ old('office_start_time', isset($companySetting) ? \Illuminate\Support\Str::substr($companySetting->office_start_time, 0, 5) : '') }}">
  @error('office_start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="office_end_time">Office End Time</label>
  <input type="time" class="form-control @error('office_end_time') is-invalid @enderror" id="office_end_time" name="office_end_time" value="{{ old('office_end_time', isset($companySetting) ? \Illuminate\Support\Str::substr($companySetting->office_end_time, 0, 5) : '') }}">
  @error('office_end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="working_hours_per_day">Working Hours Per Day</label>
  <input type="number" class="form-control @error('working_hours_per_day') is-invalid @enderror" id="working_hours_per_day" name="working_hours_per_day" value="{{ old('working_hours_per_day', $companySetting->working_hours_per_day ?? 8) }}">
  @error('working_hours_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="allow_manual_time_log" name="allow_manual_time_log" value="1" {{ old('allow_manual_time_log', $companySetting->allow_manual_time_log ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="allow_manual_time_log">Allow Manual Time Log</label>
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="require_daily_report" name="require_daily_report" value="1" {{ old('require_daily_report', $companySetting->require_daily_report ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="require_daily_report">Require Daily Report</label>
</div>

@include('partials.form.searchable-select-assets')
