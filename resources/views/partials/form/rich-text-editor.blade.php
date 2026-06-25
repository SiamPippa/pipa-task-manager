@props([
    'name' => 'description',
    'id' => null,
    'value' => '',
    'label' => 'Description',
    'required' => false,
])

@php
    $fieldId = $id ?? $name;
    $content = old($name, $value);
@endphp

<div class="mb-3" data-rich-text>
    <label class="form-label" for="{{ $fieldId }}">{{ $label }}@if($required) @include('partials.form.required-marker') @endif</label>
    <div class="rich-text-editor border rounded bg-white @error($name) border-danger @enderror"></div>
    <textarea
        class="d-none @error($name) is-invalid @enderror"
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @if($required) required @endif
    >{{ $content }}</textarea>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
        <style>
            .rich-text-editor .ql-editor {
                min-height: 200px;
            }

            .rich-text-editor .ql-container {
                font-size: 1rem;
            }

            .task-description-content p:last-child {
                margin-bottom: 0;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script src="{{ asset('assets/js/rich-text-editor.js') }}"></script>
    @endpush
@endonce
