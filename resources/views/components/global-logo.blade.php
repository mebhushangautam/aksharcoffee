@props([
    'setting' => null,
])

@php
    $setting = $setting ?? global_setting();
    $imgClass = $attributes->get('class', 'h-8');
@endphp

@if ($setting)
    <img src="{{ $setting->logo_url }}" alt="{{ $setting->name }}" {{ $attributes->class([$imgClass, 'object-contain shrink-0 dark:hidden']) }} />
    <img src="{{ $setting->dark_logo_url }}" alt="{{ $setting->name }}" {{ $attributes->class([$imgClass, 'object-contain shrink-0 hidden dark:block']) }} />
@endif
