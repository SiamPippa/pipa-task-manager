@props([
    'companies' => [],
    'selected' => null,
    'required' => true,
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
    ])
@else
    @include('partials.form.searchable-select', [
        'name' => 'company_id',
        'label' => 'Company',
        'required' => false,
        'placeholder' => $placeholder,
        'emptyOption' => false,
        'options' => $lockedCompany ? collect([(object) ['id' => $lockedCompany->id, 'name' => $lockedCompany->name]]) : collect(),
        'selected' => $companyId,
        'wrapperClass' => $wrapperClass,
        'disabled' => true,
    ])
@endif
