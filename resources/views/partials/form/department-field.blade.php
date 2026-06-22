@props([
    'departments' => [],
    'selected' => null,
    'required' => true,
    'wrapperClass' => 'mb-3',
    'placeholder' => 'Search department...',
    'emptyOption' => 'Select department',
    'dependsOn' => 'company_id',
])

@php
    use App\Support\CompanyContext;

    $departmentId = old('department_id', CompanyContext::resolveDepartmentId($selected));
@endphp

@include('partials.form.searchable-select', [
    'name' => 'department_id',
    'label' => 'Department',
    'required' => $required,
    'placeholder' => $placeholder,
    'emptyOption' => $emptyOption,
    'options' => $departments,
    'selected' => $departmentId,
    'wrapperClass' => $wrapperClass,
    'dependsOn' => $dependsOn,
    'lookup' => 'departments',
])
