<div>
    <x-right-modal id="order-detail-drawer" wire:model.live="showOrderDetail">
        <x-slot name="title">
            @if ($order)
                <div class="space-y-1.5 sm:space-y-2">
                <h2 class="grid grid-cols-[minmax(0,1fr)_auto_auto] items-center gap-x-3 text-sm sm:text-base font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    <div class="min-w-0 truncate">
                        {{ $order->show_formatted_order_number }}
                    </div>

                    <div class="inline-flex items-center gap-1.5 justify-self-center whitespace-nowrap">
                        @if ($order->order_type == 'pickup')
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z" />
                            </svg>
                        @elseif($order->order_type == 'delivery')
                            <svg class="w-4 h-4 shrink-0 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" version="1.0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <g transform="translate(0 512) scale(.1 -.1)">
                                    <path d="m2605 4790c-66-13-155-48-213-82-71-42-178-149-220-221-145-242-112-552 79-761 59-64 61-67 38-73-13-4-60-24-104-46-151-75-295-249-381-462-20-49-38-91-39-93-2-2-19 8-40 22s-54 30-74 36c-59 16-947 12-994-4-120-43-181-143-122-201 32-33 76-33 106 0 41 44 72 55 159 55h80v-135c0-131 1-137 25-160l24-25h231 231l24 25c24 23 25 29 25 161v136l95-4c82-3 97-6 117-26l23-23v-349-349l-46-46-930-6-29 30c-17 16-30 34-30 40 0 7 34 11 95 11 88 0 98 2 120 25 16 15 25 36 25 55s-9 40-25 55c-22 23-32 25-120 25h-95v80 80h55c67 0 105 29 105 80 0 19-9 40-25 55l-24 25h-231-231l-24-25c-33-32-33-78 0-110 22-23 32-25 120-25h95v-80-80h-175c-173 0-176 0-200-25-33-32-33-78 0-110 24-25 27-25 197-25h174l12-45c23-88 85-154 171-183 22-8 112-12 253-12h220l-37-43c-103-119-197-418-211-669-7-115-7-116 19-142 26-25 29-26 164-26h138l16-69c55-226 235-407 464-466 77-20 233-20 310 0 228 59 409 240 463 464l17 71h605 606l13-62c58-281 328-498 621-498 349 0 640 291 640 640 0 237-141 465-350 569-89 43-193 71-271 71h-46l-142 331c-78 183-140 333-139 335 2 1 28-4 58-12 80-21 117-18 145 11l25 24v351 351l-26 26c-24 24-30 25-91 20-130-12-265-105-317-217l-23-49-29 30c-16 17-51 43-79 57-49 26-54 27-208 24-186-3-227 9-300 87-43 46-137 173-137 185 0 3 10 6 23 6s48 12 78 28c61 31 112 91 131 155 7 25 25 53 45 70 79 68 91 152 34 242-17 27-36 65-41 85-13 46-13 100 0 100 6 0 22 11 35 25 30 29 33 82 10 190-61 290-332 508-630 504-38-1-88-5-110-9zm230-165c87-23 168-70 230-136 55-57 108-153 121-216l6-31-153-4c-131-3-161-6-201-25-66-30-133-96-165-162-26-52-28-66-31-210l-4-153-31 6c-63 13-159 66-216 121-66 62-113 143-136 230-88 339 241 668 580 580zm293-619c7-41 28-106 48-147l36-74-24-15c-43-28-68-59-68-85 0-40-26-92-54-110-30-20-127-16-211 8l-50 14-3 175c-2 166-1 176 21 218 35 67 86 90 202 90h91l12-74zm-538-496c132-25 214-88 348-269 101-137 165-199 241-237 31-15 57-29 59-30s-6-20-17-43c-12-22-27-75-33-117-12-74-12-76-38-71-149 30-321 156-424 311-53 80-90 95-140 55-48-38-35-89 52-204l30-39-28-36c-42-54-91-145-110-208l-18-57-337-3-338-2 6 82c9 112 47 272 95 400 135 357 365 522 652 468zm1490-630c0-254 1-252-83-167-54 53-77 104-77 167s23 114 77 168c84 84 83 86 83-168zm-454 63c18-13 41-46 57-83l26-61-45-19c-75-33-165-52-244-54l-75-1-3 29c-8 72 44 166 113 201 42 22 132 16 171-12zm-2346-63v-80h-120-120v80 80h120 120v-80zm1584-184c80-52 154-84 261-111l90-23 112-483c68-295 112-506 112-540 1-68-21-134-56-171l-26-27-17 48c-29 86-99 159-177 186l-38 13-6 279c-5 297-5 297-64 414-58 113-212 233-328 254-21 4-41 14-44 21-12 32 88 201 111 186 6-4 37-24 70-46zm1099-493 185-433-348-490h-138-138l33 68c40 81 56 176 44 252-8 47-203 894-217 941-4 13 9 17 75 23 80 6 230 44 280 71 14 7 29 10 32 7 4-4 90-202 192-439zm-1323 187c118-22 229-99 275-190 37-74 45-138 45-375v-225h-160-160v115c0 179-47 289-158 369-91 67-141 76-417 76h-244l10 32c5 18 9 72 9 120v88h374c209 0 397-4 426-10zm-319-402c50-15 111-67 135-115 16-32 20-70 24-244l5-205 36-72 35-72h-759-759l7 63c17 164 95 400 165 502 47 68 129 124 215 145 52 13 853 12 896-2zm2114-323c256-67 415-329 350-580-48-184-202-326-390-358-197-34-412 76-500 257-19 39-38 86-41 104l-6 32h80 81l24-53c31-69 86-123 156-156 77-36 192-36 266-1 63 31 124 91 156 155 33 68 34 197 2 267-27 60-95 127-156 157-95 46-229 36-311-22-18-12-26-15-21-6 13 22 126 182 143 202 19 22 86 23 167 2zm-1315-243c39-21 87-99 77-125-6-15-27-17-178-17-193 0-231 7-289 58-35 29-70 78-70 97 0 3 96 5 213 5 187 0 217-2 247-18zm1288-89c51-38 67-70 67-133s-16-95-69-134c-43-33-132-29-179 7-20 15-37 32-37 38 0 5 36 9 80 9 73 0 83 3 105 25 33 32 33 78 0 110-22 22-32 25-105 25-44 0-80 4-80 8 0 12 29 37 65 57 39 21 117 15 153-12zm-397-46c-10-9-11-8-5 6 3 10 9 15 12 12s0-11-7-18zm-2460-217c45-106 169-184 289-184s244 78 289 184l22 50h81 81l-7-32c-13-65-66-159-123-219-186-195-500-195-686 0-57 60-110 154-123 219l-6 32h80 81l22-50zm419 41c0-16-51-50-91-63-30-8-48-8-78 0-40 13-91 47-91 63 0 5 57 9 130 9s130-4 130-9z" />
                                </g>
                            </svg>
                        @else
                            <svg class="w-4 h-4 shrink-0 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" version="1.0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <g transform="translate(0 512) scale(.1 -.1)">
                                    <path d="m249 4691c-19-20-29-40-29-60 0-16-14-243-31-503s-28-495-25-522 19-77 35-111c46-100 126-167 236-200l40-12 3-400 2-401-25-6c-58-15-56 21-53-867l3-814 23-45c35-72 75-114 144-151 58-31 70-34 148-34s90 3 148 34c70 38 100 69 140 145l27 51 5 293 5 294 52-64c380-466 1036-731 1654-667 645 65 1211 449 1511 1024l57 110 3-495c3-491 3-495 26-540 35-72 75-114 144-151 58-31 70-34 148-34s90 3 148 34c70 38 100 69 140 145l27 51 3 1938 2 1938-52 52-161-5c-184-6-260-25-384-93-90-50-218-178-268-268-66-120-87-202-93-370l-5-148-86 84c-469 455-1109 646-1736 517-295-61-612-212-835-399l-75-63-6 53c-4 30-15 182-24 339-12 208-21 291-32 308-31 50-98 53-130 6-15-24-15-48 6-387 12-199 24-383 27-409 5-41 3-48-19-62-28-19-159-52-234-60l-53-5v455 455l-25 24c-15 16-36 25-55 25s-40-9-55-25l-25-24v-456-457l-27 6c-16 3-53 8-83 12-69 8-174 40-188 57-7 8-3 125 14 382 30 467 30 450-1 480-33 33-70 32-106-4zm4551-1171v-1040h-320-320v783c0 512 4 804 11 843 29 162 151 321 303 394 91 44 149 57 254 59l72 1v-1040zm-1955 776c271-49 475-131 701-282 126-83 292-236 390-358l64-80v-604-603l25-24c23-24 30-25 150-25 101 0 125-3 125-14 0-34-33-179-60-269-90-288-240-529-465-745-443-426-1063-587-1665-432-403 103-777 372-1019 732l-51 76v382 381l-25 24c-13 14-31 25-40 25-14 0-15 44-13 401l3 402 40 12c111 33 189 100 238 203 29 60 32 77 34 166l1 98 49 50c243 250 626 440 978 487 44 6 94 13 110 15 60 9 352-3 430-18zm-2470-652c200-61 554-55 731 13 15 6 16 1 10-38-9-57-46-112-98-146l-42-28h-256-256l-42 28c-52 34-89 89-98 145-4 23-5 42-3 42s27-7 54-16zm425-764v-400h-80-80v400 400h80 80v-400zm78-1309c-3-739-3-750-24-777-39-53-71-69-134-69s-95 16-134 69c-21 27-21 38-24 777l-2 749h160 160l-2-749zm3920 0c-3-739-3-750-24-777-39-53-71-69-134-69s-95 16-134 69c-21 27-21 38-24 777l-2 749h160 160l-2-749z" />
                                    <path d="m2420 3834c-293-38-560-167-763-371-476-475-502-1239-60-1743 495-563 1356-588 1875-52 196 202 313 436 352 703 60 408-69 797-363 1090-182 182-382 293-631 350-83 19-331 33-410 23zm315-169c467-75 826-424 927-900 16-77 16-333 0-410-98-461-436-799-897-897-77-16-333-16-410 0-348 74-626 281-783 580-173 331-175 697-7 1032 214 427 696 672 1170 595z" />
                                </g>
                            </svg>
                        @endif
                        <span class="text-sm font-medium">{{ Str::title($order->orderType?->order_type_name ?? $order->custom_order_type_name ?? $order->order_type) }}</span>
                    </div>

                    <div class="text-right whitespace-nowrap">
                        @php
                            $tokenNumber = $order->kot->whereNotNull('token_number')->first()?->token_number;
                        @endphp
                        <div class="text-[11px] text-gray-600 dark:text-gray-400">
                            @if ($tokenNumber)
                                @lang('modules.order.tokenNumber') {{ $tokenNumber }} •
                            @endif
                            @if($order->order_type == 'pickup')
                                @lang('modules.order.pickupDate')
                                {{ \Carbon\Carbon::parse($order->pickup_date)->format(dateFormat() . ' ' . timeFormat()) }}
                            @else
                                {{ $order->date_time->timezone(timezone())->format(dateFormat() . ' ' . timeFormat()) }}
                            @endif
                        </div>
                    </div>
                </h2>
                <div class="flex flex-wrap items-start justify-between gap-x-2 gap-y-1.5 sm:gap-y-2 lg:flex-nowrap lg:items-center">
                    <div class="min-w-0 flex-1 flex flex-wrap items-center gap-x-3 gap-y-2">
                        <div class="min-w-0">
                            <div class="flex flex-wrap md:flex-nowrap items-center gap-x-2 gap-y-2">
                                @if ($order->order_type == 'dine_in')
                                    @if (!is_null($order->table))
                                        <div class="inline-flex items-center gap-1.5 shrink-0">
                                            <div
                                                @if(user_can('Update Order'))
                                                    wire:click="openTableChangeConfirmation"
                                                @endif
                                                @class([
                                                    'py-1.5 px-2.5 cursor-pointer rounded-md tracking-wide bg-skin-base/[0.2] text-skin-base text-sm',
                                                    'cursor-not-allowed' => !user_can('Update Order'),
                                            ])>
                                                <h3 wire:loading.class.delay='opacity-50' @class(['text-sm font-semibold leading-none'])>
                                                    {{ $order->table->table_code ?? '--' }}
                                                </h3>
                                            </div>
                                            @if(user_can('Update Order'))
                                                <span class="relative inline-flex shrink-0">
                                                    <button type="button" wire:click="openTableChangeConfirmation"
                                                        class="inline-flex items-center justify-center p-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shrink-0"
                                                        title="{{ __('modules.order.changeTable') }}"
                                                        aria-label="{{ __('modules.order.changeTable') }}"
                                                        data-tooltip-target="tooltip-od-detail-change-table">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16" aria-hidden="true">
                                                            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                                                            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                                                        </svg>
                                                    </button>
                                                    <div id="tooltip-od-detail-change-table" role="tooltip"
                                                        class="hidden pointer-events-none whitespace-nowrap px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700">
                                                        {{ __('modules.order.changeTable') }}
                                                    </div>
                                                </span>
                                            @endif
                                        </div>
                                    @elseif(user_can('Update Order'))
                                        <span class="relative inline-flex shrink-0">
                                            <button type="button" wire:click="openTableChangeConfirmation"
                                                class="inline-flex items-center justify-center p-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shrink-0"
                                                title="{{ __('modules.order.setTable') }}"
                                                aria-label="{{ __('modules.order.setTable') }}"
                                                data-tooltip-target="tooltip-od-detail-set-table">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-table" viewBox="0 0 16 16" aria-hidden="true"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/></svg>
                                            </button>
                                            <div id="tooltip-od-detail-set-table" role="tooltip"
                                                class="hidden pointer-events-none whitespace-nowrap px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700">
                                                {{ __('modules.order.setTable') }}
                                            </div>
                                        </span>
                                    @endif
                                @endif
                                <div class="inline-flex items-center gap-1.5 min-w-0 max-w-full">
                                    @if ($order->customer_id)
                                        @php
                                            $customerDisplayName = $order->customer
                                                ? ($order->customer->name ?: __('modules.customer.walkin'))
                                                : '--';
                                        @endphp
                                        <div
                                            class="min-w-0 max-w-[10rem] sm:max-w-[14rem] text-sm font-semibold text-gray-700 dark:text-gray-300 truncate"
                                            title="{{ $customerDisplayName }}"
                                        >{{ $customerDisplayName }}</div>
                                        @if(user_can('Update Order'))
                                            <span class="relative inline-flex shrink-0">
                                                <button type="button" onclick="window.showAddCustomerModal({{ json_encode($order->customer_id) }}, {{ json_encode($order->id) }}, false)"
                                                    class="inline-flex items-center justify-center p-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shrink-0"
                                                    title="{{ __('modules.order.updateCustomerDetails') }}"
                                                    aria-label="{{ __('modules.order.updateCustomerDetails') }}"
                                                    data-tooltip-target="tooltip-od-detail-edit-customer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16" aria-hidden="true">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg>
                                                </button>
                                                <div id="tooltip-od-detail-edit-customer" role="tooltip"
                                                    class="hidden pointer-events-none whitespace-nowrap px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700">
                                                    {{ __('modules.order.updateCustomerDetails') }}
                                                </div>
                                            </span>
                                        @endif
                                    @elseif(user_can('Update Order'))
                                        <span class="relative inline-flex shrink-0">
                                            <button type="button"
                                                onclick="window.showAddCustomerModal(null, {{ json_encode($order->id) }}, false); return false;"
                                                class="inline-flex items-center justify-center p-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shrink-0"
                                                title="{{ __('modules.order.addCustomerDetails') }}"
                                                aria-label="{{ __('modules.order.addCustomerDetails') }}"
                                                data-tooltip-target="tooltip-od-detail-add-customer">
                                                <svg width="16" height="16" viewBox="-2.5 0 32 32" fill="currentColor" class="text-gray-700 dark:text-gray-300" aria-hidden="true">
                                                    <path d="M18.723 21.788c-1.15-0.48-3.884-1.423-5.565-1.919-0.143-0.045-0.166-0.052-0.166-0.649 0-0.493 0.203-0.989 0.401-1.409 0.214-0.456 0.468-1.224 0.559-1.912 0.255-0.296 0.602-0.88 0.826-1.993 0.196-0.981 0.104-1.338-0.026-1.673-0.013-0.035-0.028-0.070-0.038-0.105-0.049-0.23 0.018-1.425 0.186-2.352 0.116-0.636-0.030-1.989-0.906-3.108-0.553-0.707-1.611-1.576-3.544-1.696l-1.060 0.001c-1.9 0.12-2.96 0.988-3.513 1.695-0.876 1.119-1.021 2.472-0.906 3.108 0.169 0.928 0.236 2.123 0.187 2.348-0.010 0.039-0.025 0.074-0.039 0.11-0.129 0.335-0.221 0.692-0.025 1.673 0.222 1.113 0.57 1.697 0.826 1.993 0.090 0.688 0.344 1.456 0.559 1.912 0.157 0.334 0.23 0.788 0.23 1.431 0 0.597-0.023 0.604-0.157 0.646-1.738 0.513-4.505 1.513-5.537 1.965-0.818 0.351-1.017 0.98-1.017 1.548s0 2.251 0 2.623c0 0.371 0.22 1.006 1.017 1.006 0.613 0 5.518 0 7.746 0 0.668 0 1.098 0 1.098 0h0.192c0 0 0.437 0 1.115 0 2.237 0 7.135 0 7.747 0 0.796 0 1.017-0.634 1.017-1.006s0-2.055 0-2.623-0.392-1.262-1.209-1.613zM18.876 25.98h-17.827v-2.579c0-0.318 0.092-0.46 0.388-0.587 0.994-0.435 3.741-1.426 5.434-1.926 0.889-0.282 0.889-1.070 0.889-1.646 0-0.801-0.106-1.397-0.331-1.878-0.172-0.366-0.392-1.022-0.468-1.601l-0.041-0.312-0.206-0.238c-0.113-0.13-0.396-0.538-0.59-1.513-0.153-0.759-0.085-0.935-0.031-1.076 0.031-0.076 0.058-0.152 0.081-0.237l0.005-0.022 0.005-0.022c0.105-0.495-0.037-1.962-0.181-2.755-0.067-0.365 0.017-1.401 0.7-2.273 0.418-0.534 1.229-1.19 2.722-1.293l0.992-0.001c1.219 0.083 2.145 0.518 2.752 1.294 0.682 0.872 0.766 1.909 0.7 2.275-0.148 0.814-0.287 2.257-0.18 2.758l0.008 0.039 0.011 0.038c0.016 0.054 0.036 0.108 0.056 0.161l0.009 0.026 0.001 0.002c0.059 0.153 0.127 0.326-0.024 1.087-0.196 0.974-0.479 1.384-0.592 1.515l-0.204 0.237-0.042 0.31c-0.076 0.578-0.296 1.237-0.468 1.603-0.247 0.525-0.5 1.157-0.5 1.856 0 0.577 0 1.367 0.918 1.655 1.641 0.485 4.345 1.416 5.448 1.877 0.418 0.179 0.574 0.493 0.574 0.649l-0.006 2.579z"/>
                                                    <path d="M23.078 14.441v-4.185h-1.049v4.185h-4.186v1.049h4.186v4.185h1.049v-4.185h4.185v-1.049z"/>
                                                </svg>
                                            </button>
                                            <div id="tooltip-od-detail-add-customer" role="tooltip"
                                                class="hidden pointer-events-none whitespace-nowrap px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700">
                                                {{ __('modules.order.addCustomerDetails') }}
                                            </div>
                                        </span>
                                    @endif
                                </div>
                                @if ($order->order_type == 'dine_in')
                                    @if (!user()->hasRole('Waiter_' . user()->restaurant_id) && user_can('Update Order'))
                                        <div class="shrink-0 min-w-0">
                                            <x-select class="text-xs py-1 w-[min(100%,11rem)] min-w-[7.5rem] max-w-[11rem] sm:w-36 rounded-md" wire:model.live='selectWaiter'>
                                                <option value="">@lang('modules.order.selectWaiter')</option>
                                                @foreach ($users as $item)
                                                    <option value="{{ $item->id }}" {{ ($selectWaiter && $selectWaiter == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                                @endforeach
                                            </x-select>
                                        </div>
                                    @elseif ($selectWaiter || $order->waiter_id)
                                        @php
                                            $waiterDisplayId = $selectWaiter ?: $order->waiter_id;
                                            $waiterDisplayName = $order->waiter?->name
                                                ?? optional($users->firstWhere('id', (int) $waiterDisplayId))->name;
                                        @endphp
                                        @if ($waiterDisplayName)
                                            <div class="shrink-0 flex items-center gap-1 py-0.5 text-xs dark:text-gray-300 whitespace-nowrap">
                                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3m0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22" />
                                                </svg>
                                                <span>@lang('modules.order.waiter'):</span>
                                                <span class="font-medium truncate max-w-[8rem]">{{ $waiterDisplayName }}</span>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                            @if ($order->order_type == 'delivery')
                                <div class="space-y-3">
                                    @if ($order->deliveryPlatform)
                                        <div class="inline-flex flex-col gap-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            <div class="inline-flex items-center gap-2">
                                                @if ($order->deliveryPlatform->logo_url)
                                                    <img src="{{ $order->deliveryPlatform->logo_url }}" alt="{{ $order->deliveryPlatform->name }}" class="w-5 h-5 rounded">
                                                @endif
                                                <span class="text-sm">{{ $order->deliveryPlatform->name }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($order->deliveryExecutive)
                                        <div class="inline-flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-700 dark:text-gray-300">
                                            <span class="font-medium text-gray-500 dark:text-gray-400">@lang('modules.order.deliveryExecutive'):</span>
                                            <span class="font-semibold truncate max-w-[10rem] sm:max-w-[14rem]">{{ $order->deliveryExecutive->name }}</span>
                                            <span @class([
                                                'inline-flex shrink-0 rounded-full px-2 py-0.5 text-[11px] font-medium',
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' => (bool) $order->deliveryExecutive->is_online,
                                                'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' => !(bool) $order->deliveryExecutive->is_online,
                                            ])>
                                                {{ (bool) $order->deliveryExecutive->is_online ? __('app.online') : __('app.offline') }}
                                            </span>
                                        </div>
                                    @else
                                        <x-select class="w-full text-sm" wire:model.live='deliveryExecutive'
                                            wire:change='saveDeliveryExecutive'>
                                            <option value="">@lang('modules.order.selectDeliveryExecutive')</option>
                                            @foreach ($deliveryExecutives as $deliveryExecutive)
                                                <option value="{{ $deliveryExecutive->id }}">{{ $deliveryExecutive->name }}
                                                </option>
                                            @endforeach
                                        </x-select>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            @lang('modules.order.onlyOnlineDeliveryExecutivesShown')
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-row flex-wrap items-center gap-1.5 justify-end shrink-0">
                        <span @class([
                            'text-[10px] font-medium px-1.5 py-0.5 rounded uppercase tracking-wide whitespace-nowrap leading-tight',
                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 border border-gray-400' =>
                                $order->status == 'draft',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-400 border border-yellow-400' =>
                                $order->status == 'kot',
                            'bg-blue-100 text-blue-800 dark:bg-gray-700 dark:text-blue-400 border border-blue-400' =>
                                $order->status == 'billed' || $order->status == 'out_for_delivery',
                            'bg-green-100 text-green-800 dark:bg-gray-700 dark:text-green-400 border border-green-400' =>
                                $order->status == 'paid' || $order->status == 'delivered',
                            'bg-red-100 text-red-800 dark:bg-gray-700 dark:text-red-400 border border-red-400' =>
                                $order->status == 'canceled' || $order->status == 'payment_due',
                            'bg-orange-100 text-orange-800 dark:bg-gray-700 dark:text-orange-400 border border-orange-400' =>
                                $order->status == 'pending_verification',
                        ])>
                            @lang('modules.order.' . $order->status)
                        </span>

                        @if($order->placed_via)
                            <span @class([
                                'text-[10px] font-medium px-1.5 py-0.5 rounded uppercase tracking-wide whitespace-nowrap inline-flex items-center leading-tight',
                                'bg-indigo-100 text-indigo-800 dark:bg-indigo-700 dark:text-indigo-400 border border-indigo-400' => $order->placed_via === 'pos',
                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-700 dark:text-emerald-400 border border-emerald-400' => $order->placed_via === 'shop',
                                'bg-amber-100 text-amber-800 dark:bg-amber-700 dark:text-amber-400 border border-amber-400' => $order->placed_via === 'kiosk',
                            ])>
                                @if($order->placed_via === 'pos')
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($order->placed_via === 'shop')
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM8 15a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($order->placed_via === 'kiosk')
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 7.707 7.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                {{ __('app.via_' . $order->placed_via) }} {{ module_enabled('Kiosk') && class_exists(\Modules\Kiosk\Entities\Kiosk::class) && ($order->kiosk) ? ' : ' . $order->kiosk->code : '' }}
                            </span>
                        @endif
                    </div>

                </div>
                @if (function_exists('module_enabled') && module_enabled('Hotel') && in_array('Hotel', restaurant_modules()) && $order->order_type === 'room_service' && $order->context_type === 'HOTEL_ROOM' && $order->hotelStay)
                    @php $hotelStay = $order->hotelStay; @endphp
                    <div class="flex flex-wrap gap-2 mb-2 sm:mb-3 p-2 sm:p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800 mt-2 sm:mt-3">
                        @if ($hotelStay->room)
                            <div class="flex items-center gap-1.5 text-xs">
                                <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.folio.room'):</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $hotelStay->room->room_number }}</span>
                            </div>
                            <span class="text-gray-300 dark:text-gray-600 text-xs self-center">|</span>
                        @endif
                        <div class="flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.folio.stay'):</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $hotelStay->stay_number }}</span>
                        </div>
                        @if ($hotelStay->stayGuests && $hotelStay->stayGuests->isNotEmpty() && $hotelStay->stayGuests->first()?->guest)
                            <span class="text-gray-300 dark:text-gray-600 text-xs self-center">|</span>
                            <div class="flex items-center gap-1.5 text-xs">
                                <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.guest.guest'):</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $hotelStay->stayGuests->first()->guest->full_name }}</span>
                            </div>
                        @endif
                        @if ($order->bill_to)
                            <span class="text-gray-300 dark:text-gray-600 text-xs self-center">|</span>
                            <div class="flex items-center gap-1.5 text-xs">
                                <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.roomService.billTo'):</span>
                                <span @class([
                                    'font-semibold',
                                    'text-orange-600 dark:text-orange-400' => $order->bill_to === 'POST_TO_ROOM',
                                    'text-green-600 dark:text-green-400' => $order->bill_to !== 'POST_TO_ROOM',
                                ])>
                                    {{ $order->bill_to === 'POST_TO_ROOM' ? __('hotel::modules.roomService.postToRoom') : __('hotel::modules.roomService.payNow') }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($orderProgressStatus === 'cancelled')
                    <div class="p-3 my-2 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/20 dark:border-red-800">
                        <div class="flex items-start gap-3">
                            <svg class="flex-shrink-0 mt-0.5 w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-red-800 dark:text-red-200">
                                    @lang('modules.order.info_cancelled')
                                </h4>
                                @if ($order->cancelReason || $order->cancel_reason_text)
                                    <div class="mt-3 space-y-2">
                                        @if ($order->cancelReason)
                                            <div>
                                                <span class="text-xs font-medium text-red-700 dark:text-red-300">@lang('modules.settings.kotCancelReasons'):</span>
                                                <span class="text-sm text-red-600 dark:text-red-400">{{ $order->cancelReason->reason }}</span>
                                            </div>
                                        @endif

                                        @if ($order->cancel_reason_text)
                                            <div>
                                                <span class="text-xs font-medium text-red-700 dark:text-red-300">@lang('modules.settings.enterCancelReason'):</span>
                                                <div class="p-2 mt-1 text-sm text-red-600 bg-white border border-red-200 rounded dark:text-red-400 dark:bg-gray-800 dark:border-red-700">
                                                    {{ $order->cancel_reason_text }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            @endif
        </x-slot>

        <x-slot name="content">
            @if ($order)
                <div class="flex flex-col flex-1 min-h-0 w-full min-w-0">
                <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden -mx-2 px-2 pb-2 space-y-3">
                    {{-- Order status + KOT live in the scrollable body so small screens keep room for line items (modal title is shrink-0). --}}
                    @if ($orderProgressStatus !== 'cancelled')
                        <div class="mb-0 rounded-lg border border-gray-200 bg-white py-2 px-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-800 shrink-0">
                            @php
                                $statuses = \App\Enums\OrderStatus::progressStepsFor($order->order_type);
                                $currentIndex = \App\Enums\OrderStatus::progressIndex($orderProgressStatus, $order->order_type);
                                $nextIndex = min($currentIndex + 1, count($statuses) - 1);
                                $progressStatusValue = \App\Enums\OrderStatus::progressStatusForOrderType($orderProgressStatus, $order->order_type);
                                $progressStatus = \App\Enums\OrderStatus::from($progressStatusValue);
                                $progressPercent = count($statuses) > 1
                                    ? ($currentIndex / (count($statuses) - 1)) * 100
                                    : 0;
                            @endphp

                            <div class="flex flex-col gap-2.5">
                                {{-- Header: title (left) · status badge (center) · actions (right) --}}
                                <div class="flex items-center gap-2 min-w-0 w-full">
                                    <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wide shrink-0">
                                        {{ __('modules.order.orderStatus') }}
                                    </h3>
                                    <div class="flex flex-1 justify-center min-w-0 px-1">
                                        <span @class([
                                            'inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full border leading-snug shrink-0 max-w-full',
                                            $progressStatus->badgeClasses(),
                                        ])>
                                            <span class="truncate">{{ $progressStatus->translatedLabel() }}</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-end gap-1.5 shrink-0 max-w-[50%] sm:max-w-[45%]">
                                        @if(user_can('Update Order'))
                                            @if($orderProgressStatus === 'placed')
                                                <x-danger-button class="inline-flex items-center !gap-1 !px-2 !py-1 !text-[11px] !font-medium shrink-0" wire:click="$toggle('confirmDeleteModal')">
                                                    <span class="whitespace-nowrap">{{ __('modules.order.cancelOrder') }}</span>
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </x-danger-button>
                                            @endif
                                            @if($currentIndex < count($statuses) - 1)
                                                <x-secondary-button class="inline-flex items-center !gap-1 !px-2 !py-1 !text-[11px] !font-medium border-gray-300 shadow-sm dark:border-gray-600 shrink-0 max-w-full" wire:click="$set('orderProgressStatus', '{{ $statuses[$nextIndex] }}')">
                                                    <span class="truncate">{{ __('modules.order.moveTo') }} {{ App\Enums\OrderStatus::from($statuses[$nextIndex])->translatedLabel() }}</span>
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                    </svg>
                                                </x-secondary-button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                {{-- Progress steps: equal width, no scroll. overflow-visible so portaled tooltips are not clipped. --}}
                                <div class="relative w-full pt-1 pb-0.5 overflow-visible">
                                    <div class="absolute top-1/2 left-[2%] right-[2%] z-0 h-0.5 -translate-y-1/2 bg-gray-200 dark:bg-gray-600">
                                        <div class="h-full bg-skin-base/60 dark:bg-skin-base/50 transition-all duration-500 rounded-full" style="width: {{ $progressPercent }}%;"></div>
                                    </div>

                                    <div class="relative z-10 flex w-full items-center justify-between">
                                        @foreach ($statuses as $index => $status)
                                            @php
                                                $isCompleted = $index < $currentIndex;
                                                $isCurrent = $index === $currentIndex;
                                                $isNext = $index === $currentIndex + 1;
                                            @endphp
                                            @php
                                                $stepStatus = App\Enums\OrderStatus::from($status);
                                                $statusStepTooltipId = 'tooltip-od-detail-status-' . $order->id . '-' . $index;
                                                $statusStepLabel = $stepStatus->translatedLabel();
                                            @endphp
                                            <div class="flex flex-1 justify-center px-0.5 min-w-0">
                                                <div
                                                    class="relative inline-flex flex-col items-center cursor-default"
                                                    data-pos-status-tooltip="{{ $statusStepTooltipId }}"
                                                    title="{{ $statusStepLabel }}"
                                                    tabindex="0"
                                                    role="img"
                                                    aria-label="{{ $statusStepLabel }}"
                                                >
                                                    <div
                                                        @class([
                                                            'order-status-icon flex size-8 shrink-0 items-center justify-center rounded-full relative z-10 bg-white shadow-sm dark:bg-gray-900 [&_.order-status-step-svg]:text-current',
                                                            $isCurrent ? 'overflow-visible order-status-step-is-current' : 'overflow-hidden',
                                                            $stepStatus->progressStepIconClasses($isCompleted, $isCurrent, $isNext),
                                                        ])
                                                        data-classes-current="{{ $stepStatus->progressStepIconClasses(false, true, false) }}"
                                                        data-classes-completed="{{ $stepStatus->progressStepIconClasses(true, false, false) }}"
                                                        data-classes-next="{{ $stepStatus->progressStepIconClasses(false, false, true) }}"
                                                        data-classes-pending="{{ $stepStatus->progressStepIconClasses(false, false, false) }}"
                                                    >
                                                        {!! $stepStatus->icon() !!}
                                                    </div>
                                                </div>
                                                <div id="{{ $statusStepTooltipId }}" role="tooltip"
                                                    class="hidden pointer-events-none px-2 py-1 text-[10px] font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700 whitespace-nowrap">
                                                    {{ $statusStepLabel }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($showKotAlert)
                        <div class="py-1.5 px-2 sm:py-2 sm:px-2.5 border-l-[3px] sm:border-l-4 rounded-r-md sm:rounded-r-lg bg-orange-50 border-orange-400 dark:bg-orange-800/20 dark:border-orange-700/30 shrink-0">
                            <div class="flex items-start gap-1.5 sm:gap-2 min-w-0">
                                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 shrink-0 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98zM11 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1-8a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1"/></svg>
                                <div class="min-w-0 flex-1 pr-0.5">
                                    <span class="text-xs font-semibold text-orange-800 dark:text-orange-200 leading-snug block">
                                        @lang('modules.order.kotNotCreated')
                                    </span>
                                    <p class="text-[11px] sm:text-xs text-orange-600 dark:text-orange-400 leading-snug mt-0.5 line-clamp-2 sm:line-clamp-none">
                                        @lang('modules.order.kotNotCreatedMessage')
                                    </p>
                                </div>
                                <x-button wire:click="createKot" wire:loading.attr="disabled" wire:target="createKot" class="shrink-0 self-center !py-1 !px-2 sm:!py-1.5 sm:!px-3 !text-[11px] sm:!text-xs !font-medium leading-tight max-w-[9.5rem] sm:max-w-none">
                                    <svg wire:loading wire:target="createKot" class="inline animate-spin -ml-0.5 mr-0.5 h-3.5 w-3.5 text-white sm:h-4 sm:w-4 sm:-ml-1 sm:mr-1 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12zm2 5.291A7.96 7.96 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938z"/></svg>
                                    <svg wire:loading.remove wire:target="createKot" xmlns="http://www.w3.org/2000/svg" class="inline w-3 h-3 shrink-0 ltr:mr-0.5 rtl:ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span class="min-w-0 truncate text-left sm:text-center">@lang('modules.order.createKot')</span>
                                </x-button>
                            </div>
                        </div>
                    @endif

                <div class="flex flex-col rounded min-w-0">
                    <table class="flex-1 min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-700 shadow-sm">
                            <tr>
                                <th scope="col"
                                    class="p-2 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 rtl:text-right ltr:text-left">
                                    @lang('modules.menu.itemName')
                                </th>
                                <th scope="col"
                                    class="p-2 text-xs text-center text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.order.qty')
                                </th>
                                <th scope="col"
                                    class="hidden p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400 lg:table-cell">
                                    @lang('modules.order.price')
                                </th>
                                <th scope="col"
                                    class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.order.amount')
                                </th>

                                @if (!in_array($order->status, ['paid', 'payment_due', 'canceled']) && user_can('Delete Order') && !$usingKotItemsFallback)
                                    <th scope="col"
                                        class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                        @lang('app.action')
                                    </th>
                                @endif

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            wire:key='menu-item-list-{{ microtime() }}'>

                            @forelse ($order->items as $key => $item)
                                @php
                                    $orderTypeId  = $order->order_type_id;
                                    $selectedDeliveryApp = $order?->delivery_app_id;
                                    if ($orderTypeId) {
                                        $item->menuItem->setPriceContext($orderTypeId, $selectedDeliveryApp);
                                        if ($item->menuItemVariation) {
                                            $item->menuItemVariation->setPriceContext($orderTypeId, $selectedDeliveryApp);
                                        }
                                        foreach ($item->modifierOptions as $modifier) {
                                            $modifier->setPriceContext($orderTypeId, $selectedDeliveryApp);
                                        }
                                    }

                                    // Get prices with context applied
                                    $baseItemPrice = $item->menuItemVariation
                                        ? $item->menuItemVariation->price
                                        : $item->menuItem->price;
                                    $modifierTotal = $item->modifierOptions->sum('pivot.modifier_option_price');
                                    $displayPrice = $baseItemPrice + $modifierTotal;
                                @endphp
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                    wire:key='menu-item-{{ $key . microtime() }}'
                                    wire:loading.class.delay='opacity-80'>
                                    <td class="flex flex-col p-2 min-w-0 sm:mr-2 lg:mr-12 lg:min-w-28">
                                        <div class="inline-flex items-center text-xs text-gray-900 dark:text-white">
                                            {{ $key + 1 }}. {{ $item->menuItem ? $item->menuItem->item_name : '--' }}
                                        </div>

                                        <div class="inline-flex items-center text-xs text-gray-600 dark:text-white">
                                            {{ isset($item->menuItemVariation) ? $item->menuItemVariation->variation : '' }}
                                        </div>

                                        @if ($item->modifierOptions->isNotEmpty())
                                            <div class="text-xs text-gray-600 dark:text-white">
                                                @foreach ($item->modifierOptions as $modifier)
                                                    <div
                                                        class="flex justify-between items-center px-1 py-0.5 mb-1 text-xs bg-gray-200 rounded-md border-l-2 border-blue-500 dark:bg-gray-900">
                                                        <span class="text-gray-900 dark:text-white">{{ $modifier->name ?? $modifier->pivot->modifier_option_name }}</span>
                                                        <span
                                                            class="text-gray-600 dark:text-gray-300">{{ currency_format($modifier->pivot->modifier_option_price ?? $modifier->price , $currencyId) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                    </td>
                                    <td
                                        class="p-2 text-xs text-center text-gray-900 whitespace-nowrap dark:text-gray-400">
                                        {{ $item->quantity }}
                                    </td>

                                    <td
                                        class="hidden p-2 text-xs font-medium text-right text-gray-700 whitespace-nowrap dark:text-white lg:table-cell">
                                        {{ currency_format($displayPrice, $currencyId) }}
                                    </td>
                                    <td
                                        class="p-2 text-xs font-medium text-right text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ currency_format($item->amount, $currencyId) }}
                                    </td>

                                    @if (!in_array($order->status, ['paid', 'payment_due', 'canceled']) && user_can('Delete Order') && !$usingKotItemsFallback)
                                        <td class="text-right whitespace-nowrap">
                                            <button class="p-1 text-gray-800 border rounded dark:text-gray-400 dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20"
                                                wire:click="showDeleteItemModal('{{ $item->id }}')">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    @endif

                                </tr>
                            @empty
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="p-2 space-x-6 text-gray-800 dark:text-gray-200" colspan="5">
                                        @lang('messages.noItemAdded')
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                @if ($order->payments->count())
                    <div class="flex flex-col rounded">
                        <table class="flex-1 min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                    class="p-2 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 rtl:text-right ltr:text-left">
                                        @lang('modules.order.amount')
                                    </th>

                                    <th scope="col"
                                        class="p-2 text-xs text-center text-gray-500 uppercase dark:text-gray-400">
                                        @lang('modules.order.paymentMethod')
                                    </th>
                                    <th scope="col"
                                        class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                        @lang('app.dateTime')
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                                wire:key='menu-order-payments-{{ microtime() }}'>

                                @foreach ($order->payments as $key => $item)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="p-2 text-base text-gray-900 whitespace-nowrap dark:text-gray-400">
                                            {{ currency_format($item->amount, $currencyId) }}
                                        </td>


                                            <td @class([
                                                'p-2 text-sm text-gray-900 whitespace-nowrap text-center dark:text-gray-400',
                                            ])>
                                                <div class="inline-flex items-center justify-center gap-2">
                                                    @if($order->status !== 'pending_verification' && user_can('Update Order'))
                                                        <div class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs sm:text-sm">
                                                            {{ ucwords(str_replace('_', ' ', $item->payment_method)) }}
                                                        </div>
                                                    @else
                                                        <div @class([
                                                            'inline-flex items-center gap-1 px-2 py-1 rounded text-xs sm:text-sm',
                                                            'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-400 border border-red-400' => $item->payment_method == 'due',
                                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 border border-gray-400' => $item->payment_method != 'due',
                                                        ])>
                                                            @php
                                                                $icons = [
                                                                    'cash' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16"><path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm7 8a2 2 0 0 0 0-4 2 2 0 0 0 0 4"/><path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2h-1V3a2 2 0 0 0-2-2h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>',
                                                                    'upi' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16"><path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z"/><path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z"/><path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z"/><path d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z"/><path d="M12 9h2V8h-2z"/></svg>',
                                                                    'card' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/><path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>'
                                                                ];
                                                            @endphp

                                                            {!! $icons[$item->payment_method] ?? '' !!}
                                                            {{ ucwords(str_replace('_', ' ', $item->payment_method)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td
                                                class="p-2 text-xs sm:text-sm text-right text-gray-900 whitespace-nowrap dark:text-gray-400">
                                                @if ($item->payment_method == 'due' && user_can('Update Order'))
                                                    <x-secondary-button wire:click='showPayment({{ $order->id }})'>@lang('modules.order.addPayment')</x-secondary-button>
                                                @elseif ($order->status == 'pending_verification' && user_can('Update Order'))
                                                    <x-secondary-button class="me-1" wire:click="paymentReceived({{ $order->id }}, 'received')">
                                                        @lang('modules.order.confirmPayment')
                                                    </x-secondary-button>
                                                    <x-danger-button
                                                        wire:click="paymentReceived({{ $order->id }}, 'not_received')">
                                                        @lang('modules.order.reportUnpaid')
                                                    </x-danger-button>
                                                @else
                                                    {{ $item->created_at->timezone(timezone())->translatedFormat(dateFormat() . ' ' . timeFormat()) }}
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach

                            </tbody>
                        </table>

                    </div>
                @endif

                @if ($order->order_type == 'delivery' && $order->delivery_address)
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex gap-1.5 items-center font-semibold text-gray-800 dark:text-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/></svg>
                                @lang('modules.customer.address')
                            </div>

                            @if($order->customer_lat && $order->customer_lng && branch()->lat && branch()->lng)
                                <a href="https://www.google.com/maps/dir/?api=1&travelmode=two-wheeler&origin={{ branch()->lat }},{{ branch()->lng }}&destination={{ $order->customer_lat }},{{ $order->customer_lng }}"
                                    target="_blank"
                                    class="flex items-center gap-1 text-sm text-blue-500 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    <span>@lang('modules.order.viewOnMap')</span>
                                    <svg width="24" height="24" class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4m-8-2 8-8m0 0v5m0-5h-5"/></svg>
                                </a>
                            @endif
                        </div>

                        <div class="p-2 text-sm text-gray-600 bg-white border border-gray-200 rounded dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600">
                            {!! nl2br(e($order->delivery_address)) !!}
                        </div>
                    </div>
                @endif

                </div>

                <div class="shrink-0 border-t border-gray-200 dark:border-gray-700 pt-3 mt-1 -mx-2 px-2 bg-white dark:bg-gray-800">
                <div>
                    <div
                        class="w-full h-auto  px-2 py-1 space-y-1 text-center rounded select-none bg-gray-50 dark:bg-gray-700">
                        @if ($orderItemsCount > 0 && user_can('Update Order') && $order->status !== 'paid')
                            <div class="flex gap-2 text-left">
                                @if (user_can('Add Discount on POS'))
                                    <x-secondary-button wire:click="showAddDiscount" class="!py-1.5 !px-2.5 !text-xs !gap-1">
                                        <svg class="w-3.5 h-3.5 text-current shrink-0" width="24" height="24" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="m7.25 14.25-5.5-5.5 7-7h5.5v5.5z"/><circle cx="11" cy="5" r=".5" fill="#000"/></svg>
                                        @lang('modules.order.addDiscount')
                                    </x-secondary-button>
                                @endif
                            </div>
                        @endif
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <div>
                                @lang('modules.order.totalItem')
                            </div>
                            <div>
                                {{ $orderItemsCount }}
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <span>@lang('modules.order.subTotal')</span>
                                @php
                                    $stampDiscountAmount = (float)($order->stamp_discount_amount ?? 0);
                                    $hasFreeStampItems = $order->items()->where('is_free_item_from_stamp', true)->exists();
                                @endphp
                                @if($stampDiscountAmount > 0 || $hasFreeStampItems)
                                    <span class="px-1.5 py-0.5 text-xs rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        @lang('app.stampDiscount')
                                        @if($stampDiscountAmount > 0)
                                            (-{{ currency_format($stampDiscountAmount, $currencyId ?? restaurant()->currency_id) }})
                                        @elseif($hasFreeStampItems)
                                            (@lang('app.freeItem'))
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <div>
                                {{ currency_format($order->sub_total, $currencyId) }}
                            </div>
                        </div>

                        @if ($order->loyalty_points_redeemed > 0 && $order->loyalty_discount_amount > 0)
                            <div wire:key="loyaltyDiscount" class="flex justify-between {{ $textSize ?? 'text-sm' }} text-blue-600 dark:text-blue-400">
                                <div>
                                    @lang('app.loyaltyDiscount') ({{ number_format($order->loyalty_points_redeemed) }} @lang('app.points'))
                                </div>
                                <div>
                                    -{{ currency_format($order->loyalty_discount_amount, $currencyId ?? restaurant()->currency_id) }}
                                </div>
                            </div>
                        @endif

                        @php
                            $_odDiscDecimals = (int) (optional(currency_format_setting($currencyId ?? null))->no_of_decimal ?? 2);
                            $_odShowOrderDiscount = (int) ($order->loyalty_points_redeemed ?? 0) === 0
                                && round((float) ($order->discount_amount ?? 0), $_odDiscDecimals) > 0;
                        @endphp
                        @if ($_odShowOrderDiscount)
                            <div wire:key="discountAmount" class="flex justify-between text-xs  text-green-500 dark:text-green-400">
                                <div class="flex items-center gap-x-1">
                                    <span>
                                        @lang('modules.order.discount') @if ($order->discount_type == 'percent')
                                            ({{ rtrim(rtrim(number_format($order->discount_value, 2), '0'), '.') }}%)
                                        @endif
                                    </span>
                                    @if(user_can('Update Order') && $order->status !== 'paid')
                                        <span class="text-red-500 cursor-pointer hover:scale-110 active:scale-100" wire:click="removeDiscount">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    -{{ currency_format($order->discount_amount, $currencyId) }}
                                </div>
                            </div>
                        @endif

                        @php
                            // Calculate net for charges
                            $net = $order->sub_total - ($order->discount_amount ?? 0);
                        @endphp

                        @foreach ($order->charges as $item)
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <div class="inline-flex items-center gap-x-1">
                                    {{ $item->charge->charge_name }}
                                    @if ($item->charge->charge_type == 'percent')
                                        ({{ $item->charge->charge_value }}%)
                                    @endif

                                    @if (!in_array($order->status, ['paid', 'payment_due', 'canceled']))
                                        <span class="text-red-500 cursor-pointer hover:scale-110 active:scale-100"
                                            wire:click="removeCharge('{{ $item->id }}')">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    @php
                                        // Calculate discounted subtotal for charges (after both regular and loyalty discounts)
                                        $chargeBase = $order->sub_total
                                            - ($order->discount_amount ?? 0)
                                            - ($order->loyalty_discount_amount ?? 0);
                                    @endphp
                                    {{ currency_format($item->charge->getAmount($chargeBase), $currencyId) }}
                                </div>
                            </div>
                        @endforeach

                        @if ($order->tip_amount > 0)
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <div>
                                    @lang('modules.order.tip')
                                </div>
                                <div>
                                    {{ currency_format($order->tip_amount, $currencyId) }}
                                </div>
                            </div>
                        @endif

                        @if ($order->order_type === 'delivery' && !is_null($order->delivery_fee))
                        <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400">
                            <div>
                                @lang('modules.delivery.deliveryFee')
                            </div>
                            <div>
                                @if($order->delivery_fee > 0)
                                    {{ currency_format($order->delivery_fee, $currencyId) }}
                                @else
                                    <span class="font-medium text-green-500">@lang('modules.delivery.freeDelivery')</span>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if ($taxMode == 'order')
                            @php
                                // Calculate net for display
                                $net = $order->sub_total - ($order->discount_amount ?? 0);

                                // Use saved tax_base from database
                                $taxBase = $order->tax_base ?? ($net + $order->charges->sum(fn($charge) => $charge->charge->getAmount($net)));
                            @endphp
                            @foreach ($order->taxes as $item)
                                @if($item->tax)
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <div>
                                            {{ $item->tax->tax_name }} ({{ $item->tax->tax_percent }}%)
                                        </div>
                                        <div>
                                            @php
                                                // Step 1: Calculate discounted subtotal (after both regular and loyalty discounts)
                                                // Loyalty points are always removed from subtotal before calculating tax
                                                $discountedSubtotal = $order->sub_total
                                                    - ($order->discount_amount ?? 0)
                                                    - ($order->loyalty_discount_amount ?? 0);

                                                // Step 2: Calculate service charges on discounted subtotal
                                                $serviceTotal = 0;
                                                if ($order->charges && $order->charges->count() > 0) {
                                                    foreach ($order->charges as $chargeRelation) {
                                                        $charge = $chargeRelation->charge;
                                                        if ($charge) {
                                                            $chargeAmount = $charge->getAmount((float)$discountedSubtotal);
                                                            $serviceTotal += (float)$chargeAmount;
                                                        }
                                                    }
                                                }

                                                // Step 3: Calculate tax_base based on Tax Calculation Base setting
                                                // Check if service charges should be included in tax base
                                                $restaurant = restaurant();
                                                $includeChargesInTaxBase = false;
                                                if ($restaurant && isset($restaurant->include_charges_in_tax_base)) {
                                                    $includeChargesInTaxBase = (bool)$restaurant->include_charges_in_tax_base;
                                                }

                                                // Tax base = (subtotal - discounts) + service charges (if enabled)
                                                $taxBase = $includeChargesInTaxBase
                                                    ? ($discountedSubtotal + $serviceTotal)
                                                    : $discountedSubtotal;
                                                $taxBase = max(0, (float)$taxBase);

                                                // Step 4: Calculate tax on tax_base
                                                $taxAmount = ($item->tax->tax_percent / 100) * $taxBase;
                                            @endphp
                                            {{ currency_format($taxAmount, restaurant()->currency_id) }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            @if($order->total_tax_amount > 0)
                                @php
                                    $taxTotals = [];
                                    $totalTax = 0;
                                    foreach ($order->items as $item) {
                                        $qty = $item->quantity ?? 1;
                                        $taxBreakdown = is_array($item->tax_breakup) ? $item->tax_breakup : (json_decode($item->tax_breakup, true) ?? []);

                                        if (is_array($taxBreakdown) && !empty($taxBreakdown)) {
                                            foreach ($taxBreakdown as $taxName => $taxInfo) {
                                                // Support both keyed tax format and indexed legacy format.
                                                if (is_array($taxInfo) && array_key_exists('name', $taxInfo)) {
                                                    $name = $taxInfo['name'] ?? __('modules.order.tax');
                                                    $percent = $taxInfo['percent'] ?? ($taxInfo['rate'] ?? 0);
                                                    $amount = (float) ($taxInfo['amount'] ?? 0);
                                                } else {
                                                    $name = is_string($taxName) ? $taxName : __('modules.order.tax');
                                                    $percent = is_array($taxInfo) ? (float) ($taxInfo['percent'] ?? 0) : 0;
                                                    $amount = is_array($taxInfo) ? (float) ($taxInfo['amount'] ?? 0) : 0;
                                                }

                                                if (!isset($taxTotals[$name])) {
                                                    $taxTotals[$name] = [
                                                        'percent' => $percent,
                                                        'amount' => 0,
                                                    ];
                                                }

                                                $taxTotals[$name]['amount'] += ($amount * $qty);
                                            }
                                        }

                                        // Fallback for records where tax_breakup is missing but tax amount exists.
                                        if (empty($taxBreakdown) && ($item->tax_amount ?? 0) > 0) {
                                            $fallbackPercent = (float) ($item->tax_percentage ?? 0);
                                            $fallbackName = __('modules.order.tax') . ' ' . number_format($fallbackPercent, 2);

                                            if (!isset($taxTotals[$fallbackName])) {
                                                $taxTotals[$fallbackName] = [
                                                    'percent' => $fallbackPercent,
                                                    'amount' => 0,
                                                ];
                                            }

                                            $taxTotals[$fallbackName]['amount'] += (float) $item->tax_amount;
                                        }

                                        $totalTax += $item->tax_amount ?? 0;
                                    }
                                @endphp

                                @foreach ($taxTotals as $taxName => $taxInfo)
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $taxName }} ({{ $taxInfo['percent'] }}%)</span>
                                        <span>{{ currency_format($taxInfo['amount'], $currencyId) }}</span>
                                    </div>
                                @endforeach
                                <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <div>
                                        @lang('modules.order.totalTax')
                                    </div>
                                    <div>
                                        {{ currency_format($totalTax, $currencyId) }}
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="flex justify-between font-medium dark:text-gray-400">
                            <div>
                                @lang('modules.order.total')
                            </div>
                            <div>
                                {{ currency_format($order->total, $currencyId) }}
                            </div>
                        </div>


                        <div class="flex justify-between font-medium dark:text-gray-400">
                            <div>
                                @lang('modules.order.balanceReturn')
                            </div>
                            <div>
                                @php
                                    $totalBalance = $order->payments->sum('balance');
                                @endphp

                                {{ currency_format($totalBalance > 0 ? $totalBalance : 0, $currencyId) }}
                            </div>
                        </div>

                    </div>

                    <div class="w-full h-auto pt-1 pb-2 select-none">
                        @php
                            $cashCollection = $order->order_type === 'delivery' ? $order->orderCashCollection : null;
                        @endphp

                        @if ($cashCollection)
                            @php
                                $cashCollectionStatus = $cashCollection->status;
                                $isCustomerPaid = in_array($cashCollectionStatus, ['collected', 'submitted', 'settled'], true)
                                    || (float) ($order->amount_paid ?? 0) > 0;
                                $displayAmount = $isCustomerPaid
                                    ? (float) ($order->amount_paid ?? $cashCollection->collected_amount ?? 0)
                                    : (float) ($cashCollection->expected_amount ?? $order->remainingAmount());
                                $bannerClasses = $isCustomerPaid
                                    ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/30'
                                    : 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-950/30';
                                $titleClasses = $isCustomerPaid
                                    ? 'text-emerald-800 dark:text-emerald-200'
                                    : 'text-amber-800 dark:text-amber-200';
                                $descriptionClasses = $isCustomerPaid
                                    ? 'text-emerald-700 dark:text-emerald-300'
                                    : 'text-amber-700 dark:text-amber-300';
                                $labelClasses = $isCustomerPaid
                                    ? 'text-emerald-700 dark:text-emerald-300'
                                    : 'text-amber-700 dark:text-amber-300';
                                $badgeClasses = $isCustomerPaid
                                    ? 'border-emerald-300 bg-white text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                    : 'border-amber-300 bg-white text-amber-700 dark:border-amber-700 dark:bg-amber-900/40 dark:text-amber-200';
                            @endphp

                            <div class="mb-3 rounded-xl border p-4 {{ $bannerClasses }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="text-sm font-semibold {{ $titleClasses }}">
                                            @lang($isCustomerPaid ? 'modules.delivery.customerPaymentCollected' : 'modules.delivery.customerPaymentPending')
                                        </div>
                                        <div class="mt-1 text-sm {{ $descriptionClasses }}">
                                            @lang(
                                                $isCustomerPaid
                                                    ? 'modules.delivery.customerPaymentCollectedMessage'
                                                    : 'modules.delivery.customerPaymentPendingMessage',
                                                ['amount' => currency_format($displayAmount, $currencyId)]
                                            )
                                        </div>
                                    </div>

                                    <div class="sm:text-right">
                                        <div class="text-xs font-medium uppercase tracking-wide {{ $labelClasses }}">
                                            @lang('modules.delivery.executiveCollectionStatus')
                                        </div>
                                        <div class="mt-1 inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $badgeClasses }}">
                                            {{ match ($cashCollectionStatus) {
                                                'pending_collection' => __('modules.delivery.pendingCollection'),
                                                'collected' => __('modules.delivery.collected'),
                                                'submitted' => __('modules.delivery.submitted'),
                                                'settled' => __('modules.delivery.settled'),
                                                'rejected' => __('modules.delivery.rejected'),
                                                'not_collected' => __('modules.delivery.not_collected'),
                                                default => Str::headline((string) $cashCollectionStatus),
                                            } }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Primary actions -->
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            @if ($order->status == 'kot' && !is_null($order->table_id))
                                <button
                                    class="rounded-lg bg-gray-700 hover:bg-gray-800 text-white py-2.5 px-3 inline-flex items-center justify-center gap-2 transition-colors shadow-sm text-sm font-semibold"
                                    wire:click="saveOrder('kot')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    @lang('modules.order.addItems')
                                </button>

                                <button
                                    class="rounded-lg bg-green-600 hover:bg-green-700 text-white py-2.5 px-3 inline-flex items-center justify-center gap-2 transition-colors shadow-sm text-sm font-semibold"
                                    wire:click="saveOrder('bill')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    @lang('modules.order.bill')
                                </button>
                            @endif

                            @if (($order->status == 'billed' || $order->status == 'payment_due') && user_can('Update Order'))
                                <button
                                    class="col-span-2 rounded-lg bg-green-600 hover:bg-green-700 text-white py-2.5 px-3 inline-flex items-center justify-center gap-2 transition-colors shadow-sm text-sm font-semibold"
                                    wire:click='showPayment({{ $order->id }})'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                    @lang('modules.order.addPayment')
                                </button>
                            @endif
                        </div>
                        <!-- Secondary actions -->
                        <div class="grid grid-cols-4 gap-1.5">
                            <x-secondary-link wire:click="printOrder({{ $order->id }})" target="_blank" wire:key="print-payment-button"
                                class="cursor-pointer rounded-lg bg-white hover:bg-gray-50 text-gray-700 border py-2 px-2 inline-flex flex-row items-center justify-center gap-1.5 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                    <path
                                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                    </svg>
                                    <span class="text-xs font-medium leading-none truncate">
                                        @if($order->split_type && $order->splitOrders->count() > 0)
                                            @lang('modules.order.printSplits')
                                        @else
                                            @lang('app.print')
                                        @endif
                                    </span>
                            </x-secondary-link>

                            @if (in_array($order->status, ['billed', 'payment_due', 'paid']) && user_can('Update Order'))
                                <button
                                    class="rounded-lg bg-red-600 hover:bg-red-700 text-white py-2 px-2 inline-flex flex-row items-center justify-center gap-1.5 transition-colors"
                                    wire:click="$toggle('cancelOrderModal')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="text-xs font-medium leading-none">@lang('app.cancel')</span>
                                </button>
                            @endif

                            @if (!in_array($order->status, ['paid', 'payment_due', 'canceled']) && user()->hasRole('Admin_'. user()->restaurant_id))
                                <button
                                    class="rounded-lg bg-red-500 hover:bg-red-600 text-white py-2 px-2 inline-flex flex-row items-center justify-center gap-1.5 transition-colors"
                                    wire:click="$toggle('deleteOrderModal')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 7-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                    <span class="text-xs font-medium leading-none">@lang('app.delete')</span>
                                </button>
                            @endif

                            <button
                                class="rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-2 inline-flex flex-row items-center justify-center gap-1.5 transition-colors dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                wire:click="$toggle('showOrderDetail')" wire:loading.attr="disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-xs font-medium leading-none">{{ __('app.close') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                </div>
                </div>

            @endif
        </x-slot>
    </x-right-modal>

    @if ($order)
    <x-dialog-modal wire:model.live="showTableModal" maxWidth="2xl">
        <x-slot name="title">
            @lang('modules.table.availableTables')
        </x-slot>

        <x-slot name="content">
            @livewire('pos.setTable', ['targetEvent' => 'setOrderDetailTable'])
        </x-slot>

        <x-slot name="footer">
            <x-button-cancel wire:click="$toggle('showTableModal')" wire:loading.attr="disabled" />
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showDiscountModal" maxWidth="xl">
        <x-slot name="title">
            @lang('modules.order.addDiscount')
        </x-slot>

        <x-slot name="content">
            <div class="mt-4 flex">
                <!-- Discount Value -->
                <x-input id="discountValue" class="block w-2/3 text-md" type="number" step="0.001" wire:model.defer="discountValue"
                    placeholder="{{ __('modules.order.enterDiscountValue') }}" min="0" />
                <!-- Discount Type -->
                <x-select id="discountType" class="block ml-2 w-1/3 rounded-md border-gray-300" wire:model.defer="discountType">
                    <option value="fixed">@lang('modules.order.fixed')</option>
                    <option value="percent">@lang('modules.order.percent')</option>
                </x-select>
            </div>
        <x-input-error for="discountValue" class="mt-2" />
        </x-slot>

        <x-slot name="footer">
            <x-button-cancel wire:click="$set('showDiscountModal', false)">@lang('app.cancel')</x-button-cancel>
            <x-button class="ml-3" wire:click="addDiscounts" wire:loading.attr="disabled">@lang('app.save')</x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Print Options Modal -->
    <x-dialog-modal wire:model.live="showPrintOptionsModal" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2m8-12V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4z"/></svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.order.printOptions')</h3>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-3">
                @if ($printMode === 'single')
                    <!-- Single Split Selection View -->
                    <div class="space-y-3">
                        <x-secondary-link
                            wire:click="$set('printMode', null)"
                            class="flex items-center gap-2 pb-3 border-b border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer select-none transition-colors"
                        >
                            <span class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
                            </span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white tracking-tight">
                                @lang('modules.order.selectGuest')
                            </span>
                        </x-secondary-link>

                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach ($order->splitOrders->where('status', 'paid') as $index => $split)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-gray-700 {{ $selectedSplitId == $split->id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600' }}">
                                    <input type="radio" wire:model.live="selectedSplitId" value="{{ $split->id }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600">
                                    <div class="flex items-center justify-between flex-1 ml-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white bg-gradient-to-br from-blue-500 to-blue-600 flex-shrink-0">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    @lang('modules.order.guest') #{{ $index + 1 }}
                                                </div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ __('modules.order.' . $split->payment_method) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ currency_format($split->amount, $currencyId) }}
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <x-secondary-button wire:click="$set('showPrintOptionsModal', false)">
                                {{ __('app.cancel') }}
                            </x-secondary-button>
                            <x-button wire:click="printSingleSplit" class="bg-skin-base hover:bg-skin-base/80">
                                <svg  wire:loading wire:target="printSingleSplit" class="inline animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12zm2 5.291A7.96 7.96 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938z"/></svg>
                                <svg wire:loading.remove wire:target="printSingleSplit" class="w-4 h-4 mr-1 -ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2m8-12V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4z"/></svg>
                                @lang('modules.order.printSelected')
                            </x-button>
                        </div>
                    </div>
                @else
                    <!-- Main Print Options View -->
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        @lang('modules.order.selectPrintOption')
                    </p>

                    <!-- Print All Option -->
                    <button wire:click="handlePrintOption('all')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all group">
                        <div class="p-3 bg-gradient-to-br from-indigo-700 to-indigo-500 rounded-lg text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7z"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                @lang('modules.order.printAll')
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                @lang('modules.order.printAllDesc', ['count' => $order->splitOrders->where('status', 'paid')->count() + 1])
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </button>

                    <!-- Summary Only Option -->
                    <button wire:click="handlePrintOption('summary')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all group">
                        <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-lg text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                                @lang('modules.order.summaryOnly')
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                @lang('modules.order.summaryOnlyDesc')
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-green-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </button>

                    <!-- Individual Only Option -->
                    <button wire:click="handlePrintOption('individual')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all group">
                        <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0m6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0M7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                @lang('modules.order.individualOnly')
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                @lang('modules.order.individualOnlyDesc', ['count' => $order->splitOrders->where('status', 'paid')->count()])
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </button>

                    <!-- Single Guest Option -->
                    <button wire:click="handlePrintOption('single')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all group">
                        <div class="p-3 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0m-4 7a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400">
                                @lang('modules.order.singleGuest')
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                @lang('modules.order.singleGuestDesc')
                            </p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-amber-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </button>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            @if ($printMode !== 'single')
                <x-secondary-button wire:click="$set('showPrintOptionsModal', false)">
                    {{ __('app.close') }}
                </x-secondary-button>
            @endif
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model.defer="cancelOrderModal">
        <x-slot name="title">
            <div class="flex items-center gap-4">

                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('modules.order.cancelOrder')</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('modules.order.cancelOrderMessageUndone')</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <!-- Warning Message -->
                <div class="p-4 border bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">@lang('modules.order.cancelOrderMessage')</p>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Please select a reason for cancellation</p>
                        </div>
                    </div>
                </div>

                <!-- Reason Selection -->
                 <div>
                <x-label for="cancelReason" value="{{ __('modules.settings.selectCancelReason') }}" class="text-sm font-medium text-gray-700 dark:text-gray-200" />
                <x-select id="cancelReason" class="block w-full mt-2" wire:model.defer="cancelReason">
                    <option value="">{{ __('modules.settings.selectCancelReason') }}</option>
                    @foreach ($cancelReasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="cancelReason" class="mt-2" />
            </div>

                <!-- Custom Reason Textarea -->
               <textarea
                            wire:model.defer="cancelReasonText"
                            id="cancelReasonText"
                            rows="4"
                            class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none dark:border-gray-600 rounded-xl focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="@lang('modules.settings.enterCancelReason')"
                        ></textarea>
            </div>
        </x-slot>

        <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('cancelOrderModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

                <x-danger-button class="ml-3" wire:click='cancelOrder({{ $order->id }})'
                    wire:loading.attr="disabled">
                @lang('modules.order.cancelOrder')
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>



    <x-confirmation-modal wire:model.defer="deleteOrderModal">
        <x-slot name="title">
            @lang('modules.order.deleteOrder')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.order.deleteOrderMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('deleteOrderModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteOrder({{ $order->id }})'
                wire:loading.attr="disabled">
                @lang('modules.order.deleteOrder')
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model.defer="confirmDeleteModal">
    <x-slot name="title">
        <div class="flex items-center gap-4">

            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('modules.order.cancelOrder')</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('modules.order.cancelOrderMessageUndone')</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="flex flex-col w-full space-y-6">
            <!-- Warning Message -->
            <div class="p-4 border bg-amber-50 rounded-xl border-amber-200 dark:bg-amber-900/20 dark:border-amber-800">
                <div class="flex items-start gap-3">
                    <svg class="flex-shrink-0 mt-0.5 w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">@lang('modules.order.cancelOrderMessage')</p>
                        <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Please select a reason for cancellation</p>
                    </div>
                </div>
            </div>

            <!-- Reason Selection -->
            <div>
                <x-label for="cancelReason" value="{{ __('modules.settings.selectCancelReason') }}" class="text-sm font-medium text-gray-700 dark:text-gray-200" />
                <x-select id="cancelReason" class="block w-full mt-2" wire:model.defer="cancelReason">
                    <option value="">{{ __('modules.settings.selectCancelReason') }}</option>
                    @foreach ($cancelReasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="cancelReason" class="mt-2" />
            </div>

            <!-- Custom Reason Textarea -->
        <textarea
                        wire:model.defer="cancelReasonText"
                        id="cancelReasonText"
                        rows="4"
                        class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none rounded-xl dark:border-gray-600 focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="@lang('modules.settings.enterCancelReason')"
                    ></textarea>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="$set('confirmDeleteModal', false)" wire:loading.attr="disabled">
            {{ __('app.cancel') }}
        </x-secondary-button>

        <x-danger-button class="ml-3" wire:click="cancelOrderStatus({{ $order->id }})" wire:loading.attr="disabled">
            @lang('modules.order.cancelOrder')
        </x-danger-button>
    </x-slot>
    </x-confirmation-modal>

    <!-- Delete Order Item Confirmation Modal -->
    <x-confirmation-modal wire:model.defer="confirmDeleteItemModal">
        <x-slot name="title">
            @lang('modules.order.deleteOrderItem')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.order.deleteOrderItemMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteItemModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($itemToDelete)
            <x-danger-button class="ml-3" wire:click='deleteOrderItems({{ $itemToDelete }})' wire:loading.attr="disabled" wire:key="delete-order-item-{{ $itemToDelete }}">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>

    <!-- Table Change Confirmation Modal -->
    <x-dialog-modal wire:model.live="showTableChangeConfirmationModal" maxWidth="md">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                @lang('modules.order.changeTable')
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-amber-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.order.confirmTableChange')
                    </h3>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <p>@lang('modules.order.currentTable'): <strong>{{ $order->table->table_code ?? '--' }}</strong></p>
                        @if($pendingTable)
                            <p>@lang('modules.order.changeTo'): <strong>{{ $pendingTable->table_code }}</strong></p>
                        @endif
                        <p class="mt-2">@lang('modules.order.tableChangeMessage')</p>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border border-amber-200 dark:border-amber-800">
                    <p class="text-sm text-amber-700 dark:text-amber-300 text-center">
                        @lang('modules.order.tableChangeWarning')
                    </p>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2 w-full">
                <x-button-cancel wire:click="cancelTableChange" wire:loading.attr="disabled">
                    @lang('app.cancel')
                </x-button-cancel>
                <x-button wire:click="confirmTableChange" wire:loading.attr="disabled" class="bg-amber-600 hover:bg-amber-700">
                    @lang('modules.order.changeTable')
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>

 @endif

    @script
        <script>
            $wire.on('play_beep', () => {
                new Audio("{{ asset('sound/sound_beep-29.mp3')}}").play();
            });

            $wire.on('print_location', (url) => {
                // Detect if running in PWA standalone mode
                const isPWA = (window.matchMedia('(display-mode: standalone)').matches) ||
                            (window.navigator.standalone === true) ||
                            (document.referrer.includes('android-app://'));

                if (isPWA) {
                    // In PWA mode, open in same tab to prevent app closing
                    window.location.href = url;
                } else {
                    // In browser mode, open in new tab
                    const anchor = document.createElement('a');
                    anchor.href = url;
                    anchor.target = '_blank';
                    anchor.click();
                }
            });

            // Remove portaled tooltips left on <body> when switching orders (Livewire morph).
            window.cleanupOrderDetailDrawerPortaledTooltips = function() {
                document.querySelectorAll(
                    'body > [role="tooltip"][id^="tooltip-od-detail-"], body > [role="tooltip"][data-od-tt-in-body="1"]'
                ).forEach((el) => el.remove());
            };

            const portalOrderDetailTooltip = (tooltip, trigger) => {
                if (!tooltip.dataset.odTtInBody) {
                    document.body.appendChild(tooltip);
                    tooltip.dataset.odTtInBody = '1';
                }
                const rect = trigger.getBoundingClientRect();
                tooltip.style.position = 'fixed';
                tooltip.style.zIndex = '2147483646';
                tooltip.style.display = 'block';
                tooltip.style.width = 'max-content';
                tooltip.style.maxWidth = '14rem';
                tooltip.style.whiteSpace = 'nowrap';
                tooltip.style.top = `${Math.max(6, rect.top - 8)}px`;
                tooltip.style.left = `${rect.left + rect.width / 2}px`;
                tooltip.style.transform = 'translate(-50%, -100%)';
                tooltip.classList.remove('hidden', 'invisible', 'opacity-0');
                tooltip.classList.add('opacity-100');
            };

            const hideOrderDetailPortaledTooltip = (tooltip) => {
                if (!tooltip) return;
                tooltip.classList.add('hidden', 'invisible', 'opacity-0');
                tooltip.classList.remove('opacity-100');
                tooltip.style.display = '';
            };

            // Order detail drawer: portal tooltips to body (panel overflow clips fixed children).
            window.initOrderDetailDrawerIconTooltips = function() {
                try {
                    const drawer = document.getElementById('order-detail-drawer');
                    if (!drawer || drawer.dataset.odDrawerTtDelegated === '1') return;

                    const getIconTooltip = (trigger) => {
                        const id = trigger.getAttribute('data-tooltip-target');
                        return id ? document.getElementById(id) : null;
                    };

                    const getStatusTooltip = (trigger) => {
                        const id = trigger.getAttribute('data-pos-status-tooltip');
                        return id ? document.getElementById(id) : null;
                    };

                    const showIcon = (trigger) => {
                        const tooltip = getIconTooltip(trigger);
                        if (!tooltip) return;
                        portalOrderDetailTooltip(tooltip, trigger);
                    };

                    const hideIcon = (trigger) => {
                        hideOrderDetailPortaledTooltip(getIconTooltip(trigger));
                    };

                    const showStatus = (trigger) => {
                        const tooltip = getStatusTooltip(trigger);
                        if (!tooltip) return;
                        portalOrderDetailTooltip(tooltip, trigger);
                    };

                    const hideStatus = (trigger) => {
                        hideOrderDetailPortaledTooltip(getStatusTooltip(trigger));
                    };

                    const shouldKeepOpen = (trigger, related, getTooltip) => {
                        if (!related) return false;
                        const tooltip = getTooltip(trigger);
                        return trigger.contains(related) || (tooltip && tooltip.contains(related));
                    };

                    drawer.addEventListener('mouseover', (e) => {
                        const statusTrigger = e.target.closest('[data-pos-status-tooltip^="tooltip-od-detail-status-"]');
                        if (statusTrigger && drawer.contains(statusTrigger)) {
                            showStatus(statusTrigger);
                            return;
                        }
                        const iconTrigger = e.target.closest('[data-tooltip-target^="tooltip-od-detail-"]');
                        if (!iconTrigger || !drawer.contains(iconTrigger)) return;
                        if (iconTrigger.hasAttribute('data-pos-status-tooltip')) return;
                        showIcon(iconTrigger);
                    });

                    drawer.addEventListener('mouseout', (e) => {
                        const statusTrigger = e.target.closest('[data-pos-status-tooltip^="tooltip-od-detail-status-"]');
                        if (statusTrigger && drawer.contains(statusTrigger)) {
                            if (!shouldKeepOpen(statusTrigger, e.relatedTarget, getStatusTooltip)) {
                                hideStatus(statusTrigger);
                            }
                            return;
                        }
                        const iconTrigger = e.target.closest('[data-tooltip-target^="tooltip-od-detail-"]');
                        if (!iconTrigger || !drawer.contains(iconTrigger)) return;
                        if (iconTrigger.hasAttribute('data-pos-status-tooltip')) return;
                        if (shouldKeepOpen(iconTrigger, e.relatedTarget, getIconTooltip)) return;
                        hideIcon(iconTrigger);
                    });

                    drawer.addEventListener('focusin', (e) => {
                        const statusTrigger = e.target.closest('[data-pos-status-tooltip^="tooltip-od-detail-status-"]');
                        if (statusTrigger && drawer.contains(statusTrigger)) {
                            showStatus(statusTrigger);
                            return;
                        }
                        const iconTrigger = e.target.closest('[data-tooltip-target^="tooltip-od-detail-"]');
                        if (iconTrigger && drawer.contains(iconTrigger) && !iconTrigger.hasAttribute('data-pos-status-tooltip')) {
                            showIcon(iconTrigger);
                        }
                    });

                    drawer.addEventListener('focusout', (e) => {
                        const statusTrigger = e.target.closest('[data-pos-status-tooltip^="tooltip-od-detail-status-"]');
                        if (statusTrigger && drawer.contains(statusTrigger)) {
                            if (!shouldKeepOpen(statusTrigger, e.relatedTarget, getStatusTooltip)) {
                                hideStatus(statusTrigger);
                            }
                            return;
                        }
                        const iconTrigger = e.target.closest('[data-tooltip-target^="tooltip-od-detail-"]');
                        if (!iconTrigger || !drawer.contains(iconTrigger)) return;
                        if (iconTrigger.hasAttribute('data-pos-status-tooltip')) return;
                        if (shouldKeepOpen(iconTrigger, e.relatedTarget, getIconTooltip)) return;
                        hideIcon(iconTrigger);
                    });

                    drawer.dataset.odDrawerTtDelegated = '1';
                } catch (e) {
                    console.warn('Order detail drawer tooltip init failed:', e);
                }
            };

            const runOrderDetailDrawerTooltips = () => {
                requestAnimationFrame(() => window.initOrderDetailDrawerIconTooltips?.());
            };
            runOrderDetailDrawerTooltips();

            $wire.watch('showOrderDetail', (open) => {
                if (!open) {
                    window.cleanupOrderDetailDrawerPortaledTooltips?.();
                }
            });

            if (!window.__orderDetailDrawerTtHooksBound) {
                window.__orderDetailDrawerTtHooksBound = true;
                const bindMorphHook = () => {
                    if (window.__orderDetailDrawerMorphHook || typeof Livewire === 'undefined' || typeof Livewire.hook !== 'function') {
                        return;
                    }
                    window.__orderDetailDrawerMorphHook = true;
                    Livewire.hook('morph.updating', () => {
                        window.cleanupOrderDetailDrawerPortaledTooltips?.();
                    });
                    Livewire.hook('morph.updated', () => runOrderDetailDrawerTooltips());
                };
                if (window.Livewire && typeof Livewire.hook === 'function') {
                    bindMorphHook();
                } else {
                    document.addEventListener('livewire:init', bindMorphHook);
                }
                document.addEventListener('livewire:navigated', () => {
                    window.cleanupOrderDetailDrawerPortaledTooltips?.();
                    runOrderDetailDrawerTooltips();
                });
            }

        </script>

    @endscript


</div>
