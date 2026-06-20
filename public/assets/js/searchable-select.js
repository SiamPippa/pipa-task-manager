'use strict';

$(function () {
  $('.searchable-select').each(function () {
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
      multiple: $select.prop('multiple'),
      dropdownParent: $parent.length ? $parent : $(document.body),
    });
  });
});
