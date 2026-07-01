'use strict';

$(function () {
  const $body = $('#team-members-body');
  const $template = $('#team-member-row-template');
  let memberIndex = $body.find('.team-member-row').length;

  function initSelect2($context) {
    $context.find('.searchable-select').each(function () {
      const $select = $(this);

      if ($select.hasClass('select2-hidden-accessible')) {
        return;
      }

      const placeholder = $select.data('placeholder') || 'Search and select...';
      const allowClear = !$select.prop('required');
      const $parent = $select.closest('.card-body');

      $select.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: placeholder,
        allowClear: allowClear,
        dropdownParent: $parent.length ? $parent : $(document.body),
      });
    });
  }

  function reindexRows() {
    $body.find('.team-member-row').each(function (index) {
      const $row = $(this);

      $row.attr('data-index', index);
      $row.find('.team-member-user').attr('name', 'members[' + index + '][user_id]');
      $row.find('.team-member-status').attr('name', 'members[' + index + '][status]');
      $row.find('.team-member-lead-value').attr('name', 'members[' + index + '][is_team_lead]');
      $row.find('.team-member-lead').val(index);
    });

    syncLeadHiddenFields();
  }

  function syncLeadHiddenFields() {
    const $selectedLead = $body.find('.team-member-lead:checked');

    $body.find('.team-member-lead-value').val('0');

    if ($selectedLead.length) {
      $selectedLead.closest('tr').find('.team-member-lead-value').val('1');
    }
  }

  function destroySelect2($row) {
    $row.find('.searchable-select.select2-hidden-accessible').each(function () {
      $(this).select2('destroy');
    });
  }

  $('#add-team-member').on('click', function () {
    const html = $template.html().replace(/__INDEX__/g, String(memberIndex));
    const $row = $(html);

    memberIndex += 1;
    $body.append($row);
    initSelect2($row);
    reindexRows();

    if ($body.find('.team-member-lead:checked').length === 0) {
      $body.find('.team-member-lead').first().prop('checked', true);
      syncLeadHiddenFields();
    }
  });

  $body.on('click', '.remove-team-member', function () {
    const $rows = $body.find('.team-member-row');

    if ($rows.length <= 1) {
      return;
    }

    const $row = $(this).closest('.team-member-row');
    const wasLead = $row.find('.team-member-lead').is(':checked');

    destroySelect2($row);
    $row.remove();
    reindexRows();

    if (wasLead) {
      $body.find('.team-member-lead').first().prop('checked', true);
      syncLeadHiddenFields();
    }
  });

  $body.on('change', '.team-member-lead', function () {
    syncLeadHiddenFields();
  });

  $('form').on('submit', function () {
    syncLeadHiddenFields();
  });

  initSelect2($body);
  syncLeadHiddenFields();
});
