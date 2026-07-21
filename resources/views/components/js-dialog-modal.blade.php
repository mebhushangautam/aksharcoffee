{{-- Same layout as x-modal, but visibility is Alpine-only (no Livewire wire:model / @entangle). Open/close via window CustomEvent add-customer-modal-open / add-customer-modal-close. --}}
@props(['id' => null, 'maxWidth' => null, 'maxHeight' => null])

@php
$id = $id ?? 'js-dialog-modal';

$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
][$maxWidth ?? '2xl'];

$maxHeightKey = $maxHeight ?? '2xl';

$maxHeightClasses = [
    'sm' => 'sm:max-h-sm',
    'md' => 'sm:max-h-md',
    'lg' => 'sm:max-h-lg',
    'xl' => 'sm:max-h-xl',
    '2xl' => 'sm:max-h-2xl',
    '3xl' => 'sm:max-h-3xl',
    '4xl' => 'sm:max-h-4xl',
    '5xl' => 'sm:max-h-5xl',
    '6xl' => 'sm:max-h-6xl',
    'full' => 'sm:max-h-full',
    'none' => '',
    'auto' => '',
];

$resolvedMaxHeight = $maxHeightClasses[$maxHeightKey] ?? $maxHeightClasses['2xl'];

$modalPanelOverflow = in_array($maxHeightKey, ['none', 'auto'], true)
    ? 'overflow-visible'
    : 'overflow-hidden overflow-y-auto';

@endphp

<div
    wire:ignore.self
    x-data="{ show: false }"
    x-on:add-customer-modal-open.window="show = true"
    x-on:add-customer-modal-close.window="show = false"
    x-on:keydown.escape.window="if (show) { show = false }"
    x-show="show"
    id="{{ $id }}"
    class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-[70]"
    style="display: none;"
>
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="if (show) { show = false }" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:w-full {{ $maxWidth }} {{ $resolvedMaxHeight }} sm:mx-auto {{ $modalPanelOverflow }} mt-16 sm:mt-20"
                    x-trap.inert.noscroll="show"
                    x-on:click.stop
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 relative z-30">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $title }}
            </div>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                {{ $content }}
            </div>
        </div>

        @if (isset($footer))
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-end relative z-10 shrink-0">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>
