@props([
    'restaurant' => null,
])

@php
    $restaurant = $restaurant ?? restaurant();
    $imgClass = $attributes->get('class', 'h-7 w-7');
@endphp

@if ($restaurant)
    <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" {{ $attributes->class([$imgClass, 'object-contain shrink-0 dark:hidden']) }} />
    <img src="{{ $restaurant->dark_logo_url }}" alt="{{ $restaurant->name }}" {{ $attributes->class([$imgClass, 'object-contain shrink-0 hidden dark:block']) }} />
@endif
