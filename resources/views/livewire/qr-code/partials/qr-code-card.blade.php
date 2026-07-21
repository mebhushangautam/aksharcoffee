{{-- Expects $item (printable array) and optional $tableModel (Table model for status badges). --}}
@php
    $itemId = $item['id'];
    $isTable = ($item['kind'] ?? '') === 'table';
@endphp

<div
    @class([
        'group flex flex-col gap-2 border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600 p-2.5',
        'bg-red-50 dark:bg-red-900/20' => $isTable && ($tableModel->status ?? '') === 'inactive',
        'bg-white' => !$isTable || ($tableModel->status ?? 'active') === 'active',
    ])
    wire:key="qr-card-{{ $itemId }}"
>
    <div class="flex items-center justify-between gap-2">
        <label class="inline-flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer dark:text-gray-300">
            <input
                type="checkbox"
                class="rounded border-gray-300 text-skin-base focus:ring-skin-base dark:border-gray-600 dark:bg-gray-800"
                :checked="isSelected(@js($itemId))"
                @change="toggleSelected(@js($itemId))"
            >
            @lang('app.select')
        </label>
        @if ($isTable && $tableModel)
            <div @class([
                'px-2 py-0.5 rounded text-xs font-semibold',
                'bg-green-100 text-green-700' => $tableModel->available_status === 'available',
                'bg-red-100 text-red-700' => $tableModel->available_status === 'reserved',
                'bg-blue-100 text-blue-700' => $tableModel->available_status === 'running',
            ])>
                {{ $tableModel->table_code }}
            </div>
        @endif
    </div>

    @if ($isTable && $tableModel)
        <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">
            {{ $tableModel->seating_capacity }} @lang('modules.table.seats')
            @if ($tableModel->available_status === 'reserved')
                · @lang('modules.table.reserved')
            @endif
            @if ($tableModel->status === 'inactive')
                · @lang('app.inactive')
            @endif
        </p>
    @else
        <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">{{ $item['subtitle'] ?? '' }}</p>
    @endif

    <button
        type="button"
        class="flex w-full justify-center rounded-md bg-gray-50 p-2 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-750 transition"
        @click="openModal(items.find(i => i.id === @js($itemId)) || @js($item))"
        title="@lang('modules.table.viewQrCode')"
    >
        <img
            src="{{ $item['image_url'] }}"
            alt="{{ $item['label'] }}"
            class="max-h-24 max-w-full object-contain pointer-events-none"
            loading="lazy"
        >
    </button>

    <div class="flex items-center justify-center gap-2 pt-0.5">
        @if ($isTable)
            <x-secondary-button
                wire:click="downloadQrCode('{{ $item['table_code'] }}', '{{ $item['branch_id'] }}')"
                class="text-xs !px-2 !py-1"
                type="button"
                title="@lang('app.download')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </x-secondary-button>
            <x-secondary-link
                target="_blank"
                href="{{ $item['visit_url'] }}"
                class="text-xs !px-2 !py-1"
                title="@lang('app.visitLink')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </x-secondary-link>
            <x-secondary-button
                wire:click="generateQrCode('{{ $item['table_id'] }}')"
                class="text-xs !px-2 !py-1"
                type="button"
                title="@lang('modules.table.generateQrCode')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </x-secondary-button>
            <button
                type="button"
                class="inline-flex items-center justify-center px-2 py-1 text-xs text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                @click="openModal(items.find(i => i.id === @js($itemId)) || @js($item))"
                title="@lang('modules.table.viewQrCode')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                </svg>
            </button>
        @else
            <x-secondary-button wire:click="downloadBranchQrCode" class="text-xs !px-2 !py-1" type="button"
                title="@lang('app.download')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </x-secondary-button>
            <x-secondary-link target="_blank" href="{{ $item['visit_url'] }}" class="text-xs !px-2 !py-1"
                title="@lang('app.visitLink')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </x-secondary-link>
            <x-secondary-button wire:click="generateQrCode" class="text-xs !px-2 !py-1" type="button"
                title="@lang('modules.table.generateQrCode')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </x-secondary-button>
            <button
                type="button"
                class="inline-flex items-center justify-center px-2 py-1 text-xs text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                @click="openModal(items.find(i => i.id === @js($itemId)) || @js($item))"
                title="@lang('modules.table.viewQrCode')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                </svg>
            </button>
        @endif
    </div>
</div>
