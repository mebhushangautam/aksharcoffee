@extends('layouts.guest')

@section('content')

@livewire('shop.orderSuccess', [
    'id' => $id,
    'restaurant' => $restaurant,
    'shopBranch' => $shopBranch,
])

@if (!empty($deferredKotPrintOrderId))
    @livewire('shop.deferred-kot-print', ['orderId' => $deferredKotPrintOrderId], key('shop-deferred-kot-print-' . $deferredKotPrintOrderId))
@endif

@endsection
