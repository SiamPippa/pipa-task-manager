@php
  $team = $team ?? null;
  $members = old('members');

  if ($members === null && $team) {
      $members = $team->members->map(fn ($member) => [
          'user_id' => $member->id,
          'is_team_lead' => (bool) $member->pivot->is_team_lead,
          'status' => (bool) ($member->pivot->status ?? true),
      ])->values()->all();
  }

  if (empty($members)) {
      $members = [['user_id' => '', 'is_team_lead' => true, 'status' => true]];
  }
@endphp

@include('partials.form.company-field', [
    'companies' => $companies,
    'selected' => $team?->company_id,
])

<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label" for="name">Name @include('partials.form.required-marker')</label>
      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $team?->name ?? '') }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label class="form-label" for="code">Code</label>
      <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $team?->code ?? '') }}">
      @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
  </div>
  <div class="col-md-3 d-flex align-items-center">
    <div class="form-check mb-3">
      <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $team?->status ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="status">Active</label>
    </div>
  </div>
</div>

<div class="mb-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <label class="form-label mb-0">Team Members @include('partials.form.required-marker')</label>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-team-member">Add Member</button>
  </div>

  @error('members')
    <div class="text-danger small mb-2">{{ $message }}</div>
  @enderror

  <div class="table-responsive">
    <table class="table table-bordered align-middle mb-0" id="team-members-table">
      <thead>
        <tr>
          <th style="width: 45%;">User</th>
          <th style="width: 15%;" class="text-center">Team Lead</th>
          <th style="width: 20%;">Status</th>
          <th style="width: 10%;" class="text-center">Action</th>
        </tr>
      </thead>
      <tbody id="team-members-body">
        @foreach ($members as $index => $member)
          @include('teams._member-row', [
              'index' => $index,
              'member' => $member,
              'users' => $users,
          ])
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<template id="team-member-row-template">
  @include('teams._member-row', [
      'index' => '__INDEX__',
      'member' => ['user_id' => '', 'is_team_lead' => false, 'status' => true],
      'users' => $users,
  ])
</template>

@include('partials.form.searchable-select-assets')

@push('scripts')
  <script src="{{ asset('assets/js/team-form.js') }}"></script>
@endpush
