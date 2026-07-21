@props(['id' => null, 'maxWidth' => null, 'waiters' => [], 'contentScroll' => true])

@php
$id = $id ?? md5($attributes->wire('model'));

$contentRegionOverflow = $contentScroll
    ? 'overflow-y-auto overflow-x-hidden'
    : 'overflow-x-hidden overflow-hidden';

$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
][$maxWidth ?? '2xl'];
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="jetstream-modal fixed inset-0 overflow-y-auto overflow-x-hidden px-4 py-6 sm:px-0 z-[60]"
    style="display: none;">
    <!-- Overlay -->
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <!-- Sliding Modal: full viewport height (matches customer-list, tables, etc.). z-[60] stacks above POS nav (z-50). -->
    <div x-show="show" class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl transform transition-all fixed top-0 left-0 right-0 w-screen max-w-full sm:left-auto sm:right-0 sm:w-full h-screen max-h-screen {{ $maxWidth }} flex flex-col"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full">
        <div class="flex flex-col flex-1 min-h-0 px-4 pt-3 pb-2 sm:px-6 sm:pt-4">
            <div class="shrink-0 text-base sm:text-lg font-medium text-gray-900 dark:text-gray-100 min-w-0">
                {{ $title }}
            </div>

            @if(isset($content))
                <div class="mt-2 sm:mt-4 flex flex-col flex-1 min-h-0 min-w-0 {{ $contentRegionOverflow }} text-sm text-gray-600 dark:text-gray-400">
                    {{ $content }}
                </div>
            @endif
        </div>

        @if (isset($footer))
        <div class="shrink-0 flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>
