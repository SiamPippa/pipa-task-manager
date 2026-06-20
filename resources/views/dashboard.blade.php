@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
  @include('analytics._body')
@endsection

@push('scripts')
  <script>
    window.projectAnalyticsData = @json($chartData);
  </script>
  <script src="{{ asset('assets/js/project-analytics.js') }}"></script>
@endpush
