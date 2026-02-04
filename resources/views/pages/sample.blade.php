@extends('layout.master')

@push('plugin-styles')
  <link rel="stylesheet" href="{{ asset('assets/plugins/dragula/dragula.min.css') }}">
@endpush

@section('content')

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/dragula/dragula.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/dragula.js') }}"></script>
@endpush