@php
    $isStandaloneKotDetailPage = request()->routeIs('pos.kot');
    $isKotSidebarDetailView = $isStandaloneKotDetailPage && request()->boolean('show-order-detail');
    $compactOrderDetailUi = $isKotSidebarDetailView || $isStandaloneKotDetailPage;
    $shouldStickyAddPayment = $isStandaloneKotDetailPage
        && isset($orderDetail)
        && in_array($orderDetail->status, ['billed', 'payment_due'])
        && user_can('Update Order');
@endphp

    <div @class([
        'pos-order-detail-panel flex flex-col flex-1 min-h-0 h-full w-full max-w-full bg-white dark:bg-gray-800 overflow-hidden',
        'p-4 lg:border-l lg:border-gray-200 dark:lg:border-gray-700' => !$isStandaloneKotDetailPage,
        'px-2 pt-3 pr-2 rounded-md' => $isKotSidebarDetailView,
        'p-3 lg:p-4 lg:pr-4' => $isStandaloneKotDetailPage && !$isKotSidebarDetailView,
    ])>
    <div class="flex flex-col flex-1 min-h-0">

        <div @class([
            'flex-1 min-h-0 overflow-y-auto overflow-x-hidden',
        ])>

        <div class="flex justify-between items-center dark:text-neutral-200">
            <h2 class="{{ $compactOrderDetailUi ? 'text-sm font-semibold' : 'text-lg' }} dark:text-neutral-200">
                {{ $orderDetail->show_formatted_order_number }}

            </h2>
            @php
                $orderDetailOrderTypeLabel = Str::title($orderDetail->orderType?->order_type_name ?? $orderDetail->custom_order_type_name ?? $orderDetail->order_type);
            @endphp
            <div class="flex items-center gap-2 cursor-default"
                title="{{ $orderDetailOrderTypeLabel }}"
                data-tooltip-target="tooltip-order-detail-order-type">
                @if ($orderDetail->order_type == 'pickup')
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-bag-fill" viewBox="0 0 16 16">
                        <path
                            d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z" />
                    </svg>
                @elseif($orderDetail->order_type == 'delivery')
                    <svg class="w-4 h-4 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                        fill="currentColor" version="1.0" viewBox="0 0 512 512"
                        xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(0 512) scale(.1 -.1)">
                            <path d="m2605 4790c-66-13-155-48-213-82-71-42-178-149-220-221-145-242-112-552 79-761 59-64 61-67 38-73-13-4-60-24-104-46-151-75-295-249-381-462-20-49-38-91-39-93-2-2-19 8-40 22s-54 30-74 36c-59 16-947 12-994-4-120-43-181-143-122-201 32-33 76-33 106 0 41 44 72 55 159 55h80v-135c0-131 1-137 25-160l24-25h231 231l24 25c24 23 25 29 25 161v136l95-4c82-3 97-6 117-26l23-23v-349-349l-46-46-930-6-29 30c-17 16-30 34-30 40 0 7 34 11 95 11 88 0 98 2 120 25 16 15 25 36 25 55s-9 40-25 55c-22 23-32 25-120 25h-95v80 80h55c67 0 105 29 105 80 0 19-9 40-25 55l-24 25h-231-231l-24-25c-33-32-33-78 0-110 22-23 32-25 120-25h95v-80-80h-175c-173 0-176 0-200-25-33-32-33-78 0-110 24-25 27-25 197-25h174l12-45c23-88 85-154 171-183 22-8 112-12 253-12h220l-37-43c-103-119-197-418-211-669-7-115-7-116 19-142 26-25 29-26 164-26h138l16-69c55-226 235-407 464-466 77-20 233-20 310 0 228 59 409 240 463 464l17 71h605 606l13-62c58-281 328-498 621-498 349 0 640 291 640 640 0 237-141 465-350 569-89 43-193 71-271 71h-46l-142 331c-78 183-140 333-139 335 2 1 28-4 58-12 80-21 117-18 145 11l25 24v351 351l-26 26c-24 24-30 25-91 20-130-12-265-105-317-217l-23-49-29 30c-16 17-51 43-79 57-49 26-54 27-208 24-186-3-227 9-300 87-43 46-137 173-137 185 0 3 10 6 23 6s48 12 78 28c61 31 112 91 131 155 7 25 25 53 45 70 79 68 91 152 34 242-17 27-36 65-41 85-13 46-13 100 0 100 6 0 22 11 35 25 30 29 33 82 10 190-61 290-332 508-630 504-38-1-88-5-110-9zm230-165c87-23 168-70 230-136 55-57 108-153 121-216l6-31-153-4c-131-3-161-6-201-25-66-30-133-96-165-162-26-52-28-66-31-210l-4-153-31 6c-63 13-159 66-216 121-66 62-113 143-136 230-88 339 241 668 580 580zm293-619c7-41 28-106 48-147l36-74-24-15c-43-28-68-59-68-85 0-40-26-92-54-110-30-20-127-16-211 8l-50 14-3 175c-2 166-1 176 21 218 35 67 86 90 202 90h91l12-74zm-538-496c132-25 214-88 348-269 101-137 165-199 241-237 31-15 57-29 59-30s-6-20-17-43c-12-22-27-75-33-117-12-74-12-76-38-71-149 30-321 156-424 311-53 80-90 95-140 55-48-38-35-89 52-204l30-39-28-36c-42-54-91-145-110-208l-18-57-337-3-338-2 6 82c9 112 47 272 95 400 135 357 365 522 652 468zm1490-630c0-254 1-252-83-167-54 53-77 104-77 167s23 114 77 168c84 84 83 86 83-168zm-454 63c18-13 41-46 57-83l26-61-45-19c-75-33-165-52-244-54l-75-1-3 29c-8 72 44 166 113 201 42 22 132 16 171-12zm-2346-63v-80h-120-120v80 80h120 120v-80zm1584-184c80-52 154-84 261-111l90-23 112-483c68-295 112-506 112-540 1-68-21-134-56-171l-26-27-17 48c-29 86-99 159-177 186l-38 13-6 279c-5 297-5 297-64 414-58 113-212 233-328 254-21 4-41 14-44 21-12 32 88 201 111 186 6-4 37-24 70-46zm1099-493 185-433-348-490h-138-138l33 68c40 81 56 176 44 252-8 47-203 894-217 941-4 13 9 17 75 23 80 6 230 44 280 71 14 7 29 10 32 7 4-4 90-202 192-439zm-1323 187c118-22 229-99 275-190 37-74 45-138 45-375v-225h-160-160v115c0 179-47 289-158 369-91 67-141 76-417 76h-244l10 32c5 18 9 72 9 120v88h374c209 0 397-4 426-10zm-319-402c50-15 111-67 135-115 16-32 20-70 24-244l5-205 36-72 35-72h-759-759l7 63c17 164 95 400 165 502 47 68 129 124 215 145 52 13 853 12 896-2zm2114-323c256-67 415-329 350-580-48-184-202-326-390-358-197-34-412 76-500 257-19 39-38 86-41 104l-6 32h80 81l24-53c31-69 86-123 156-156 77-36 192-36 266-1 63 31 124 91 156 155 33 68 34 197 2 267-27 60-95 127-156 157-95 46-229 36-311-22-18-12-26-15-21-6 13 22 126 182 143 202 19 22 86 23 167 2zm-1315-243c39-21 87-99 77-125-6-15-27-17-178-17-193 0-231 7-289 58-35 29-70 78-70 97 0 3 96 5 213 5 187 0 217-2 247-18zm1288-89c51-38 67-70 67-133s-16-95-69-134c-43-33-132-29-179 7-20 15-37 32-37 38 0 5 36 9 80 9 73 0 83 3 105 25 33 32 33 78 0 110-22 22-32 25-105 25-44 0-80 4-80 8 0 12 29 37 65 57 39 21 117 15 153-12zm-397-46c-10-9-11-8-5 6 3 10 9 15 12 12s0-11-7-18zm-2460-217c45-106 169-184 289-184s244 78 289 184l22 50h81 81l-7-32c-13-65-66-159-123-219-186-195-500-195-686 0-57 60-110 154-123 219l-6 32h80 81l22-50zm419 41c0-16-51-50-91-63-30-8-48-8-78 0-40 13-91 47-91 63 0 5 57 9 130 9s130-4 130-9z" />
                        </g>
                    </svg>
                @else
                    <svg class="w-4 h-4 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                        fill="currentColor" version="1.0" viewBox="0 0 512 512"
                        xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(0 512) scale(.1 -.1)">
                            <path d="m249 4691c-19-20-29-40-29-60 0-16-14-243-31-503s-28-495-25-522 19-77 35-111c46-100 126-167 236-200l40-12 3-400 2-401-25-6c-58-15-56 21-53-867l3-814 23-45c35-72 75-114 144-151 58-31 70-34 148-34s90 3 148 34c70 38 100 69 140 145l27 51 5 293 5 294 52-64c380-466 1036-731 1654-667 645 65 1211 449 1511 1024l57 110 3-495c3-491 3-495 26-540 35-72 75-114 144-151 58-31 70-34 148-34s90 3 148 34c70 38 100 69 140 145l27 51 3 1938 2 1938-52 52-161-5c-184-6-260-25-384-93-90-50-218-178-268-268-66-120-87-202-93-370l-5-148-86 84c-469 455-1109 646-1736 517-295-61-612-212-835-399l-75-63-6 53c-4 30-15 182-24 339-12 208-21 291-32 308-31 50-98 53-130 6-15-24-15-48 6-387 12-199 24-383 27-409 5-41 3-48-19-62-28-19-159-52-234-60l-53-5v455 455l-25 24c-15 16-36 25-55 25s-40-9-55-25l-25-24v-456-457l-27 6c-16 3-53 8-83 12-69 8-174 40-188 57-7 8-3 125 14 382 30 467 30 450-1 480-33 33-70 32-106-4zm4551-1171v-1040h-320-320v783c0 512 4 804 11 843 29 162 151 321 303 394 91 44 149 57 254 59l72 1v-1040zm-1955 776c271-49 475-131 701-282 126-83 292-236 390-358l64-80v-604-603l25-24c23-24 30-25 150-25 101 0 125-3 125-14 0-34-33-179-60-269-90-288-240-529-465-745-443-426-1063-587-1665-432-403 103-777 372-1019 732l-51 76v382 381l-25 24c-13 14-31 25-40 25-14 0-15 44-13 401l3 402 40 12c111 33 189 100 238 203 29 60 32 77 34 166l1 98 49 50c243 250 626 440 978 487 44 6 94 13 110 15 60 9 352-3 430-18zm-2470-652c200-61 554-55 731 13 15 6 16 1 10-38-9-57-46-112-98-146l-42-28h-256-256l-42 28c-52 34-89 89-98 145-4 23-5 42-3 42s27-7 54-16zm425-764v-400h-80-80v400 400h80 80v-400zm78-1309c-3-739-3-750-24-777-39-53-71-69-134-69s-95 16-134 69c-21 27-21 38-24 777l-2 749h160 160l-2-749zm3920 0c-3-739-3-750-24-777-39-53-71-69-134-69s-95 16-134 69c-21 27-21 38-24 777l-2 749h160 160l-2-749z" />
                            <path d="m2420 3834c-293-38-560-167-763-371-476-475-502-1239-60-1743 495-563 1356-588 1875-52 196 202 313 436 352 703 60 408-69 797-363 1090-182 182-382 293-631 350-83 19-331 33-410 23zm315-169c467-75 826-424 927-900 16-77 16-333 0-410-98-461-436-799-897-897-77-16-333-16-410 0-348 74-626 281-783 580-173 331-175 697-7 1032 214 427 696 672 1170 595z" />
                        </g>
                    </svg>
                @endif
                <span class="{{ $compactOrderDetailUi ? 'text-xs font-medium' : '' }}">{{ $orderDetailOrderTypeLabel }}</span>
            </div>
            <div id="tooltip-order-detail-order-type" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                {{ __('modules.settings.orderType') }}: {{ $orderDetailOrderTypeLabel }}
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </div>

        <div class="flex justify-between gap-2 {{ $compactOrderDetailUi ? 'my-1.5 space-y-0.5 px-0.5' : 'my-4 space-y-1' }} {{ $isStandaloneKotDetailPage && !$isKotSidebarDetailView ? 'sm:px-2' : '' }}">
            <div class="inline-flex gap-4 table-display-container">
                @if ($orderDetail->order_type == 'dine_in')
                    <div id="table-info-section" style="display: {{ $orderDetail->table ? 'flex' : 'none' }};" class="inline-flex items-center gap-2">
                    <div @class([$compactOrderDetailUi ? 'p-2 rounded-md' : 'p-3 rounded-lg', 'tracking-wide bg-skin-base/[0.2] text-skin-base'])>
                            <h3 @class(['font-semibold']) id="table-code">
                                {{ $orderDetail->table->table_code ?? '--' }}
                            </h3>
                        </div>
                        @if(user_can('Update Order'))
                            <x-secondary-button
                                onclick="showTableChangeConfirmationModal()"
                                title="{{ __('modules.order.changeTable') }}"
                                aria-label="{{ __('modules.order.changeTable') }}"
                                data-tooltip-target="tooltip-order-detail-change-table"
                            >
                                <svg width="20" height="20"  viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.74 14H22v-4h-2.26v-.14a8.2 8.2 0 0 0-.82-1.92l1.6-1.6-2.86-2.83-1.6 1.6A8 8 0 0 0 14 4.25V2h-4v2.25a8 8 0 0 0-2.06.86l-1.6-1.6-2.83 2.83 1.6 1.6a8.2 8.2 0 0 0-.82 1.92V10H2v4h2.26v.14a8.2 8.2 0 0 0 .82 1.92l-1.6 1.6 2.83 2.83 1.6-1.6a8 8 0 0 0 2.06.86V22h4v-2.25a8 8 0 0 0 2.06-.86l1.6 1.6 2.83-2.83-1.6-1.6a8.2 8.2 0 0 0 .82-1.92Z"/></svg>
                            </x-secondary-button>
                            <div id="tooltip-order-detail-change-table" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                {{ __('modules.order.changeTable') }}
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        @endif
                    </div>
                    <div id="set-table-section" style="display: {{ $orderDetail->table ? 'none' : 'block' }};">
                        @if(user_can('Update Order'))
                            <button type="button" onclick="showTableChangeConfirmationModal()"
                                class="inline-flex items-center justify-center p-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                title="{{ __('modules.order.setTable') }}" aria-label="{{ __('modules.order.setTable') }}"
                                data-tooltip-target="tooltip-order-detail-set-table">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-table" viewBox="0 0 16 16" aria-hidden="true"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/></svg>
                            </button>
                            <div id="tooltip-order-detail-set-table" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                {{ __('modules.order.setTable') }}
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        @endif
                    </div>
                @else
                    <div @class([$compactOrderDetailUi ? 'p-2 rounded-md' : 'p-3 rounded-lg', 'tracking-wide bg-skin-base/[0.2] text-skin-base'])>
                        <h3 @class(['font-semibold'])>
                            {{ $orderDetail->table->table_code ?? '--' }}
                        </h3>
                    </div>
                @endif

                <div class="mt-2 customer-display-container">
                    <div id="customer-info-section" class="flex items-center gap-2" style="display: {{ $orderDetail->customer ? 'flex' : 'none' }};">
                        <div id="customer-name" class="font-semibold text-gray-700 dark:text-gray-300">{{ $orderDetail->customer->name ?? '' }}</div>
                        @if(user_can('Update Order'))
                            <button type="button" id="edit-customer-btn" onclick="window.showAddCustomerModal({{ json_encode($orderDetail->customer_id ?? null) }}, {{ (int) $orderDetail->id }}, true)" title="{{__('modules.order.updateCustomerDetails')}}" aria-label="{{ __('modules.order.updateCustomerDetails') }}" data-tooltip-target="tooltip-order-detail-edit-customer" class="p-1 text-gray-500 transition-colors bg-gray-100 rounded-md hover:text-gray-700 hover:bg-gray-200 rtl:ml-2 ltr:mr-2 dark:text-gray-300 dark:bg-gray-600 dark:hover:text-gray-200 dark:hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </button>
                            <div id="tooltip-order-detail-edit-customer" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                {{ __('modules.order.updateCustomerDetails') }}
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        @endif
                    </div>

                    <div id="add-customer-section" style="display: {{ $orderDetail->customer ? 'none' : 'block' }};">
                        <button type="button"
                            onclick="window.showAddCustomerModal(null, {{ (int) $orderDetail->id }}, true)"
                            class="inline-flex items-center justify-center p-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            title="{{ __('modules.order.addCustomerDetails') }}" aria-label="{{ __('modules.order.addCustomerDetails') }}"
                            data-tooltip-target="tooltip-order-detail-add-customer">
                            <svg width="16" height="16" viewBox="-2.5 0 32 32" fill="currentColor" class="text-gray-700 dark:text-gray-300" aria-hidden="true">
                                <path d="M18.723 21.788c-1.15-0.48-3.884-1.423-5.565-1.919-0.143-0.045-0.166-0.052-0.166-0.649 0-0.493 0.203-0.989 0.401-1.409 0.214-0.456 0.468-1.224 0.559-1.912 0.255-0.296 0.602-0.88 0.826-1.993 0.196-0.981 0.104-1.338-0.026-1.673-0.013-0.035-0.028-0.070-0.038-0.105-0.049-0.23 0.018-1.425 0.186-2.352 0.116-0.636-0.030-1.989-0.906-3.108-0.553-0.707-1.611-1.576-3.544-1.696l-1.060 0.001c-1.9 0.12-2.96 0.988-3.513 1.695-0.876 1.119-1.021 2.472-0.906 3.108 0.169 0.928 0.236 2.123 0.187 2.348-0.010 0.039-0.025 0.074-0.039 0.11-0.129 0.335-0.221 0.692-0.025 1.673 0.222 1.113 0.57 1.697 0.826 1.993 0.090 0.688 0.344 1.456 0.559 1.912 0.157 0.334 0.23 0.788 0.23 1.431 0 0.597-0.023 0.604-0.157 0.646-1.738 0.513-4.505 1.513-5.537 1.965-0.818 0.351-1.017 0.98-1.017 1.548s0 2.251 0 2.623c0 0.371 0.22 1.006 1.017 1.006 0.613 0 5.518 0 7.746 0 0.668 0 1.098 0 1.098 0h0.192c0 0 0.437 0 1.115 0 2.237 0 7.135 0 7.747 0 0.796 0 1.017-0.634 1.017-1.006s0-2.055 0-2.623-0.392-1.262-1.209-1.613zM18.876 25.98h-17.827v-2.579c0-0.318 0.092-0.46 0.388-0.587 0.994-0.435 3.741-1.426 5.434-1.926 0.889-0.282 0.889-1.070 0.889-1.646 0-0.801-0.106-1.397-0.331-1.878-0.172-0.366-0.392-1.022-0.468-1.601l-0.041-0.312-0.206-0.238c-0.113-0.13-0.396-0.538-0.59-1.513-0.153-0.759-0.085-0.935-0.031-1.076 0.031-0.076 0.058-0.152 0.081-0.237l0.005-0.022 0.005-0.022c0.105-0.495-0.037-1.962-0.181-2.755-0.067-0.365 0.017-1.401 0.7-2.273 0.418-0.534 1.229-1.19 2.722-1.293l0.992-0.001c1.219 0.083 2.145 0.518 2.752 1.294 0.682 0.872 0.766 1.909 0.7 2.275-0.148 0.814-0.287 2.257-0.18 2.758l0.008 0.039 0.011 0.038c0.016 0.054 0.036 0.108 0.056 0.161l0.009 0.026 0.001 0.002c0.059 0.153 0.127 0.326-0.024 1.087-0.196 0.974-0.479 1.384-0.592 1.515l-0.204 0.237-0.042 0.31c-0.076 0.578-0.296 1.237-0.468 1.603-0.247 0.525-0.5 1.157-0.5 1.856 0 0.577 0 1.367 0.918 1.655 1.641 0.485 4.345 1.416 5.448 1.877 0.418 0.179 0.574 0.493 0.574 0.649l-0.006 2.579z"/>
                                <path d="M23.078 14.441v-4.185h-1.049v4.185h-4.186v1.049h4.186v4.185h1.049v-4.185h4.185v-1.049z"/>
                            </svg>
                        </button>
                        <div id="tooltip-order-detail-add-customer" role="tooltip"
                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            {{ __('modules.order.addCustomerDetails') }}
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                    <div class="{{ $compactOrderDetailUi ? 'text-[11px]' : 'text-xs' }} font-medium text-gray-600 dark:text-gray-400">{{ $orderDetail->date_time->timezone(timezone())->format(dateFormat() . ' ' . timeFormat()) }}</div>
                </div>

            </div>
            <div>
                <span title="{{ __('modules.order.' . $orderDetail->status) }}"
                    @class(['font-medium px-2 py-0.5 rounded uppercase tracking-wide whitespace-nowrap cursor-default ',
                'text-xs' => $compactOrderDetailUi,
                'text-sm' => !$compactOrderDetailUi,
                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 border border-gray-400' => ($orderDetail->status == 'draft'),
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-400 border border-yellow-400' => ($orderDetail->status == 'kot'),
                'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-400 border border-blue-400' => ($orderDetail->status == 'billed'),
                'bg-green-100 text-green-800 dark:bg-gray-700 dark:text-green-400 border border-green-400' => ($orderDetail->status == 'paid'),
                'bg-amber-100 text-amber-800 dark:bg-amber-700 dark:text-amber-400 border border-amber-400' => ($orderDetail->status == 'payment_due'),
                'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-400 border border-red-400' => ($orderDetail->status == 'canceled'),
                ])>
                    @lang('modules.order.' . $orderDetail->status)
                </span>
            </div>

        </div>

        @if ($orderDetail->order_status->value === 'cancelled')
            <span class="inline-block px-2 py-1 my-2 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                @lang('modules.order.info_cancelled')
                            </span>
                                @else
        <div class="mb-0 rounded-lg border border-gray-200 bg-white pb-1.5 pt-1 px-1.5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            @php
                $statuses = \App\Enums\OrderStatus::progressStepsFor($orderDetail->order_type);
                $currentIndex = \App\Enums\OrderStatus::progressIndex($orderDetail->order_status->value, $orderDetail->order_type);
                $nextIndex = min($currentIndex + 1, count($statuses) - 1);
                $progressStatusValue = \App\Enums\OrderStatus::progressStatusForOrderType($orderDetail->order_status->value, $orderDetail->order_type);
                $progressStatus = \App\Enums\OrderStatus::from($progressStatusValue);
                $progressPercent = count($statuses) > 1
                    ? ($currentIndex / (count($statuses) - 1)) * 100
                    : 0;
            @endphp

            @if ($orderDetail->order_status->value === 'canceled')
                <div class="flex items-center justify-center">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">
                        {{ __('modules.order.orderCancelled') }}
                    </h3>

                </div>
            @else
                <div class="flex flex-col gap-2 sm:gap-2.5">
                    <div class="flex items-center gap-2 min-w-0 w-full text-gray-900 dark:text-white">
                        <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wide shrink-0">
                            {{ __('modules.order.orderStatus') }}
                        </h3>
                        <div class="flex flex-1 justify-center min-w-0 px-1">
                            <span id="order-status-badge" data-status="{{ $orderDetail->order_status->value }}" @class([
                                'inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full border leading-snug shrink-0 max-w-full',
                                $progressStatus->badgeClasses(),
                            ])>
                                <span class="truncate">{{ $progressStatus->translatedLabel() }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-end gap-1.5 shrink-0 max-w-[50%] sm:max-w-[45%]">
                            @if(user_can('Update Order'))
                                @if($orderDetail->order_status->value === 'placed')
                                    <button id="order-status-cancel-btn" type="button" onclick="showCancelOrderModal()" class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium bg-red-600 border border-transparent rounded-md text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-1 focus:ring-red-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 dark:text-gray-200 shrink-0">
                                        <span>{{ __('modules.order.cancelOrder') }}</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif

                                @if($currentIndex < count($statuses) - 1)
                                    <x-secondary-button id="order-status-next-btn" class="inline-flex items-center !px-2 !py-0.5 !text-[10px] !font-medium gap-1 border-gray-300 shadow-sm dark:border-gray-600 shrink-0 max-w-full" onclick="updateOrderStatus('{{ $statuses[$nextIndex] }}')">
                                        <span id="order-status-next-label" class="truncate">{{ __('modules.order.moveTo') }} {{ App\Enums\OrderStatus::from($statuses[$nextIndex])->translatedLabel() }}</span>
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13 7 5 5m0 0-5 5m5-5H6"/></svg>
                                    </x-secondary-button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="relative w-full pt-1 pb-0.5 overflow-visible">
                        <div class="absolute top-1/2 left-[2%] right-[2%] z-0 h-0.5 -translate-y-1/2 bg-gray-200 dark:bg-gray-600">
                            <div id="order-status-progress-bar" class="h-full bg-skin-base/60 dark:bg-skin-base/50 transition-all duration-500 rounded-full" style="width: {{ $progressPercent }}%;"></div>
                        </div>

                        <div id="order-status-steps" class="relative z-10 flex w-full items-center justify-between">
                            @foreach($statuses as $index => $status)
                                @php
                                    $isCompleted = $index < $currentIndex;
                                    $isCurrent = $index === $currentIndex;
                                    $isNext = $index === $currentIndex + 1;
                                @endphp
                                @php
                                    $stepStatus = App\Enums\OrderStatus::from($status);
                                    $statusStepTooltipId = 'tooltip-order-detail-status-' . $orderDetail->id . '-' . $index;
                                    $statusStepLabel = $stepStatus->translatedLabel();
                                @endphp
                                <div class="order-status-step flex flex-1 justify-center px-0.5 min-w-0">
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
                                            data-icon-base="order-status-icon flex size-8 shrink-0 items-center justify-center rounded-full relative z-10 bg-white shadow-sm dark:bg-gray-900 [&_.order-status-step-svg]:text-current"
                                        >
                                            {!! $stepStatus->icon() !!}
                                        </div>
                                    </div>
                                    <div id="{{ $statusStepTooltipId }}" role="tooltip"
                                        class="hidden pointer-events-none px-2 py-1 text-[10px] font-medium text-white bg-gray-900 rounded-lg shadow-sm dark:bg-gray-700 whitespace-nowrap">
                                        {{ $statusStepLabel }}
                                    </div>

                                    <span class="order-status-label sr-only">
                                        {{ $statusStepLabel }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif
        </div>
        @endif

        @if ($orderDetail)
        <div class="flex flex-col rounded ">
            <table class="flex-1 min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                <thead class="bg-gray-100 dark:bg-gray-700">
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
                            class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.order.price')
                        </th>
                        <th scope="col"
                            class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.order.amount')
                        </th>
                        @if (user_can('Delete Order') && $orderDetail->status !== 'paid')
                        <th scope="col"
                            class="p-2 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                            @lang('app.action')
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

                    @forelse ($orderDetail->items->load('modifierOptions') as $key => $item)
                    @php
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
                        $expectedAmount = (float) $displayPrice * (int) ($item->quantity ?? 1);
                        $actualAmount = (float) ($item->amount ?? 0);
                        // Treat item as free from stamp ONLY when flag is set AND amount is actually zero (or nearly zero)
                        $isFreeItemFromStamp = (bool) ($item->is_free_item_from_stamp ?? false) && $actualAmount <= 0.0001;
                        $stampDiscountAmount = !$isFreeItemFromStamp ? max(0, $expectedAmount - $actualAmount) : 0;
                        $hasStampDiscount = $stampDiscountAmount > 0.01;
                    @endphp
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" data-order-item-id="{{ $item->id }}">
                        <td class="flex flex-col p-2 mr-12 lg:min-w-28">
                            <div class="inline-flex items-center gap-2 text-xs text-gray-900 dark:text-white">
                                {{ $item->menuItem->item_name }}
                                @if ($isFreeItemFromStamp)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        @lang('app.freeItem')
                                    </span>
                                @elseif($hasStampDiscount)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        @lang('app.stampDiscount') (-{{ currency_format($stampDiscountAmount, restaurant()->currency_id) }})
                                    </span>
                                @endif
                            </div>
                            @if (isset($item->menuItemVariation))
                            <div class="inline-flex items-center text-xs text-gray-600 dark:text-white">
                                {{ $item->menuItemVariation->variation }}
                            </div>
                            @endif
                            @if ($item->modifierOptions->isNotEmpty())
                                <div class="mt-1 text-xs text-gray-600 dark:text-white">
                                    @foreach ($item->modifierOptions as $modifier)
                                        <div class="flex items-center justify-between text-xs mb-1 py-0.5 px-1 border-l-2 border-blue-500 bg-gray-200 dark:bg-gray-900 rounded-md">
                                            <span class="text-gray-900 dark:text-white">{{ $modifier->name ?? $modifier->pivot->modifier_option_name }}</span>
                                            <span class="text-gray-600 dark:text-gray-300">{{ currency_format($modifier->pivot->modifier_option_price ?? $modifier->price, restaurant()->currency_id) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="p-2 text-xs text-center text-gray-900 dark:text-gray-200 whitespace-nowrap">
                            {{ $item->quantity }}
                        </td>
                        <td class="p-2 text-xs font-medium text-right text-gray-700 whitespace-nowrap dark:text-white">
                            {{ currency_format($displayPrice, restaurant()->currency_id) }}
                        </td>
                        <td class="p-2 text-xs font-medium text-right text-gray-900 whitespace-nowrap dark:text-white">
                            @if ($isFreeItemFromStamp)
                                <div class="flex flex-col items-end">
                                    <span class="text-green-600 dark:text-green-400 font-semibold">
                                        {{ currency_format(0, restaurant()->currency_id) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 line-through">
                                        {{ currency_format($expectedAmount, restaurant()->currency_id) }}
                                    </span>
                                </div>
                            @elseif($hasStampDiscount)
                                <div class="flex flex-col items-end">
                                    <span class="text-blue-600 dark:text-blue-400 font-semibold">
                                        {{ currency_format($item->amount, restaurant()->currency_id) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 line-through">
                                        {{ currency_format($expectedAmount, restaurant()->currency_id) }}
                                    </span>
                                </div>
                            @else
                                {{ currency_format($item->amount, restaurant()->currency_id) }}
                            @endif
                        </td>
                        @if (user_can('Delete Order') && $orderDetail->status !== 'paid')
                        <td class="text-right whitespace-nowrap">
                            <button type="button" onclick="deleteOrderItem('{{ $item->id }}')" class="p-1 text-gray-800 border rounded dark:text-gray-400 dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
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
        @endif

        </div>

        @if ($orderDetail)
        <div @class([
            'shrink-0 bg-white dark:bg-gray-800',
            $compactOrderDetailUi ? 'pt-1.5 mt-1' : 'pt-3 mt-2',
            $isStandaloneKotDetailPage ? '' : '-mx-4 px-4',
            'border-t border-gray-200 dark:border-gray-700 pb-1' => !$shouldStickyAddPayment,
            'z-20 border-t border-gray-100 pt-1 pb-[calc(env(safe-area-inset-bottom,0px)+0.5rem)]' => $shouldStickyAddPayment,
        ])>
            <div @class([
                'w-full h-auto text-center select-none rounded',
                $compactOrderDetailUi ? 'p-2 space-y-1.5 rounded-lg' : 'p-4 space-y-4 rounded-lg',
                'bg-gray-50 dark:bg-gray-700',
                'p-1 space-y-1 rounded' => $shouldStickyAddPayment,
            ])>
                @if (count($orderDetail->items) > 0 && $orderDetail->status !== 'paid' && user_can('Update Order'))
                <div class="flex text-left">
                    @if (user_can('Add Discount on POS'))
                        <button type="button" onclick="showAddDiscountModal()" class="inline-flex items-center px-1 py-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 gap-1 leading-none">
                            <svg class="h-4 w-4 text-current me-1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                <path d="m7.25 14.25-5.5-5.5 7-7h5.5v5.5z"/>
                                <circle cx="11" cy="5" r=".5" fill="#000"/>
                            </svg>
                            @lang('modules.order.addDiscount')
                        </button>
                    @endif
                </div>
            @endif
                <div class="flex justify-between {{ $compactOrderDetailUi ? 'text-xs' : 'text-sm' }} text-gray-500 dark:text-neutral-400">
                    <div>
                        @lang('modules.order.totalItem')
                    </div>
                    <div id="order-detail-items-count">
                        {{ count($orderDetail->items) }}
                    </div>
                </div>
                <div class="flex justify-between {{ $compactOrderDetailUi ? 'text-xs' : 'text-sm' }} text-gray-500 dark:text-neutral-400">
                    <div class="flex items-center gap-2">
                        <span>@lang('modules.order.subTotal')</span>
                        @php
                            $stampDiscountAmount = (float)($orderDetail->stamp_discount_amount ?? 0);
                            $hasFreeStampItems = $orderDetail->items()->where('is_free_item_from_stamp', true)->exists();
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
                    <div id="order-detail-subtotal">
                        {{ currency_format($orderDetail->sub_total, restaurant()->currency_id) }}
                    </div>
                </div>

                @if ($orderDetail->loyalty_points_redeemed > 0 && $orderDetail->loyalty_discount_amount > 0)
                    <div class="flex justify-between {{ $textSize ?? 'text-sm' }} text-blue-600 dark:text-blue-400">
                        <div>
                            @lang('loyalty::app.loyaltyDiscount') ({{ number_format($orderDetail->loyalty_points_redeemed) }} @lang('loyalty::app.points'))
                        </div>
                        <div>
                            -{{ currency_format($orderDetail->loyalty_discount_amount, $currencyId ?? restaurant()->currency_id) }}
                        </div>
                    </div>
                @endif

                @php
                    $_pdDiscDecimals = (int) (optional(currency_format_setting())->no_of_decimal ?? 2);
                    $_pdShowDiscountRow = round((float) ($orderDetail->discount_amount ?? 0), $_pdDiscDecimals) > 0
                        && (int) ($orderDetail->loyalty_points_redeemed ?? 0) === 0;
                @endphp
                <div id="discount-row" class="flex justify-between text-sm text-green-500 dark:text-green-400" style="display: {{ $_pdShowDiscountRow ? 'flex' : 'none' }};">
                    <div class="inline-flex items-center gap-x-1">
                        @lang('modules.order.discount')
                        <span id="discount-type-display">
                            @if ($orderDetail->discount_type == 'percent')
                                ({{ rtrim(rtrim($orderDetail->discount_value), '.') }}%)
                            @endif
                        </span>
                        @if(user_can('Add Discount on POS') && user_can('Update Order'))
                            <span class="text-red-500 cursor-pointer hover:scale-110 active:scale-100" onclick="removeCurrentDiscount()" title="@lang('app.remove')">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                    <div id="discount-display">
                        @if($_pdShowDiscountRow)-{{ currency_format($orderDetail->discount_amount, restaurant()->currency_id) }}@endif
                    </div>
                </div>

                @foreach ($extraCharges as $charge)
                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400" data-charge-id="{{ $charge->id }}">
                    <div class="inline-flex items-center gap-x-1">{{ $charge->charge_name }}
                        @if ($charge->charge_type == 'percent')
                            ({{ $charge->charge_value }}%)
                        @endif
                        @if(user_can('Update Order'))
                        <span class="text-red-500 cursor-pointer hover:scale-110 active:scale-100"
                            onclick="removeExtraCharge({{ $charge->id }}, '{{ $orderType }}')" title="@lang('app.remove')">
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
                            $chargeBase = $orderDetail->sub_total
                                - ($orderDetail->discount_amount ?? 0)
                                - ($orderDetail->loyalty_discount_amount ?? 0);
                        @endphp
                        {{ currency_format($charge->getAmount($chargeBase), restaurant()->currency_id) }}
                    </div>
                </div>
                @endforeach

                @if ($orderDetail->tip_amount > 0)
                <div class="flex justify-between {{ $compactOrderDetailUi ? 'text-xs' : 'text-sm' }} text-gray-500 dark:text-neutral-400">
                    <div>
                        @lang('modules.order.tip')
                    </div>
                    <div>
                        {{ currency_format($orderDetail->tip_amount, restaurant()->currency_id) }}
                    </div>
                </div>
                @endif

                @if ($orderType === 'delivery' && !is_null($deliveryFee))
                <div class="flex justify-between {{ $compactOrderDetailUi ? 'text-xs' : 'text-sm' }} text-gray-500 dark:text-neutral-400">
                        <div>
                            @lang('modules.delivery.deliveryFee')
                        </div>
                        <div>
                            @if($deliveryFee > 0)
                                {{ currency_format($deliveryFee, restaurant()->currency_id) }}
                            @else
                                <span class="font-medium text-green-500">@lang('modules.delivery.freeDelivery')</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($taxMode == 'order')
                    @php
                        $discountedSubtotal = $orderDetail->sub_total
                            - ($orderDetail->discount_amount ?? 0)
                            - ($orderDetail->loyalty_discount_amount ?? 0);
                        $taxBaseForDisplay = $orderDetail->tax_base ?? $discountedSubtotal;
                    @endphp
                    @foreach ($orderDetail->taxes as $item)
                        @if (!$item->tax)
                            @continue
                        @endif
                <div class="flex justify-between {{ $compactOrderDetailUi ? 'text-xs' : 'text-sm' }} text-gray-500 dark:text-neutral-400">
                            <div>
                                {{ $item->tax->tax_name }} ({{ $item->tax->tax_percent }}%)
                            </div>
                            <div>
                                @php
                                    $taxAmount = ($item->tax->tax_percent / 100) * $taxBaseForDisplay;
                                @endphp
                                {{ currency_format($taxAmount, restaurant()->currency_id) }}
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between text-sm text-gray-500 dark:text-neutral-400">
                        <div>@lang('modules.order.totalTax')</div>
                        <div id="order-detail-total-tax">{{ currency_format($orderDetail->total_tax_amount ?? 0, restaurant()->currency_id) }}</div>
                    </div>
                @else
                    @php
                    // Show item-wise tax breakdown using actual order items data
                    $taxTotals = [];
                    foreach ($orderItemTaxDetails as $item) {
                        $qty = $item['qty'] ?? 1;
                        if (!empty($item['tax_breakup'])) {
                            foreach ($item['tax_breakup'] as $taxName => $taxInfo) {
                                if (!isset($taxTotals[$taxName])) {
                                    $taxTotals[$taxName] = [
                                        'percent' => $taxInfo['percent'],
                                        'amount' => 0
                                    ];
                                }
                                $taxTotals[$taxName]['amount'] += $taxInfo['amount'] * $qty;
                            }
                        }
                    }
                @endphp
                @foreach ($taxTotals as $taxName => $taxInfo)
                    <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400">
                        <div>
                            {{ $taxName }} ({{ $taxInfo['percent'] }}%)
                        </div>
                        <div>
                            {{ currency_format($taxInfo['amount'], restaurant()->currency_id) }}
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400">
                    <div>
                        @lang('modules.order.totalTax')
                        @if($taxMode === 'item')
                            <span
                                class="ml-2 px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                                @lang(restaurant()->tax_inclusive ? 'modules.settings.taxInclusive' : 'modules.settings.taxExclusive')
                            </span>
                        @endif
                    </div>
                    <div id="order-detail-total-tax">
                        {{ currency_format($totalTaxAmount, restaurant()->currency_id) }}
                    </div>
                </div>
                @endif

                <div class="flex justify-between {{ $compactOrderDetailUi ? 'font-semibold text-base' : 'font-medium' }} dark:text-neutral-300">
                    <div>
                        @lang('modules.order.total')
                    </div>
                    <div id="order-detail-total">
                        {{ currency_format($orderDetail->total, restaurant()->currency_id) }}
                    </div>
                </div>
            </div>

            <div @class([
                'w-full h-auto text-center select-none',
                'pt-3 pb-4' => !$shouldStickyAddPayment,
                'mt-2 pt-2 pb-2' => $shouldStickyAddPayment,
            ])>
                <div class="flex flex-col gap-2">

                    @if (in_array($orderDetail->status, ['billed', 'payment_due']) && user_can('Update Order'))
                    <button type="button" onclick="showPaymentModalForOrder({{ $orderDetail->id }}, this)" class="pos-order-action-btn w-full p-2 text-white bg-green-600 rounded">
                        <span data-btn-text>@lang('modules.order.addPayment')</span>
                        <span data-btn-loading class="hidden">
                            <svg class="animate-spin inline-flex -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            @lang('modules.order.addPayment')
                        </span>
                    </button>
                    @endif

                    @if($orderDetail->status == 'paid')
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="printOrder({{ $orderDetail->id }}, this)" class="pos-order-action-btn inline-flex items-center justify-center w-full p-2 text-gray-800 border border-gray-300 rounded dark:border-gray-600 dark:text-gray-200 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 gap-x-1">
                            <span data-btn-text class="inline-flex items-center gap-x-1">
                            <svg class="w-5 h-5 text-current" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M16.444 18H19a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2.556M17 11V5a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v6h10ZM7 15h10v4a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4Z"/>
                            </svg>
                            @if($orderDetail->split_type && $orderDetail->splitOrders()->where('status', 'paid')->count() > 0)
                                @lang('modules.order.printSplits')
                            @else
                                @lang('app.print')
                            @endif
                            </span>
                            <span data-btn-loading class="hidden">
                                <svg class="animate-spin inline-block -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                @lang('app.print')
                            </span>
                        </button>
                        <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center w-full p-2 text-gray-700 border border-gray-300 rounded dark:border-gray-600 dark:text-gray-200 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 gap-x-1">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            @lang('app.close')
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
    </div>
    <div id="confirmDeleteModal" style="display: none;" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" onclick="closeCancelOrderModal()">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto">
            <div class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">@lang('modules.order.cancelOrder')</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('modules.order.cancelOrderMessageUndone')</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
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
                <label for="cancelReason" class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('modules.settings.selectCancelReason') }}</label>
                <select id="cancelReason" class="block w-full mt-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="">{{ __('modules.settings.selectCancelReason') }}</option>
                    @foreach ($cancelReasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                    @endforeach
                </select>
                <div id="cancelReasonError" class="mt-2 text-sm text-red-600 dark:text-red-400" style="display: none;"></div>
            </div>

                <!-- Custom Reason Textarea -->
                <textarea
                    id="cancelReasonText"
                    rows="4"
                    class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none dark:border-gray-600 rounded-xl focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="@lang('modules.settings.enterCancelReason')"
                ></textarea>
            </div>
            <div class="px-6 py-4 bg-gray-100 dark:bg-gray-800 text-right">
                <button type="button" onclick="closeCancelOrderModal()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('app.cancel') }}
                </button>
                <button type="button" onclick="cancelOrder()" class="ml-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    @lang('modules.order.cancelOrder')
                </button>
            </div>
        </div>
    </div>

    <!-- Table Change Confirmation Modal -->
    <div id="showTableChangeConfirmationModal" style="display: none;" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" onclick="cancelTableChange()">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
            <div class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">@lang('modules.order.changeTable')</h3>
                </div>
            </div>
            <div class="px-6 py-4">
            <div class="space-y-4">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-amber-100" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.order.confirmTableChange')
                    </h3>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <p>@lang('modules.order.currentTable'): <strong>{{ $orderDetail->table->table_code ?? '--' }}</strong></p>
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
            <div class="px-6 py-4 bg-gray-100 dark:bg-gray-800">
                <div class="flex justify-end gap-2 w-full">
                    <x-button-cancel type="button" onclick="cancelTableChange()" >
                        @lang('app.cancel')
                    </x-button-cancel>
                    <x-button type="button" onclick="confirmTableChange()">
                        @lang('modules.order.changeTable')
                    </x-button>
                </div>
            </div>
        </div>
    </div>

