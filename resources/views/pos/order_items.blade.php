@php
    $isShowOrderDetail = request()->boolean('show-order-detail');
    $isLivewirePos = isset($this) && $this instanceof \Livewire\Component;
    $posCtxOrderIdForCustomerModal = isset($orderDetail) && $orderDetail?->id
        ? (int) $orderDetail->id
        : (isset($orderID) && $orderID ? (int) $orderID : null);
@endphp

<div id="pos-order-items-panel" class="w-full max-w-full flex flex-1 min-h-0 flex-col overflow-hidden bg-white dark:border-gray-700 px-3 pt-3 dark:bg-gray-800 rounded-md lg:rounded-none {{ $isShowOrderDetail ? 'lg:mx-auto lg:max-w-5xl lg:px-4 lg:pt-4' : 'lg:pr-4' }}">

    <style>
        @media (max-width: 1023px) {
            #pos-order-items-panel {
                width: 100%;
                max-width: 100%;
                height: 100%;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                padding-bottom: 0;
                box-sizing: border-box;
            }

            #pos-cart-scroll {
                flex: 1 1 auto;
                min-height: 0;
                padding-right: 0;
                padding-bottom: 0.5rem;
            }

            #pos-cart-action-footer {
                margin-top: auto;
                flex-shrink: 0;
                width: 100%;
                margin-right: 0;
                padding-bottom: max(0.75rem, env(safe-area-inset-bottom, 0px));
                background: rgb(255 255 255);
                border-top-width: 1px;
                box-shadow: 0 -4px 16px rgb(0 0 0 / 0.08);
            }

            [data-pos-cart-order-actions] {
                padding-bottom: 0.25rem;
            }

            .dark #pos-cart-action-footer {
                background: rgb(31 41 55);
            }

            #pos-mobile-action-dock {
                position: static;
                width: 100%;
                max-width: 100%;
                margin: 0;
                box-sizing: border-box;
                padding: 0;
                background: transparent;
                border-top: 0;
                box-shadow: none;
            }

            [data-pos-cart-order-actions] .pos-order-action-btn,
            [data-pos-cart-order-actions] .pos-new-kot-link {
                min-height: 2.75rem;
            }

            [data-pos-cart-order-actions] #saveBillPrintBtnText {
                font-size: 0.625rem;
                line-height: 1.1;
            }
        }

        @media (min-width: 1024px) {
            #pos-cart-action-footer {
                position: static;
                box-shadow: none;
                padding-bottom: 0;
            }

            #pos-mobile-action-dock {
                position: static;
                box-shadow: none;
                border-top: 0;
                padding: 0;
                background: transparent;
            }
        }

        .pos-hover-scrollbar {
            scrollbar-width: thin;
            -ms-overflow-style: auto;
        }

        .pos-hover-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .pos-hover-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(107, 114, 128, 0.45);
            border-radius: 9999px;
        }

        .pos-hover-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>

    @include('pos.partials.running-order-banner')

    <div class="shrink-0 border-b border-gray-200 dark:border-gray-700 pb-2 mb-2">
        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1.5 text-xs">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 min-w-0 flex-1">
                <div class="inline-flex flex-wrap items-center gap-2 min-w-0">
                    <span class="text-gray-500 dark:text-gray-400">@lang('modules.settings.orderType')</span>
                    @unless($isShowOrderDetail)
                        @if ($allowOrderTypeChange ?? true)
                        <button
                            type="button"
                            onclick="changeOrderType()"
                            class="cursor-pointer font-semibold underline underline-offset-2 text-gray-900 dark:text-white hover:text-skin-base dark:hover:text-skin-base transition-colors"
                            title="{{ __('app.change') }}"
                            aria-label="{{ __('app.change') }}"
                        >
                            {{ \App\Models\OrderType::find($orderTypeId)?->order_type_name ?? ucfirst($orderType) }}
                        </button>
                        @else
                        <span class="font-semibold text-gray-900 dark:text-white">{{ \App\Models\OrderType::find($orderTypeId)?->order_type_name ?? ucfirst($orderType) }}</span>
                        @endif
                    @else
                        <span class="font-semibold underline underline-offset-2 text-gray-900 dark:text-white">{{ \App\Models\OrderType::find($orderTypeId)?->order_type_name ?? ucfirst($orderType) }}</span>
                    @endunless
                    @unless($isShowOrderDetail)
                        @if ($allowOrderTypeChange ?? true)
                        <button type="button" onclick="changeOrderType()"
                            class="cursor-pointer inline-flex items-center justify-center p-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            title="{{ __('app.change') }}" aria-label="{{ __('app.change') }}" data-tooltip-target="tooltip-change-order-type-order">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.83 9H3.1z"/>
                            </svg>
                        </button>
                        <div id="tooltip-change-order-type-order" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            {{ __('app.change') }}
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                        @endif
                    @endunless
                </div>

                @if ($orderType == 'dine_in')
                    <div class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-xs dark:text-gray-300 table-display-container shrink-0 min-w-0">
                        <div id="table-info-section" style="display: {{ $tableNo ? 'flex' : 'none' }};" class="inline-flex items-center gap-1.5 min-h-8">
                            <svg fill="currentColor" class="w-4 h-4 shrink-0 self-center text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.999 44.999" xml:space="preserve"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><path d="m42.558 23.378 2.406-10.92a1.512 1.512 0 0 0-2.954-.652l-2.145 9.733h-9.647a1.512 1.512 0 0 0 0 3.026h.573l-3.258 7.713a1.51 1.51 0 0 0 1.393 2.102c.59 0 1.15-.348 1.394-.925l2.974-7.038 4.717.001 2.971 7.037a1.512 1.512 0 1 0 2.787-1.177l-3.257-7.713h.573a1.51 1.51 0 0 0 1.473-1.187m-28.35 1.186h.573a1.512 1.512 0 0 0 0-3.026H5.134L2.99 11.806a1.511 1.511 0 1 0-2.954.652l2.406 10.92a1.51 1.51 0 0 0 1.477 1.187h.573L1.234 32.28a1.51 1.51 0 0 0 .805 1.98 1.515 1.515 0 0 0 1.982-.805l2.971-7.037 4.717-.001 2.972 7.038a1.514 1.514 0 0 0 1.982.805 1.51 1.51 0 0 0 .805-1.98z"/><path d="M24.862 31.353h-.852V18.308h8.13a1.513 1.513 0 1 0 0-3.025H12.856a1.514 1.514 0 0 0 0 3.025h8.13v13.045h-.852a1.514 1.514 0 0 0 0 3.027h4.728a1.513 1.513 0 1 0 0-3.027"/></svg>
                            <span id="table-code" class="font-medium leading-none self-center">{{ $tableNo }}</span>
                            @if (user_can('Update Order'))
                                <button type="button" onclick="showTableChangeConfirmationModal()"
                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    title="{{ __('modules.order.changeTable') }}" aria-label="{{ __('modules.order.changeTable') }}" data-tooltip-target="tooltip-change-table-order">
                                    <svg width="16" height="16"  viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.74 14H22v-4h-2.26v-.14a8.2 8.2 0 0 0-.82-1.92l1.6-1.6-2.86-2.83-1.6 1.6A8 8 0 0 0 14 4.25V2h-4v2.25a8 8 0 0 0-2.06.86l-1.6-1.6-2.83 2.83 1.6 1.6a8.2 8.2 0 0 0-.82 1.92V10H2v4h2.26v.14a8.2 8.2 0 0 0 .82 1.92l-1.6 1.6 2.83 2.83 1.6-1.6a8 8 0 0 0 2.06.86V22h4v-2.25a8 8 0 0 0 2.06-.86l1.6 1.6 2.83-2.83-1.6-1.6a8.2 8.2 0 0 0 .82-1.92Z"/></svg>
                                </button>
                                <div id="tooltip-change-table-order" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ __('modules.order.changeTable') }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @endif
                        </div>
                        <div id="set-table-section" class="inline-flex items-center" style="display: {{ $tableNo ? 'none' : 'inline-flex' }};">
                            @if(user_can('Update Order'))
                                <button type="button" onclick="showTableChangeConfirmationModal()"
                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    title="{{ __('modules.order.setTable') }}" aria-label="{{ __('modules.order.setTable') }}" data-tooltip-target="tooltip-set-table-order">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-table" viewBox="0 0 16 16" aria-hidden="true"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/></svg>
                                </button>
                                <div id="tooltip-set-table-order" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ __('modules.order.setTable') }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @endif
                        </div>
                        @if (user_can('Update Order') && !$isShowOrderDetail)
                            <button type="button" onclick="showMergeTableModal()"
                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                title="{{ __('modules.order.mergeTables') }}" aria-label="{{ __('modules.order.mergeTables') }}" data-tooltip-target="tooltip-merge-tables-order">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16" aria-hidden="true"><path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/></svg>
                                
                            </button>
                            <div id="tooltip-merge-tables-order" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                {{ __('modules.order.mergeTables') }}
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        @endif
                    </div>
                    <div class="customer-display-container shrink-0 min-w-0">
                        <div id="customer-info-section" class="flex items-center gap-2" style="display: {{ $customer ? 'flex' : 'none' }};">
                            <div id="customer-name" class="font-semibold text-gray-700 dark:text-gray-300 text-xs truncate max-w-[10rem]">{{ $customer->name ?? '' }}</div>
                            @if(user_can('Update Order'))
                                <button type="button" id="edit-customer-btn" onclick="window.showAddCustomerModal({{ json_encode($customer->id ?? null) }}, @json($posCtxOrderIdForCustomerModal), true)" title="{{__('modules.order.updateCustomerDetails')}}" class="p-1 text-gray-500 transition-colors bg-gray-100 rounded-md hover:text-gray-700 hover:bg-gray-200 rtl:ml-2 ltr:mr-2 dark:text-gray-300 dark:bg-gray-600 dark:hover:text-gray-200 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                                </button>
                                <button id="remove-customer-btn" type="button" onclick="clearSelectedCustomer()" title="{{ __('app.remove') }}" aria-label="{{ __('app.remove') }}" data-tooltip-target="tooltip-remove-customer-details-order-dinein" class="p-1 text-red-500 transition-colors bg-red-50 rounded-md hover:text-red-700 hover:bg-red-100 dark:text-red-300 dark:bg-red-900/30 dark:hover:text-red-200 dark:hover:bg-red-900/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
                                </button>
                                <div id="tooltip-remove-customer-details-order-dinein" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ __('app.remove') }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @endif
                        </div>

                        <div id="add-customer-section" style="display: {{ $customer ? 'none' : 'block' }};">
                            <a href="javascript:;"
                                onclick="window.showAddCustomerModal(null, @json($posCtxOrderIdForCustomerModal), true); return false;"
                                class="text-xs underline underline-offset-2 dark:text-gray-300 whitespace-nowrap">&plus; @lang('modules.order.addCustomerDetails')</a>
                        </div>
                    </div>
                @else
                    <div class="customer-display-container shrink-0 min-w-0">
                        <div id="customer-info-section" class="flex items-center gap-2" style="display: {{ $customer ? 'flex' : 'none' }};">
                            <div id="customer-name" class="font-semibold text-gray-700 dark:text-gray-300 text-xs truncate max-w-[10rem]">{{ $customer->name ?? '' }}</div>
                            @if(user_can('Update Order'))
                                <button type="button" id="edit-customer-btn" onclick="window.showAddCustomerModal({{ json_encode($customer->id ?? null) }}, @json($posCtxOrderIdForCustomerModal), true)" title="{{__('modules.order.updateCustomerDetails')}}" class="p-1 text-gray-500 transition-colors bg-gray-100 rounded-md hover:text-gray-700 hover:bg-gray-200 rtl:ml-2 ltr:mr-2 dark:text-gray-300 dark:bg-gray-600 dark:hover:text-gray-200 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                                </button>
                                <button id="remove-customer-btn" type="button" onclick="clearSelectedCustomer()" title="{{ __('app.remove') }}" aria-label="{{ __('app.remove') }}" data-tooltip-target="tooltip-remove-customer-details-order" class="p-1 text-red-500 transition-colors bg-red-50 rounded-md hover:text-red-700 hover:bg-red-100 dark:text-red-300 dark:bg-red-900/30 dark:hover:text-red-200 dark:hover:bg-red-900/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
                                </button>
                                <div id="tooltip-remove-customer-details-order" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ __('app.remove') }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @endif
                        </div>

                        <div id="add-customer-section" style="display: {{ $customer ? 'none' : 'block' }};">
                            <a href="javascript:;"
                                onclick="window.showAddCustomerModal(null, @json($posCtxOrderIdForCustomerModal), true); return false;"
                                class="text-xs underline underline-offset-2 dark:text-gray-300 whitespace-nowrap">&plus; @lang('modules.order.addCustomerDetails')</a>
                        </div>
                    </div>
                @endif
            </div>
            <div data-pos-order-number-badge class="inline-flex items-center gap-1.5 font-medium text-gray-800 dark:text-neutral-200 text-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="w-4 h-4 bi bi-receipt shrink-0" viewBox="0 0 16 16" aria-hidden="true">
                    <path
                        d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                    <path
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                </svg>
                <span id="order-number-display">
                    @if(!isOrderPrefixEnabled())
                        @lang('modules.order.orderNumber') #<span class="order-number-value">{{ $orderNumber }}</span>
                    @else
                        <span class="formatted-order-number-value">{{ $formattedOrderNumber }}</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div id="pos-cart-scroll" class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden space-y-3 pb-4 pos-hover-scrollbar lg:pr-1">
        @if ($orderType == 'dine_in')
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 mt-1 mb-1 min-h-8 w-full justify-between">
                <div class="inline-flex items-center justify-center gap-1.5 text-xs h-8 shrink-0 px-1">
                    <span class="text-gray-700 dark:text-gray-300 leading-none shrink-0">@lang('modules.order.noOfPax')</span>
                    <x-input type="number" step='1' min='1' class="h-8 w-10 min-w-0 max-w-[2.5rem] px-1 py-0 text-center tabular-nums leading-none" id="no-of-pax" value="{{ $noOfPax }}" onchange="updateNoOfPax(this.value)" />
                </div>

                <div class="flex flex-wrap items-center justify-end gap-1.5 min-w-0 flex-1">
                    @if (auth()->user()->roles->pluck('display_name')->contains('Waiter'))
                        <div class="inline-flex items-center gap-1 text-xs dark:text-gray-300 h-8">
                            <svg class="w-4 h-4 shrink-0 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3m0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22" />
                            </svg>
                            <span class="font-medium max-w-[8rem] sm:max-w-none truncate leading-none">{{ $waiterName }}</span>
                        </div>
                    @elseif(user_can('Update Order'))
                        <div class="inline-flex items-center gap-1.5 min-w-0">
                            <button type="button" onclick="showKotNoteModal()"
                                class="relative inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                title="{{ __('modules.order.addNote') }}" aria-label="{{ __('modules.order.addNote') }}" data-tooltip-target="tooltip-add-note-order">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </button>
                            <div id="tooltip-add-note-order" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                {{ __('modules.order.addNote') }}
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                            <x-select class="text-xs h-8 min-h-8 w-36 min-w-0 max-w-full sm:max-w-[11rem] rounded-md border-gray-300 bg-white px-2 py-0 pr-7 leading-none dark:border-gray-600 dark:bg-gray-800" id="waiter-select" onchange="updateWaiterSelection(this.value)">
                                <option value="">@lang('modules.order.selectWaiter')</option>
                                @foreach ($users as $item)
                                    @php $runningCount = (int)($waiterRunningOrdersMap[$item->id] ?? 0); @endphp
                                    <option value="{{ $item->id }}" {{ ($currentWaiter && $currentWaiter->id == $item->id) ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ __('modules.table.running') }}: {{ $runningCount }})
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                    @elseif($currentWaiter)
                        <div class="inline-flex items-center gap-1 text-xs dark:text-gray-300 h-8">
                            <svg class="w-4 h-4 shrink-0 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3m0 14.2a7.2 7.2 0 0 1-6-3.22c.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08a7.2 7.2 0 0 1-6 3.22" />
                            </svg>
                            <span class="font-medium text-gray-900 dark:text-gray-100 max-w-[8rem] sm:max-w-none truncate leading-none">{{ $waiterName }}</span>
                        </div>
                    @endif
                </div>
        </div>
        @endif

        @if (isset($orderDetail) && $orderDetail && function_exists('module_enabled') && module_enabled('Hotel') && in_array('Hotel', restaurant_modules()) && $orderDetail->order_type === 'room_service' && $orderDetail->context_type === 'HOTEL_ROOM' && $orderDetail->hotelStay)
            @php $hotelStayKot = $orderDetail->hotelStay; @endphp
            <div class="flex flex-wrap gap-2 mb-2 p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                @if ($hotelStayKot->room)
                    <div class="flex items-center gap-1.5 text-xs">
                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.folio.room'):</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $hotelStayKot->room->room_number }}</span>
                    </div>
                    <span class="text-gray-300 dark:text-gray-600 text-xs self-center">|</span>
                @endif
                <div class="flex items-center gap-1.5 text-xs">
                    <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.folio.stay'):</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $hotelStayKot->stay_number }}</span>
                </div>
                @if ($hotelStayKot->stayGuests && $hotelStayKot->stayGuests->isNotEmpty() && $hotelStayKot->stayGuests->first()?->guest)
                    <span class="text-gray-300 dark:text-gray-600 text-xs self-center">|</span>
                    <div class="flex items-center gap-1.5 text-xs">
                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.guest.guest'):</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $hotelStayKot->stayGuests->first()->guest->full_name }}</span>
                    </div>
                @endif
                @if ($orderDetail->bill_to)
                    <span class="text-gray-300 dark:text-gray-600 text-xs self-center">|</span>
                    <div class="flex items-center gap-1.5 text-xs">
                        <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">@lang('hotel::modules.roomService.billTo'):</span>
                        <span @class([
                            'font-semibold',
                            'text-orange-600 dark:text-orange-400' => $orderDetail->bill_to === 'POST_TO_ROOM',
                            'text-green-600 dark:text-green-400' => $orderDetail->bill_to !== 'POST_TO_ROOM',
                        ])>
                            {{ $orderDetail->bill_to === 'POST_TO_ROOM' ? __('hotel::modules.roomService.postToRoom') : __('hotel::modules.roomService.payNow') }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        @if (in_array($orderType, ['delivery', 'pickup']))
            <div class="flex items-center justify-between gap-3">
                @if ($orderType == 'delivery' && user_can('Update Order'))
                    <div class="flex items-center gap-2 w-full">
                        <svg class="w-5 h-5 flex-shrink-0 text-gray-700 dark:text-gray-200"
                            fill="currentColor" version="1.0" viewBox="0 0 512 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <g transform="translate(0 512) scale(.1 -.1)">
                                <path
                                    d="m2605 4790c-66-13-155-48-213-82-71-42-178-149-220-221-145-242-112-552 79-761 59-64 61-67 38-73-13-4-60-24-104-46-151-75-295-249-381-462-20-49-38-91-39-93-2-2-19 8-40 22s-54 30-74 36c-59 16-947 12-994-4-120-43-181-143-122-201 32-33 76-33 106 0 41 44 72 55 159 55h80v-135c0-131 1-137 25-160l24-25h231 231l24 25c24 23 25 29 25 161v136l95-4c82-3 97-6 117-26l23-23v-349-349l-46-46-930-6-29 30c-17 16-30 34-30 40 0 7 34 11 95 11 88 0 98 2 120 25 16 15 25 36 25 55s-9 40-25 55c-22 23-32 25-120 25h-95v80 80h55c67 0 105 29 105 80 0 19-9 40-25 55l-24 25h-231-231l-24-25c-33-32-33-78 0-110 22-23 32-25 120-25h95v-80-80h-175c-173 0-176 0-200-25-33-32-33-78 0-110 24-25 27-25 197-25h174l12-45c23-88 85-154 171-183 22-8 112-12 253-12h220l-37-43c-103-119-197-418-211-669-7-115-7-116 19-142 26-25 29-26 164-26h138l16-69c55-226 235-407 464-466 77-20 233-20 310 0 228 59 409 240 463 464l17 71h605 606l13-62c58-281 328-498 621-498 349 0 640 291 640 640 0 237-141 465-350 569-89 43-193 71-271 71h-46l-142 331c-78 183-140 333-139 335 2 1 28-4 58-12 80-21 117-18 145 11l25 24v351 351l-26 26c-24 24-30 25-91 20-130-12-265-105-317-217l-23-49-29 30c-16 17-51 43-79 57-49 26-54 27-208 24-186-3-227 9-300 87-43 46-137 173-137 185 0 3 10 6 23 6s48 12 78 28c61 31 112 91 131 155 7 25 25 53 45 70 79 68 91 152 34 242-17 27-36 65-41 85-13 46-13 100 0 100 6 0 22 11 35 25 30 29 33 82 10 190-61 290-332 508-630 504-38-1-88-5-110-9zm230-165c87-23 168-70 230-136 55-57 108-153 121-216l6-31-153-4c-131-3-161-6-201-25-66-30-133-96-165-162-26-52-28-66-31-210l-4-153-31 6c-63 13-159 66-216 121-66 62-113 143-136 230-88 339 241 668 580 580zm293-619c7-41 28-106 48-147l36-74-24-15c-43-28-68-59-68-85 0-40-26-92-54-110-30-20-127-16-211 8l-50 14-3 175c-2 166-1 176 21 218 35 67 86 90 202 90h91l12-74zm-538-496c132-25 214-88 348-269 101-137 165-199 241-237 31-15 57-29 59-30s-6-20-17-43c-12-22-27-75-33-117-12-74-12-76-38-71-149 30-321 156-424 311-53 80-90 95-140 55-48-38-35-89 52-204l30-39-28-36c-42-54-91-145-110-208l-18-57-337-3-338-2 6 82c9 112 47 272 95 400 135 357 365 522 652 468zm1490-630c0-254 1-252-83-167-54 53-77 104-77 167s23 114 77 168c84 84 83 86 83-168zm-454 63c18-13 41-46 57-83l26-61-45-19c-75-33-165-52-244-54l-75-1-3 29c-8 72 44 166 113 201 42 22 132 16 171-12zm-2346-63v-80h-120-120v80 80h120 120v-80zm1584-184c80-52 154-84 261-111l90-23 112-483c68-295 112-506 112-540 1-68-21-134-56-171l-26-27-17 48c-29 86-99 159-177 186l-38 13-6 279c-5 297-5 297-64 414-58 113-212 233-328 254-21 4-41 14-44 21-12 32 88 201 111 186 6-4 37-24 70-46zm1099-493 185-433-348-490h-138-138l33 68c40 81 56 176 44 252-8 47-203 894-217 941-4 13 9 17 75 23 80 6 230 44 280 71 14 7 29 10 32 7 4-4 90-202 192-439zm-1323 187c118-22 229-99 275-190 37-74 45-138 45-375v-225h-160-160v115c0 179-47 289-158 369-91 67-141 76-417 76h-244l10 32c5 18 9 72 9 120v88h374c209 0 397-4 426-10zm-319-402c50-15 111-67 135-115 16-32 20-70 24-244l5-205 36-72 35-72h-759-759l7 63c17 164 95 400 165 502 47 68 129 124 215 145 52 13 853 12 896-2zm2114-323c256-67 415-329 350-580-48-184-202-326-390-358-197-34-412 76-500 257-19 39-38 86-41 104l-6 32h80 81l24-53c31-69 86-123 156-156 77-36 192-36 266-1 63 31 124 91 156 155 33 68 34 197 2 267-27 60-95 127-156 157-95 46-229 36-311-22-18-12-26-15-21-6 13 22 126 182 143 202 19 22 86 23 167 2zm-1315-243c39-21 87-99 77-125-6-15-27-17-178-17-193 0-231 7-289 58-35 29-70 78-70 97 0 3 96 5 213 5 187 0 217-2 247-18zm1288-89c51-38 67-70 67-133s-16-95-69-134c-43-33-132-29-179 7-20 15-37 32-37 38 0 5 36 9 80 9 73 0 83 3 105 25 33 32 33 78 0 110-22 22-32 25-105 25-44 0-80 4-80 8 0 12 29 37 65 57 39 21 117 15 153-12zm-397-46c-10-9-11-8-5 6 3 10 9 15 12 12s0-11-7-18zm-2460-217c45-106 169-184 289-184s244 78 289 184l22 50h81 81l-7-32c-13-65-66-159-123-219-186-195-500-195-686 0-57 60-110 154-123 219l-6 32h80 81l22-50zm419 41c0-16-51-50-91-63-30-8-48-8-78 0-40 13-91 47-91 63 0 5 57 9 130 9s130-4 130-9z" />
                            </g>
                        </svg>

                        <x-select class="w-full text-xs" id="delivery-executive-select" onchange="updateSelectDeliveryExecutive(this.value)">
                            <option value="">@lang('modules.order.selectDeliveryExecutive')</option>
                            @foreach ($deliveryExecutives as $item)
                                @php
                                    $isBusy = (bool)($deliveryExecutiveBusyMap[$item->id] ?? false);
                                    $isSelected = (int)$selectDeliveryExecutive === (int)$item->id;
                                @endphp
                                @if (!$isBusy || $isSelected)
                                    <option
                                        value="{{ $item->id }}"
                                        data-busy="{{ $isBusy ? 1 : 0 }}"
                                        data-name="{{ $item->name }}"
                                        {{ $isSelected ? 'selected' : '' }}>
                                        {{ $item->name }}@if ($isBusy) (Busy)@endif
                                    </option>
                                @endif
                            @endforeach
                        </x-select>
                    </div>
                @endif

                @if ($orderType == 'pickup')
                    @php
                        $orderItemsPickupDate = $pickupDate ?? now()->format(restaurant()->date_format);
                        $orderItemsPickupTime = $pickupTime ?? now()->format('H:i');
                    @endphp
                    <div class="gap-2 flex justify-between items-center mb-2 w-full">
                        <div class="inline-flex items-center gap-2 w-full flex-wrap">
                            <label for="pickupDateInput" class="text-sm text-gray-600 dark:text-gray-300">
                                @lang('modules.order.pickUpDateTime'):
                            </label>
                            <div class="flex items-stretch gap-2 flex-1 min-w-0" id="pickup-datetime-container-order-items">
                                <div class="relative flex-1 min-w-0 flex flex-col justify-center">
                                    <x-datepicker
                                        id="pickupDateInput"
                                        value="{{ $orderItemsPickupDate }}"
                                        minDate="{{ $minDate }}"
                                        maxDate="{{ $maxDate }}"
                                        onchange="window.updatePickupDate && window.updatePickupDate(this.value)"
                                        class="pl-4 pr-5 min-h-[2.75rem] box-border py-2 text-lg leading-tight text-gray-700 dark:text-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 w-full" />
                                </div>

                                <div class="relative flex-1 min-w-0 flex flex-col justify-center" style="overflow: visible;">
                                    <x-time-picker
                                        id="pickupTimeInput"
                                        value="{{ $orderItemsPickupTime }}"
                                        onchange="window.updatePickupTime && window.updatePickupTime(this.value)"
                                        class="!min-h-[2.75rem] pl-4 pr-11 py-2 text-lg leading-tight"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

        @if ($orderStatus->value === 'cancelled')
            <span class="inline-block px-2 py-1 my-2 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                @lang('modules.order.info_cancelled')
            </span>
        @else
            <div class="px-2 py-1.5 bg-white border border-gray-200 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-700 {{ $isShowOrderDetail ? 'sm:px-3 sm:py-2' : '' }}">
                @php
                    $statuses = \App\Enums\OrderStatus::progressStepsFor($orderType);
                    $currentIndex = \App\Enums\OrderStatus::progressIndex($orderStatus->value, $orderType);
                    $nextIndex = min($currentIndex + 1, count($statuses) - 1);
                @endphp

                <div class="flex flex-col gap-2.5">
                    <div class="flex items-center justify-between gap-2 text-gray-900 dark:text-white">
                        <h3 class="text-xs font-semibold leading-none shrink-0">
                            {{ __('modules.order.orderStatus') }}
                        </h3>
                        <div class="flex items-center justify-end gap-1.5 min-w-0 sm:ml-auto">
                            <span id="order-status-badge" data-status="{{ $orderStatus->value }}" @class([
                                'inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full border leading-none shrink-0',
                                $orderStatus->badgeClasses(),
                            ])>
                                {{ App\Enums\OrderStatus::from($orderStatus->value)->translatedLabel() }}
                            </span>
                            @if (user_can('Update Order'))
                                @if ($orderStatus->value === 'placed')
                                    <x-danger-button id="order-status-cancel-btn" class="inline-flex items-center !gap-0.5 !px-1.5 !py-0.5 !text-[10px] !font-medium !rounded-md leading-none shadow-none focus:!ring-1 focus:!ring-offset-0 dark:text-gray-200 shrink-0"
                                        onclick="confirmCancelOrder()">
                                        <span>{{ __('modules.order.cancelOrder') }}</span>
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </x-danger-button>
                                @endif

                                @if ($currentIndex < count($statuses) - 1)
                                    <x-secondary-button id="order-status-next-btn" class="inline-flex items-center !gap-0.5 !text-[10px] !font-medium !rounded-md leading-none shadow-none focus:!ring-1 focus:!ring-offset-0 dark:text-gray-200 shrink-0"
                                        onclick="updateOrderStatus('{{ $statuses[$nextIndex] }}')">
                                        <span id="order-status-next-label" class="whitespace-nowrap">{{ __('modules.order.moveTo') }}
                                            {{ App\Enums\OrderStatus::from($statuses[$nextIndex])->translatedLabel() }}</span>
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </x-secondary-button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="relative py-0.5">
                        <!-- Progress line between steps -->
                        <div class="absolute top-1/2 left-0 right-0 z-0 h-0.5 -translate-y-1/2 bg-gray-200 dark:bg-gray-700" style="margin: 0 6%;">
                            <div class="h-full bg-skin-base/60 dark:bg-skin-base/50 transition-all duration-500 rounded-full" style="width: {{ $currentIndex > 0 ? ($currentIndex / (count($statuses) - 1)) * 100 : 0 }}%;"></div>
                        </div>

                        <div id="order-status-steps" class="relative z-10 flex justify-between px-1">
                            @foreach ($statuses as $index => $status)
                                @php
                                    $isCompleted = $index < $currentIndex;
                                    $isCurrent = $index === $currentIndex;
                                    $isNext = $index === $currentIndex + 1;
                                    $stepStatus = App\Enums\OrderStatus::from($status);
                                @endphp
                                @php
                                    $statusTooltipOrderId = (int) ($orderDetail->id ?? $orderID ?? 0);
                                    $statusStepTooltipId = 'tooltip-order-detail-status-' . $statusTooltipOrderId . '-' . $index;
                                    $statusStepLabel = $stepStatus->translatedLabel();
                                @endphp
                                <div data-status="{{ $status }}" class="order-status-step flex flex-1 justify-center px-0.5 min-w-0">
                                    <div
                                        class="group relative inline-flex flex-col items-center cursor-default"
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
                                            data-icon-base="order-status-icon flex size-8 shrink-0 items-center justify-center rounded-full relative  bg-white shadow-sm dark:bg-gray-900 [&_.order-status-step-svg]:text-current"
                                        >
                                            {!! $stepStatus->icon() !!}
                                        </div>
                                        <div
                                            id="{{ $statusStepTooltipId }}"
                                            role="tooltip"
                                            class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-1 hidden w-max max-w-[10rem] -translate-x-1/2 rounded-md bg-gray-900 px-2 py-1 text-center text-[10px] font-medium leading-tight text-white shadow-sm dark:bg-gray-700 whitespace-normal break-words group-hover:block group-focus:block"
                                        >
                                            {{ $statusStepLabel }}
                                        </div>
                                    </div>

                                    <!-- Hidden label used by JS to update badge text -->
                                    <span class="order-status-label sr-only">
                                        {{ App\Enums\OrderStatus::from($status)->translatedLabel() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $showKotAlert = $isShowOrderDetail
                && isset($orderDetail)
                && $orderDetail
                && $orderDetail->status === 'kot'
                && isset($kotList)
                && count($kotList) === 0
                && user_can('Update Order');
        @endphp
        @if ($showKotAlert)
            <div class="py-1.5 px-2 sm:py-2 sm:px-2.5 border-l-4 rounded-r-lg bg-orange-50 border-orange-400 dark:bg-orange-800/20 dark:border-orange-700/30">
                <div class="flex items-start gap-2 min-w-0">
                    <svg class="h-4 w-4 shrink-0 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98zM11 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-1-8a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1"/></svg>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-semibold text-orange-800 dark:text-orange-200 leading-snug block">
                            @lang('modules.order.kotNotCreated')
                        </span>
                        <p class="text-xs text-orange-600 dark:text-orange-400 leading-snug mt-0.5">
                            @lang('modules.order.kotNotCreatedMessage')
                        </p>
                    </div>
                    <x-button type="button" onclick="saveOrder('kot')" class="shrink-0 self-center !py-1.5 !px-3 !text-xs !font-medium leading-tight">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline w-3 h-3 shrink-0 ltr:mr-0.5 rtl:ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="min-w-0 truncate">@lang('modules.order.createKot')</span>
                    </x-button>
                </div>
            </div>
        @endif

        @foreach ($kotList as $kot)
            @php
                $hasItems = false;
                foreach ($orderItemList as $key => $item) {
                    if (str_starts_with(trim((string) $key, '"'), 'kot_' . $kot->id . '_')) {
                        $hasItems = true;
                        break;
                    }
                }
            @endphp

            @if ($hasItems)
                <div class="flex justify-between px-3 py-2 text-[11px] font-medium text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                    <div>@lang('menu.kot') #{{ $kot->kot_number }}</div>
                    <div>{{ $kot->created_at->timezone(timezone())->translatedFormat('d F, h:i A') }}</div>
                </div>

                <div class="flex flex-col gap-2" data-kot-id="{{ $kot->id }}" @if(!$isLivewirePos) data-js-populated="1" @endif>
                    @if ($isLivewirePos)
                        @foreach ($orderItemList as $key => $item)
                            @php $normKey = trim((string) $key, '"'); @endphp
                            @if (str_starts_with($normKey, 'kot_' . $kot->id . '_'))
                                @include('pos.partials.order-kot-cart-line', [
                                    'cartKey' => $key,
                                    'normKey' => $normKey,
                                    'item' => $item,
                                    'orderItemQty' => $orderItemQty ?? [],
                                    'orderItemAmount' => $orderItemAmount ?? [],
                                ])
                            @endif
                        @endforeach
                    @endif
                </div>
            @endif
        @endforeach

        @if (count($orderItemList) === 0 && isset($orderDetail) && $orderDetail && $orderDetail->status === 'kot')
            @php
                $fallbackKotItems = collect();
                if (!$orderDetail->relationLoaded('kot')) {
                    $orderDetail->load('kot.items.menuItem', 'kot.items.menuItemVariation', 'kot.items.modifierOptions');
                }
                foreach ($orderDetail->kot as $kot) {
                    $fallbackKotItems = $fallbackKotItems->merge($kot->items->where('status', '!=', 'cancelled'));
                }
            @endphp
            @foreach ($fallbackKotItems as $kotItem)
                @php
                    $isFreeItemFromStamp = (bool) ($kotItem->is_free_item_from_stamp ?? false);
                    $expectedAmount = (float) ($kotItem->price ?? 0) * (int) ($kotItem->quantity ?? 1);
                    $actualAmount = (float) ($kotItem->amount ?? 0);
                    $stampDiscountAmount = !$isFreeItemFromStamp ? max(0, $expectedAmount - $actualAmount) : 0;
                    $hasStampDiscount = $stampDiscountAmount > 0.01;
                @endphp
                <div class="flex justify-between items-start bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 inline-flex items-center gap-2">
                            {{ $kotItem->menuItem->item_name ?? __('app.item') }}
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
                        <div class="text-[11px] text-gray-500">
                            {{ __('modules.order.qty') }}: {{ $kotItem->quantity }}
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        @if ($isFreeItemFromStamp)
                            <div class="flex flex-col items-end">
                                <span class="text-green-600 dark:text-green-400">{{ currency_format(0, restaurant()->currency_id) }}</span>
                                <span class="text-[10px] text-gray-400 line-through">{{ currency_format($expectedAmount, restaurant()->currency_id) }}</span>
                            </div>
                        @elseif($hasStampDiscount)
                            <div class="flex flex-col items-end">
                                <span class="text-blue-600 dark:text-blue-400">{{ currency_format($actualAmount, restaurant()->currency_id) }}</span>
                                <span class="text-[10px] text-gray-400 line-through">{{ currency_format($expectedAmount, restaurant()->currency_id) }}</span>
                            </div>
                        @else
                            {{ currency_format($actualAmount, restaurant()->currency_id) }}
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="pos-cart-action-footer" class="shrink-0 relative z-20 border-t border-gray-100 pt-1 pb-2 bg-white shadow-[0_-4px_12px_-2px_rgba(15,23,42,0.06)] dark:bg-gray-800 dark:shadow-none {{ $isShowOrderDetail ? 'mt-1' : '' }}">
        <div class="h-auto p-1 text-center bg-gray-50 rounded space-y-1 dark:bg-gray-700 pointer-events-auto">
            <div class="flex flex-wrap items-center gap-1 text-left">
                @if (user_can('Add Discount on POS'))
                    <div id="discount-button-container" style="display: {{ count($orderItemList) > 0 ? 'block' : 'none' }};">
                        <button onclick="showAddDiscountModal()" class="inline-flex items-center px-1 py-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 gap-1 leading-none">
                            <svg class="w-4 h-4 text-current me-1" width="24" height="24" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                <path d="m7.25 14.25-5.5-5.5 7-7h5.5v5.5z"/>
                                <circle cx="11" cy="5" r=".5" fill="#000"/>
                            </svg>
                            @lang('modules.order.addDiscount')
                        </button>
                    </div>
                @endif
                @include('pos.partials.loyalty-redeem-button')
            </div>

            <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400">
                <div>
                    @lang('modules.order.totalItem')
                </div>
                <div id="total-items-display">
                    {{ count($orderItemList) }}
                </div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400">
                <div class="flex items-center gap-2">
                    <span>@lang('modules.order.subTotal')</span>
                    @php
                        $stampDiscountAmount = 0;
                        $hasFreeStampItems = false;
                        if (isset($orderID) && $orderID && isset($orderDetail) && $orderDetail) {
                            $stampDiscountAmount = (float)($orderDetail->stamp_discount_amount ?? 0);
                            $hasFreeStampItems = $orderDetail->items()->where('is_free_item_from_stamp', true)->exists();
                        } else {
                            foreach (($orderItemList ?? []) as $itemKey => $itemValue) {
                                $note = $itemNotes[$itemKey] ?? '';
                                $rawFreeFlag = is_array($itemValue)
                                    ? ($itemValue['is_free_item_from_stamp'] ?? false)
                                    : ($itemValue->is_free_item_from_stamp ?? false);
                                $isFlaggedFree = in_array($rawFreeFlag, [true, 1, '1', 'true'], true);

                                if (
                                    str_starts_with((string)$itemKey, 'free_stamp_')
                                    || $isFlaggedFree
                                    || str_contains((string)$note, __('loyalty::app.freeItemFromStamp'))
                                    || str_starts_with(strtolower(trim((string)$note)), 'free')
                                ) {
                                    $hasFreeStampItems = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <span id="stamp-discount-badge"
                        class="px-1.5 py-0.5 text-xs rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 {{ ($stampDiscountAmount > 0 || $hasFreeStampItems) ? '' : 'hidden' }}">
                        <span id="stamp-discount-badge-text">
                            @lang('app.stampDiscount')
                            @if($stampDiscountAmount > 0)
                                (-{{ currency_format($stampDiscountAmount, restaurant()->currency_id) }})
                            @elseif($hasFreeStampItems)
                                (@lang('app.freeItem'))
                            @endif
                        </span>
                    </span>
                </div>
                <div id="subtotal-display">
                    {{ currency_format($subTotal, restaurant()->currency_id) }}
                </div>
            </div>

            @if(function_exists('module_enabled') && module_enabled('Loyalty'))
                <div id="loyalty-discount-row-blade">
                    @include('loyalty::components.loyalty-discount-display', [
                        'loyaltyPointsRedeemed' => $loyaltyPointsRedeemed ?? 0,
                        'loyaltyDiscountAmount' => $loyaltyDiscountAmount ?? 0,
                        'currencyId' => restaurant()->currency_id,
                        'showEditIcon' => true,
                        'customer' => $customer ?? null
                    ])
                </div>
                <div id="loyalty-discount-row-js" class="flex justify-between text-xs text-blue-600 dark:text-blue-400 items-center" style="display: none;">
                    <div class="inline-flex items-center gap-x-1">
                        <span id="loyalty-js-label">@lang('loyalty::app.loyaltyDiscount') (<span id="loyalty-js-points">0</span> @lang('loyalty::app.points'))</span>
                        @if(user_can('Update Order'))
                            <span class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-300 cursor-pointer ml-1"
                                onclick="if (typeof window.openLoyaltyRedemptionModal === 'function') { window.openLoyaltyRedemptionModal(); }"
                                title="{{ __('Edit loyalty points') }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </span>
                        @endif
                    </div>
                    <div id="loyalty-js-amount" class="text-blue-600 dark:text-blue-400 font-medium">-0</div>
                </div>
            @endif

            @php
                $_posDiscDecimals = (int) (optional(currency_format_setting())->no_of_decimal ?? 2);
                $_posShowDiscountRow = round((float) ($discountAmount ?? 0), $_posDiscDecimals) > 0 && (int) ($loyaltyPointsRedeemed ?? 0) === 0;
            @endphp
            <div id="discount-row"
                class="flex justify-between text-green-500 text-xs dark:text-green-400"
                style="display: {{ $_posShowDiscountRow ? 'flex' : 'none' }};">
                <div class="inline-flex items-center gap-x-1">
                    @lang('modules.order.discount')
                    <span id="discount-type-display">
                        @if ($discountType == 'percent')
                            ({{ $discountValue }}%)
                        @endif
                    </span>
                    @if(user_can('Add Discount on POS') && user_can('Update Order'))
                        <span type="button" class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-transform hover:scale-110 active:scale-95 focus:outline-none cursor-pointer"
                            onclick="removeCurrentDiscount()" title="@lang('app.remove')">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </div>
                <div id="discount-display">
                    -{{ currency_format($discountAmount, restaurant()->currency_id) }}
                </div>
            </div>
            <div id="taxable-amount-row"
                class="flex justify-between text-gray-500 text-xs dark:text-neutral-400"
                style="{{ count($orderItemList) > 0 ? '' : 'display: none;' }}">
                <div>{{ __('Taxable Amount') }}</div>
                <div id="taxable-amount-display">
                    {{ currency_format(max(0, ($subTotal ?? 0) - ($discountAmount ?? 0)), restaurant()->currency_id) }}
                </div>
            </div>

            @php
                $applicableExtraCharges = collect($extraCharges ?? [])->filter(function ($charge) use ($orderType) {
                    $allowedTypes = $charge->order_types ?? [];

                    return empty($allowedTypes) || in_array($orderType, $allowedTypes);
                });
            @endphp

            <div id="extra-charges-container" style="{{ (count($orderItemList) > 0 && $extraCharges && count($extraCharges) > 0) ? '' : 'display: none;' }}">
                @if (count($orderItemList) > 0 && $extraCharges)
                    @foreach ($extraCharges as $charge)
                        <div
                            class="flex justify-between text-gray-500 text-xs dark:text-neutral-400"
                            data-charge-id="{{ $charge->id }}"
                            data-charge-name="{{ $charge->charge_name }}"
                            data-charge-type="{{ $charge->charge_type }}"
                            data-charge-value="{{ $charge->charge_value }}">
                            <div class="inline-flex items-center gap-x-1">{{ $charge->charge_name }}
                                @if ($charge->charge_type == 'percent')
                                    ({{ $charge->charge_value }}%)
                                @endif
                                @if (user_can('Update Order'))
                                    <span class="text-red-500 hover:scale-110 active:scale-100 cursor-pointer"
                                        onclick="removeExtraCharge({{ $charge->id }}, '{{ $orderType }}')">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="charge-amount-display">
                                {{ currency_format($charge->getAmount($discountedTotal), restaurant()->currency_id) }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            @if ($tipAmount > 0)
                <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400">
                    <div>
                        @lang('modules.order.tip')
                    </div>
                    <div>
                        {{ currency_format($tipAmount, restaurant()->currency_id) }}
                    </div>
                </div>
            @endif

            @if ($orderType === 'delivery' && !is_null($deliveryFee))
                <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400">
                    <div>
                        @lang('modules.delivery.deliveryFee')
                        <span id="delivery-fee-note" class="{{ $deliveryFee > 0 ? 'hidden' : '' }}">
                            @lang('modules.delivery.freeDelivery')
                        </span>
                    </div>
                    <div id="delivery-fee-display">
                        @if ($deliveryFee > 0)
                            {{ currency_format($deliveryFee, restaurant()->currency_id) }}
                        @else
                            <span class="font-medium text-green-500">@lang('modules.delivery.freeDelivery')</span>
                        @endif
                    </div>
                </div>
            @endif

            @if ($taxMode == 'order')
                <div id="order-taxes-container">
                    @foreach ($taxes as $item)
                        <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400"
                            data-tax-name="{{ $item->tax_name }}"
                            data-tax-percent="{{ $item->tax_percent }}">
                            <div>
                                {{ $item->tax_name }} ({{ $item->tax_percent }}%)
                            </div>
                            <div class="tax-amount-display">
                                {{ currency_format(($item->tax_percent / 100) * $taxBase, restaurant()->currency_id) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @php
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
                <div id="item-taxes-container">
                    @foreach ($taxTotals as $taxName => $taxInfo)
                        <div class="flex justify-between text-gray-500 text-[11px] dark:text-neutral-400"
                            data-tax-name="{{ $taxName }}"
                            data-tax-percent="{{ $taxInfo['percent'] }}">
                            <div>
                                {{ $taxName }} ({{ $taxInfo['percent'] }}%)
                            </div>
                            <div class="tax-amount-display">
                                {{ currency_format($taxInfo['amount'], restaurant()->currency_id) }}
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400">
                        <div>
                            @lang('modules.order.totalTax')
                        </div>
                        <div id="total-tax-display">
                            {{ currency_format($totalTaxAmount, restaurant()->currency_id) }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-between font-semibold text-gray-900 dark:text-gray-100">
                <div>
                    @lang('modules.order.total')
                </div>
                <div id="total-display" class="text-skin-base text-base">
                    {{ currency_format($total, restaurant()->currency_id) }}
                </div>
            </div>
        </div>

        @if ($orderType == 'delivery' && $orderDetail->delivery_address)
            <div class="flex flex-col gap-2 p-3 mt-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                @if ($orderDetail->customer)
                    <div class="flex gap-1.5 items-center text-gray-800 dark:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg>
                        <span class="text-gray-900 dark:text-gray-100">
                            {{ $orderDetail->customer->name }}
                        </span>
                    </div>
                @endif
                <div class="flex items-center justify-between mb-2">
                    <div class="flex gap-1.5 items-center text-gray-800 dark:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                        </svg>
                        @lang('modules.customer.address')
                    </div>

                    @if ($orderDetail->customer_lat && $orderDetail->customer_lng && branch()->lat && branch()->lng)
                        <a href="https://www.google.com/maps/dir/?api=1&travelmode=two-wheeler&origin={{ branch()->lat }},{{ branch()->lng }}&destination={{ $orderDetail->customer_lat }},{{ $orderDetail->customer_lng }}"
                            target="_blank"
                            class="flex items-center gap-1 text-sm text-blue-500 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            <span>@lang('modules.order.viewOnMap')</span>
                            <svg width="24" height="24" class="w-4 h-4" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" fill="none">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4m-8-2 8-8m0 0v5m0-5h-5" />
                            </svg>
                        </a>
                    @endif
                </div>

                <div
                    class="p-2 text-xs text-gray-600 bg-white border border-gray-200 rounded dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600">
                    {!! nl2br(e($orderDetail->delivery_address)) !!}
                </div>
            </div>
        @endif

    <div id="pos-mobile-action-dock" class="shrink-0 w-full">
        <div class="relative z-20 h-auto pt-3 text-center w-full pointer-events-auto lg:pt-3 mb-2" data-pos-cart-order-actions>
            @if ($orderDetail->status == 'kot')
                <div class="flex gap-3 mb-4" data-pos-non-kot-cart-actions>
                    @if (user_can('Bill and Payment'))
                        <button class="pos-order-action-btn rounded bg-skin-base text-white w-full p-2 relative text-xs hover:opacity-90"
                            onclick="saveOrder('bill')" id="saveBillBtn">
                            <span id="saveBillBtnText">@lang('modules.order.bill')</span>
                            <span id="saveBillBtnLoading" class="hidden">
                                <svg class="animate-spin inline-flex items-center -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                @lang('modules.order.bill')
                            </span>
                        </button>
                    @endif
                    @if (user_can('Bill and Payment'))
                        <button class="pos-order-action-btn rounded bg-green-500 text-white w-full p-2 relative text-xs hover:bg-green-600"
                            onclick="saveOrder('bill', 'payment')" id="saveBillPaymentBtn">
                            <span id="saveBillPaymentBtnText">@lang('modules.order.billAndPayment')</span>
                            <span id="saveBillPaymentBtnLoading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-flex items-center" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                @lang('modules.order.billAndPayment')
                            </span>
                        </button>
                    @endif
                    @if (user_can('Bill and Payment'))
                        <button class="pos-order-action-btn rounded bg-blue-500 text-white w-full p-2 relative text-xs hover:bg-blue-600"
                            onclick="saveOrder('bill', 'print')" id="saveBillPrintBtn">
                            <span id="saveBillPrintBtnText">@lang('modules.order.createBillAndPrintReceipt')</span>
                            <span id="saveBillPrintBtnLoading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                @lang('modules.order.createBillAndPrintReceipt')
                            </span>
                        </button>
                    @endif
                </div>

                @if (user_can('Update Order'))
                    <div class="flex gap-3 mb-2">
                        <a href="{{ route('pos.kot', ['id' => $orderDetail->id]) }}"
                            @class([
                                'pos-new-kot-link inline-flex items-center justify-center w-full p-2 text-xs font-semibold text-center rounded shadow-sm cursor-pointer relative z-[1] border-2 border-skin-base bg-skin-base/[0.12] text-skin-base hover:bg-skin-base/20 active:bg-skin-base/25 dark:bg-skin-base/20 dark:text-white dark:hover:bg-skin-base/30',
                                'col-span-2' => ! user()->hasRole('Admin_'. user()->restaurant_id),
                            ])>
                            @lang('modules.order.newKot')
                        </a>

                        @if (user()->hasRole('Admin_'. user()->restaurant_id))
                            <button type="button" class="pos-order-action-btn rounded bg-red-500 text-white w-full p-2 relative text-xs font-semibold hover:bg-red-600 active:bg-red-700 shadow-sm" onclick="confirmDeleteOrder()">
                                @lang('modules.order.deleteOrder')
                            </button>
                        @endif
                    </div>
                @endif
            @endif

            @if ($orderDetail->status == 'billed' && user_can('Bill and Payment'))
                <div class="flex gap-3 mb-4">
                    <button class="pos-order-action-btn rounded bg-skin-base text-white w-full p-2 relative text-xs hover:opacity-90"
                        onclick="saveOrder('bill')" id="saveBillBtn">
                        <span id="saveBillBtnText">@lang('modules.order.addPayment')</span>
                        <span id="saveBillBtnLoading" class="hidden">
                            <svg class="animate-spin inline-flex -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            @lang('modules.order.addPayment')
                        </span>
                    </button>
                </div>
            @endif
        </div>
        </div>
    </div>

   {{-- <x-confirmation-modal wire:model="confirmDeleteModal">
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

                <textarea
                    wire:model.defer="cancelReasonText"
                    id="cancelReasonText"
                    rows="4"
                    class="block w-full px-4 py-3 transition-all duration-200 border-2 border-gray-300 shadow-sm resize-none dark:border-gray-600 rounded-xl focus:ring-2 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="@lang('modules.settings.enterCancelReason')"></textarea>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmDeleteModal', false)" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="cancelOrder" wire:loading.attr="disabled">
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

            <x-danger-button class="ml-3" wire:click="saveOrder('cancel')"
                wire:loading.attr="disabled">
                @lang('modules.order.deleteOrder')
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

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
                        <p>@lang('modules.order.currentTable'): <strong>{{ $tableNo }}</strong></p>
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
    </x-dialog-modal> --}}

</div>
