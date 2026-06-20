@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2-bootstrap-5-theme.min.css') }}" />
        <style>
            .select2-container--bootstrap-5 .select2-selection {
                min-height: calc(1.53em + 0.875rem + 2px);
                padding: 0.4375rem 0.875rem;
            }

            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
                padding-left: 0;
                line-height: 1.53;
            }

            .select2-container--bootstrap-5.select2-container--focus .select2-selection,
            .select2-container--bootstrap-5.select2-container--open .select2-selection {
                border-color: #696cff;
                box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.25);
            }

            .is-invalid + .select2-container--bootstrap-5 .select2-selection {
                border-color: #ff3e1d;
            }
        </style>
    @endpush

    @push('scripts')
        <meta name="lookup-base-url" content="{{ url('/lookup') }}">
        <script src="{{ asset('assets/vendor/libs/select2/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/searchable-select.js') }}"></script>
        <script src="{{ asset('assets/js/dependent-select.js') }}"></script>
    @endpush
@endonce
