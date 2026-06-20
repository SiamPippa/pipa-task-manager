@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $designation->company_id ?? null,
])
<div class="mb-3">
  <label class="form-label" for="title">Title</label>
  <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $designation->title ?? '') }}" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $designation->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $designation->status ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="status">Active</label>
</div>

@include('partials.form.searchable-select-assets')
