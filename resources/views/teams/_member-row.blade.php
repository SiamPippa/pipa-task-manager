@php
  $rowIndex = $index;
  $selectedUserId = $member['user_id'] ?? '';
  $isTeamLead = filter_var($member['is_team_lead'] ?? false, FILTER_VALIDATE_BOOLEAN);
  $isActive = filter_var($member['status'] ?? true, FILTER_VALIDATE_BOOLEAN);
@endphp

<tr class="team-member-row" data-index="{{ $rowIndex }}">
  <td>
    <select
      name="members[{{ $rowIndex }}][user_id]"
      class="form-select searchable-select team-member-user @error('members.'.$rowIndex.'.user_id') is-invalid @enderror"
      data-placeholder="Search member..."
      data-empty-option="Select member"
      data-lookup="users"
      data-depends-on="company_id"
      required
    >
      <option value="">Select member</option>
      @foreach ($users as $user)
        <option value="{{ $user->id }}" @selected((string) $selectedUserId === (string) $user->id)>{{ $user->name }}</option>
      @endforeach
    </select>
    @error('members.'.$rowIndex.'.user_id')
      <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
  </td>
  <td class="text-center">
    <input
      type="radio"
      class="form-check-input team-member-lead"
      name="team_lead_member"
      value="{{ $rowIndex }}"
      @checked($isTeamLead)
    >
    <input type="hidden" name="members[{{ $rowIndex }}][is_team_lead]" value="{{ $isTeamLead ? '1' : '0' }}" class="team-member-lead-value">
  </td>
  <td>
    <select name="members[{{ $rowIndex }}][status]" class="form-select team-member-status">
      <option value="1" @selected($isActive)>Active</option>
      <option value="0" @selected(! $isActive)>Inactive</option>
    </select>
  </td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-outline-danger remove-team-member" title="Remove member">&times;</button>
  </td>
</tr>
