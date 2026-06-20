<div class="mb-3">
  <label class="form-label" for="name">Name</label>
  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name ?? '') }}" required>
  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="code">Code</label>
  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $company->code ?? '') }}" required>
  @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="email">Email</label>
  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->email ?? '') }}">
  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="phone">Phone</label>
  <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone ?? '') }}">
  @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
  <label class="form-label" for="logo">Logo URL</label>
  <input type="text" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" value="{{ old('logo', $company->logo ?? '') }}">
  @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-check mb-3">
  <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $company->status ?? true) ? 'checked' : '' }}>
  <label class="form-check-label" for="status">Active</label>
</div>
