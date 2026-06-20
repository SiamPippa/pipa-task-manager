@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $department->company_id ?? null,
])
<div class="mb-3">
  <label class="form-label" for="name">Name</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $department->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $department->status ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="status">Active</label>
</div>

@include('partials.form.searchable-select-assets')
