{{-- Core AJAX POS: room + pay type; pay select wide enough for full labels + info tooltip. --}}
@php
    $roomBill = $billTo ?? 'POST_TO_ROOM';
    if ($roomBill !== 'POST_TO_ROOM' && $roomBill !== 'PAY_NOW') {
        $roomBill = 'POST_TO_ROOM';
    }
@endphp
<div class="flex flex-nowrap items-center gap-1 w-full min-w-0 text-[11px] sm:text-xs text-gray-700 dark:text-gray-200">
    <span class="sr-only">@lang('modules.order.roomServiceRoomLabel')</span>
    <button type="button" onclick="openHotelRoomModalAjax()"
        class="inline-flex shrink-0 items-center justify-center px-1.5 sm:px-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium h-7 sm:h-8 whitespace-nowrap"
        title="@lang('modules.order.roomServiceChooseRoom')">
        <span class="sm:hidden">@lang('modules.order.roomServicePickShort')</span>
        <span class="hidden sm:inline">@lang('modules.order.roomServiceChooseRoom')</span>
    </button>
    <div class="min-w-0 flex-1 overflow-hidden leading-tight">
        <span id="ajax-room-stay-summary-room" class="font-semibold text-gray-900 dark:text-white truncate block">@lang('modules.order.notSet')</span>
        <span id="ajax-room-stay-summary-stay" class="text-gray-500 dark:text-gray-400 text-[10px] truncate block hidden"></span>
    </div>
    <span class="text-gray-300 dark:text-gray-600 shrink-0 select-none hidden sm:inline" aria-hidden="true">·</span>
    <div class="shrink-0 flex items-center gap-0.5 min-w-0">
        <label for="pos-room-service-bill-to" class="sr-only">@lang('modules.order.roomServiceBillToLabel')</label>
        <x-select id="pos-room-service-bill-to"
            class="!w-[10.75rem] sm:!w-44 h-7 sm:h-8 flex-none text-[11px] sm:text-xs rounded-md border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-800"
            onchange="setRoomServiceBillTo(this.value)"
            title="@lang('modules.order.roomServiceBillToTooltip')">
            <option value="POST_TO_ROOM" @selected($roomBill === 'POST_TO_ROOM')>@lang('modules.order.roomServicePostToRoom')</option>
            <option value="PAY_NOW" @selected($roomBill === 'PAY_NOW')>@lang('modules.order.payNow')</option>
        </x-select>
        <button type="button"
            class="shrink-0 inline-flex items-center justify-center p-0.5 rounded text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
            title="@lang('modules.order.roomServiceBillToTooltip')"
            aria-label="@lang('modules.order.roomServiceBillToHelpAria')"
            data-tooltip-target="tooltip-room-service-bill-to">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
            </svg>
        </button>
        <div id="tooltip-room-service-bill-to" role="tooltip"
            class="absolute z-10 invisible inline-block max-w-xs px-3 py-2 text-xs font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
            @lang('modules.order.roomServiceBillToTooltip')
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>
    </div>
</div>
