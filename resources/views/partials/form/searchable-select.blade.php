@props([
    'name',
    'label',
    'id' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Search and select...',
    'options' => [],
    'selected' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'emptyOption' => '',
    'wrapperClass' => 'mb-3',
    'dependsOn' => null,
    'filterOn' => null,
    'lookup' => null,
    'multiple' => false,
])

@php
    $fieldId = $id ?? rtrim($name, '[]');
    $fieldName = $name;
    $validationKey = str_replace('[]', '', $name);
    $currentValue = old($validationKey, $selected);
    $selectedValues = collect(is_array($currentValue) ? $currentValue : [$currentValue])
        ->filter(fn ($value) => $value !== null && $value !== '')
        ->map(fn ($value) => (string) $value)
        ->all();
@endphp

<div class="{{ $wrapperClass }}">
    <label class="form-label" for="{{ $fieldId }}">{{ $label }}@if($required) @include('partials.form.required-marker') @endif</label>
    <select
        id="{{ $fieldId }}"
        name="{{ $fieldName }}"
        class="form-select searchable-select @error($validationKey) is-invalid @enderror @error($validationKey.'.*') is-invalid @enderror"
        data-placeholder="{{ $placeholder }}"
        data-empty-option="{{ $emptyOption !== false ? $emptyOption : '' }}"
        @if($lookup) data-lookup="{{ $lookup }}" @endif
        @if($dependsOn) data-depends-on="{{ is_array($dependsOn) ? implode(',', $dependsOn) : $dependsOn }}" @endif
        @if($filterOn) data-filter-on="{{ is_array($filterOn) ? implode(',', $filterOn) : $filterOn }}" @endif
        @if($multiple) multiple @endif
        @if($required && ! $disabled) required @endif
        @if($disabled) disabled data-fixed-company="1" @endif
    >
        @if($emptyOption !== false && ! $multiple)
            <option value="">{{ $emptyOption }}</option>
        @endif
        @foreach($options as $option)
            @php
                $value = is_object($option) ? $option->{$optionValue} : $option[$optionValue];
                $text = is_object($option) ? $option->{$optionLabel} : $option[$optionLabel];
                $isSelected = in_array((string) $value, $selectedValues, true);
            @endphp
            <option value="{{ $value }}" @selected($isSelected)>{{ $text }}</option>
        @endforeach
    </select>
    @error($validationKey)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error($validationKey.'.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
