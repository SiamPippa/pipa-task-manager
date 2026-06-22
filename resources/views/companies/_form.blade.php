<div class="mb-3">
  <label class="form-label" for="name">Name @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code @include('partials.form.required-marker')</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $company->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="email">Email</label>
  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email ?? '') }}" placeholder="contact@company.com" inputmode="email" autocomplete="email">
  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="phone">Phone</label>
  <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone ?? '') }}" placeholder="+8801712345678" inputmode="tel" autocomplete="tel">
  <small class="text-muted">Use digits with optional +, spaces, or hyphens.</small>
  @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="logo">Logo</label>
  @if (!empty($company?->logo))
    <div class="mb-2 d-flex align-items-center gap-3">
      @if ($company->logo_url)
        <img src="{{ $company->logo_url }}" alt="{{ $company->name }} logo" class="img-thumbnail" style="max-height: 80px;">
      @endif
      <span class="text-muted small">Current logo uploaded</span>
    </div>
  @endif
  <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
  <small class="text-muted">JPEG, PNG, GIF, or WebP. Max 2MB.@if (!empty($company?->logo)) Leave empty to keep the current logo.@endif</small>
  @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $company->status ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="status">Active</label>
</div>
