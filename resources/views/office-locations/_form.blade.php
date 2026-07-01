@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $officeLocation?->company_id,
])

<div class="mb-3">
  <label class="form-label" for="name">Name @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $officeLocation?->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label" for="code">Code</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $officeLocation?->code ?? '') }}">
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
  <label class="form-label" for="address">Address</label>
  <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $officeLocation?->address ?? '') }}</textarea>
  @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $officeLocation?->status ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="status">Active</label>
</div>

@include('partials.form.searchable-select-assets')
