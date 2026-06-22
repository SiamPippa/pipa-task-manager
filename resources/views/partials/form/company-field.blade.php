@props([
    'companies' => [],
    'selected' => null,
    'required' => true,
    'disabled' => false,
    'wrapperClass' => 'mb-3',
    'placeholder' => 'Search company...',
    'emptyOption' => 'Select company',
])

@php
    use App\Support\CompanyContext;

    $user = auth()->user();
    $companyId = old('company_id', CompanyContext::resolveCompanyId($selected, $user));
    $lockedCompany = $user?->company;
@endphp

@if (CompanyContext::canSelectCompany($user))
    @include('partials.form.searchable-select', [
        'name' => 'company_id',
        'label' => 'Company',
        'required' => $required,
        'placeholder' => $placeholder,
        'emptyOption' => $emptyOption,
        'options' => $companies,
        'selected' => $companyId,
        'wrapperClass' => $wrapperClass,
        'disabled' => $disabled,
    ])
@else
    <input type="hidden" name="company_id" value="{{ $companyId }}">
    <div class="{{ $wrapperClass }}">
        <label class="form-label" for="company_id_display">Company</label>
        <input
            type="text"
            class="form-control"
            id="company_id_display"
            value="{{ $lockedCompany?->name ?? '-' }}"
            disabled
            readonly
        >
    </div>
@endif
