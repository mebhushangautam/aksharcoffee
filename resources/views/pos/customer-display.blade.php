@extends('layouts.public')

@push('styles')
<style>
    #main-content { overflow: hidden; height: 100%; }
    #main-content main { height: 100%; }
</style>
@endpush

@section('content')
    @livewire('customer-display')
@endsection 