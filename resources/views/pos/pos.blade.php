<div class="relative overflow-x-hidden flex flex-col h-full min-h-0" id="pos-container">

    @if(config('app.debug') && app()->environment('development'))
        <div id="pos-offline-test-toolbar" class="print:hidden pointer-events-auto fixed top-2 right-2 z-[10003] w-[min(13rem,calc(100vw-0.75rem))] max-lg:top-auto max-lg:right-auto max-lg:bottom-[5.25rem] max-lg:left-2 max-lg:w-[min(10.5rem,calc(100vw-4.5rem))] rounded-lg border border-amber-700/40 bg-amber-500/95 p-1.5 shadow-lg backdrop-blur-sm dark:bg-amber-900/90 dark:border-amber-600/50" role="region" aria-label="{{ __('messages.posOfflineDebugHint') }}">
            <div class="mb-1 flex items-center justify-between gap-1 max-lg:hidden">
                <p class="text-[9px] font-medium leading-tight text-amber-950 dark:text-amber-100">{{ __('messages.posOfflineDebugHint') }}</p>
                <button type="button" id="pos-offline-test-drag" class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border border-amber-900/25 bg-amber-100/70 text-amber-900 hover:bg-amber-100 cursor-move dark:bg-amber-800/60 dark:text-amber-100 dark:border-amber-200/30" aria-label="Move debug panel">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M7 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0 6a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M14.5 5.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m1.5 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                    </svg>
                </button>
            </div>
            <p class="mb-1 hidden max-lg:block text-[9px] font-medium leading-tight text-amber-950 dark:text-amber-100">{{ __('messages.posOfflineDebugHint') }}</p>
            <button type="button" id="pos-offline-test-toggle" class="w-full rounded-md bg-amber-950 px-2 py-1.5 text-xs max-lg:text-[10px] max-lg:py-1 font-semibold text-amber-50 hover:bg-black dark:bg-amber-200 dark:text-amber-950 dark:hover:bg-white">{{ __('messages.posOfflineDebugSimulate') }}</button>
        </div>
    @endif

    {{-- MultiPOS: registration modals / pending & declined overlays (active device label is in navigation-menu by stop impersonate) --}}
    @if(module_enabled('MultiPOS') && in_array('MultiPOS', restaurant_modules()))
        @include('multipos::partials.pos-registration', [
            'hasPosMachine' => $hasPosMachine,
            'machineStatus' => $machineStatus,
            'posMachine' => $posMachine,
            'limitReached' => $limitReached,
            'limitMessage' => $limitMessage,
            'shouldBlockPos' => $shouldBlockPos
        ])
    @endif

    {{-- Only render POS content if not blocked by registration/pending/declined --}}
    @if(!$shouldBlockPos)
        {{-- Restaurant availability banner (outside operating hours) --}}
        @if(!empty($showRestaurantClosedBanner) && !empty($restaurantClosedMessage))
            <div class="p-2">
                <div class="w-full p-3 text-sm font-medium text-center text-red-700 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
                    {{ $restaurantClosedMessage }}
                </div>
            </div>
        @endif

        {{-- Offline: order queued on device (replaces success toast) --}}
        <div id="pos-offline-queued-banner-wrap"
            class="fixed inset-0 z-[9990] flex items-center justify-center px-2 transition-all duration-500 ease-out opacity-0 pointer-events-none -translate-y-2"
            role="status"
            aria-live="polite"
            aria-hidden="true">
            <div class="flex w-[min(36rem,calc(100vw-1rem))] items-start gap-2.5 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2.5 shadow-md dark:border-sky-700/50 dark:bg-sky-950/90">
                <span class="mt-0.5 inline-flex h-2 w-2 shrink-0 rounded-full bg-sky-500 shadow-sm shadow-sky-500/40" aria-hidden="true"></span>
                <p id="pos-offline-queued-banner-text" class="min-w-0 flex-1 text-xs leading-snug text-sky-950 dark:text-sky-100 sm:text-sm"></p>
            </div>
        </div>

        {{-- Order type modal: pure Blade + JS (prices prefetched in window.posOrderTypePriceMaps) --}}
        @include('pos.partials.order-type-modal', [
            'orderTypes' => $orderTypes ?? [],
            'deliveryPlatforms' => $deliveryPlatforms ?? collect(),
            'posDeliveryPlatformsForModal' => $posDeliveryPlatformsForModal ?? [],
        ])


        {{-- Below lg: pad whole stack; lg+: outer pt-0 so fixed cart stays full viewport — menu column adds lg:pt-16 to clear fixed nav (same as app main pt-16). --}}
        <div class="relative flex flex-col lg:flex-row flex-1 min-h-0 gap-x-2 w-full overflow-hidden pt-16 lg:pt-0">

            <div class="w-full lg:w-8/12 flex-shrink-0 min-h-0 lg:min-h-0 lg:pt-16">
       
                @include('pos.menu', ['posMenuFiltersInline' => true])
            </div>
            {{-- Keeps menu width when cart is position:fixed (out of flex flow). --}}
            <div class="hidden lg:block lg:w-4/12 shrink-0" aria-hidden="true"></div>
            {{-- lg+: fixed cart full viewport height; panel children use --pos-nav-offset padding-top to clear the nav. --}}
            <div id="order-items-container" class="w-full max-w-full lg:w-4/12 flex flex-1 min-h-0 flex-col overflow-hidden border-s border-gray-200 dark:border-gray-700 lg:flex-none lg:flex-shrink-0 max-lg:bg-white max-lg:dark:bg-gray-800 max-lg:px-0 max-lg:pb-0 sm:max-lg:px-0 lg:px-0 lg:pb-0 lg:fixed lg:inset-y-0 lg:right-0 rtl:lg:left-0 rtl:lg:right-auto lg:z-40 lg:bg-white lg:dark:bg-gray-800 lg:pt-0">
                @php
                    $showOrderDetail = request()->boolean('show-order-detail');
                @endphp

                @if (!$orderDetail || ($orderDetail && $orderDetail->status == 'draft'))
                    @include('pos.kot_items')
                @elseif($orderDetail && $orderDetail->status == 'kot')
                    @php
                        // Get current KOT ID for print functionality
                        $currentKot = $orderDetail->kot()->orderBy('created_at', 'desc')->first();
                        $currentKotId = $currentKot ? $currentKot->id : null;
                    @endphp
                    @if($currentKotId)
                    <script>
                        // Set current KOT ID for print functionality
                        window.currentKotId = {{ $currentKotId }};
                    </script>
                    @endif
                    @if($showOrderDetail)
                        @include('pos.order_items')
                    @else
                        @include('pos.kot_items')
                    @endif
                @elseif($orderDetail && in_array($orderDetail->status, ['billed', 'paid', 'payment_due']))
                    @include('pos.order_detail')
                @endif
            </div>
        </div>

        {{-- Variation Modal --}}
        <div id="variationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-200">@lang('modules.menu.itemVariations')</h3>
                        <button type="button" onclick="closeVariationModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="variationModalContent"></div>
                </div>
            </div>
        </div>

        @if(module_enabled('Hotel')  && in_array('Hotel', restaurant_modules()))
            @include('hotel::pos.show-stay')
        @endif

        {{-- KOT Note Modal --}}
        <div id="kotNoteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-xl w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-200">@lang('modules.order.addNote')</h3>
                        <button type="button" onclick="closeKotNoteModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div>
                        <label for="orderNote" class="block text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.order.orderNote')</label>
                        <textarea id="orderNote" data-gramm="false" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" rows="2"></textarea>
                        <div id="orderNoteError" class="mt-2 text-red-600 text-sm" style="display: none;"></div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <x-secondary-button type="button" onclick="closeKotNoteModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">@lang('app.cancel')</x-secondary-button>
                        <x-button type="button" onclick="saveKotNote()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">@lang('app.save')</x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Item Note Modal --}}
        <div id="itemNoteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-xl w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-200">@lang('modules.order.addNote')</h3>
                        <button type="button" onclick="closeItemNoteModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div>
                        <label for="itemNoteInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.order.orderNote')</label>
                        <textarea id="itemNoteInput" data-gramm="false" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" rows="2"></textarea>
                        <div id="itemNoteError" class="mt-2 text-red-600 text-sm" style="display: none;"></div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <x-secondary-button type="button" onclick="closeItemNoteModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">@lang('app.cancel')</x-secondary-button>
                        <x-button type="button" onclick="saveItemNote()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">@lang('app.save')</x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Modal --}}
        <div id="tableModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-200">@lang('modules.table.availableTables')</h3>
                        <button type="button" onclick="closeTableModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="tableModalContent"></div>
                </div>
            </div>
        </div>

        {{-- Discount Modal --}}
        <div id="discountModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-xl w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-100">@lang('modules.order.addDiscount')</h3>
                        <button type="button" onclick="closeDiscountModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <div class="mb-3">
                            <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">@lang('modules.order.percent')</p>
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2" id="discountPresetOptions">
                                <button type="button" class="discount-preset-btn px-2 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" data-discount-percent="5">5%</button>
                                <button type="button" class="discount-preset-btn px-2 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" data-discount-percent="10">10%</button>
                                <button type="button" class="discount-preset-btn px-2 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" data-discount-percent="20">20%</button>
                                <button type="button" class="discount-preset-btn px-2 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" data-discount-percent="30">30%</button>
                                <button type="button" class="discount-preset-btn px-2 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" data-discount-percent="40">40%</button>
                                <button type="button" class="discount-preset-btn px-2 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" data-discount-percent="50">50%</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label for="discountValue" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Value</label>
                            <input id="discountValue" type="number" step="0.01" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" placeholder="{{ __('modules.order.enterDiscountValue') }}" min="0" />
                        </div>
                        <div>
                            <label for="discountType" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Type</label>
                            <select id="discountType" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="fixed">@lang('modules.order.fixed')</option>
                                <option value="percent">@lang('modules.order.percent')</option>
                            </select>
                        </div>
                        <div id="discountApplyOnWrapper">
                            <label for="discountApplyOn" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Apply On</label>
                            <select id="discountApplyOn" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="sub_total">@lang('modules.order.subTotal')</option>
                                <option value="total" selected>@lang('modules.order.total')</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>@lang('modules.order.subTotal')</span>
                            <span id="discountPreviewSubTotal">$0.00</span>
                        </div>
                        <div class="mt-1 flex justify-between text-green-600 dark:text-green-400">
                            <span>Discount</span>
                            <span id="discountPreviewAmount">-$0.00</span>
                        </div>
                        <div class="mt-1 flex justify-between font-semibold text-gray-800 dark:text-gray-100">
                            <span>@lang('modules.order.total')</span>
                            <span id="discountPreviewTotal">$0.00</span>
                        </div>
                    </div>
                    <div id="discountValueError" class="mt-2 text-red-600 text-sm" style="display: none;"></div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" onclick="closeDiscountModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">@lang('app.cancel')</button>
                        <x-button type="button" onclick="saveDiscount()">@lang('app.save')</x-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Offline payment (queued to localStorage; syncs via ajax.pos.sync-offline-payment) --}}
        <div id="pos-offline-payment-modal" class="fixed inset-0 z-[10018] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" aria-hidden="true">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-start gap-2">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.order.payment')</h3>
                        <p id="pos-offline-payment-order-label" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"></p>
                    </div>
                    <button type="button" id="pos-offline-payment-close" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 shrink-0 p-1" aria-label="@lang('app.close')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">@lang('modules.order.total')</span>
                        <span id="pos-offline-payment-due-display" class="font-semibold text-gray-900 dark:text-white"></span>
                    </div>
                    <div>
                        <label for="pos-offline-payment-method" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('modules.order.paymentMethod')</label>
                        <select id="pos-offline-payment-method" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="cash">@lang('modules.order.cash')</option>
                            <option value="card">@lang('modules.order.card')</option>
                            <option value="upi">@lang('modules.order.upi')</option>
                            <option value="bank_transfer">@lang('modules.order.bank_transfer')</option>
                            <option value="due">@lang('modules.order.due')</option>
                        </select>
                    </div>
                    <div id="pos-offline-payment-tendered-wrap">
                        <label for="pos-offline-payment-tendered" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">@lang('modules.order.amountPaid')</label>
                        <input type="number" step="0.01" min="0" id="pos-offline-payment-tendered" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><span id="pos-offline-payment-change-label">@lang('modules.order.change')</span>: <span id="pos-offline-payment-change-display">—</span></p>
                    </div>
                    <p id="pos-offline-payment-error" class="text-sm text-red-600 dark:text-red-400 hidden"></p>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-2">
                    <button type="button" id="pos-offline-payment-cancel" class="px-4 py-2 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">@lang('app.cancel')</button>
                    <button type="button" id="pos-offline-payment-confirm" class="px-4 py-2 text-sm rounded-md bg-skin-base text-white hover:opacity-90">@lang('app.confirm')</button>
                </div>
            </div>
        </div>

        {{-- Inline Tailwind Confirm Popover (anchored near clicked button) --}}
        <div id="posSimpleConfirmBackdrop" class="fixed inset-0 z-[10019] hidden bg-black/45 backdrop-blur-[1px]"></div>
        <div id="posSimpleConfirmModal" class="fixed z-[10020] hidden w-[18rem] max-w-[calc(100vw-1rem)] rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden">
            <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">@lang('app.confirm')</h3>
            </div>
            <div class="px-3 py-2">
                <p id="posSimpleConfirmMessage" class="text-xs text-gray-700 dark:text-gray-200 leading-relaxed"></p>
            </div>
            <div class="px-3 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-end gap-2">
                <button type="button" id="posSimpleConfirmCancel" class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    @lang('app.cancel')
                </button>
                <button type="button" id="posSimpleConfirmOk" class="px-3 py-1.5 rounded-md bg-red-600 text-white text-xs hover:bg-red-700">
                    @lang('app.ok')
                </button>
            </div>
        </div>

        {{-- Loyalty Redemption (AJAX POS) - $posLoyaltyEnabled passed from PosController (tt parity) --}}
        @if($posLoyaltyEnabled ?? false)
        <style>
            /* Ensure loyalty modal sits above other POS UI and has top spacing */
            #loyaltyRedemptionModal {
                z-index: 9999 !important;
                padding-top: 4rem !important;
            }
            @media (min-width: 640px) {
                #loyaltyRedemptionModal {
                    padding-top: 5rem !important;
                }
            }
        </style>
        <div id="loyaltyRedemptionModal" class="fixed inset-0 flex items-start justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/70 dark:border-gray-700 shadow-2xl max-w-md w-full mx-4 mt-8 overflow-hidden">
                <div class="px-6 pt-5 pb-4">
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-semibold dark:text-gray-100">
                                @lang('loyalty::app.redeemLoyaltyPoints')
                            </h3>
                        </div>
                        <button type="button" onclick="closeLoyaltyRedemptionModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        {{-- Always render this block so JS can populate when customer added in-session --}}
                        <div id="loyalty-info-block" class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
                            <p id="loyalty-customer-row" class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2" style="{{ ($customer ?? null) ? '' : 'display:none' }}">
                                <span id="loyalty-customer-name">{{ optional($customer)->name ?? '' }}</span> {{ __('loyalty::app.hasAvailablePoints') }}:
                                <span id="loyalty-available-points">{{ number_format($loyaltyPointsAvailable ?? 0) }}</span>
                                @lang('loyalty::app.points')
                            </p>
                            <p id="loyalty-no-customer-row" class="text-sm text-blue-900 dark:text-blue-200" style="{{ ($customer ?? null) ? 'display:none' : '' }}">
                                @lang('loyalty::app.noPointsAvailable')
                            </p>
                            <p class="text-xs text-blue-700 dark:text-blue-300" id="loyalty-points-value-row">
                                {{ __('loyalty::app.pointsValue') }}:
                                <span id="loyalty-points-value">0</span>
                            </p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1" id="loyalty-max-discount-row">
                                {{ __('loyalty::app.maxDiscountToday') }}:
                                <span id="loyalty-max-discount">{{ $loyaltyDiscountAmount ?? 0 }}</span>
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    @lang('loyalty::app.pointsToRedeem')
                                </label>
                                <input
                                    id="loyaltyPointsInput"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="@lang('loyalty::app.enterPoints')" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="loyalty-min-max-row" style="display:none;">
                                    <span id="loyalty-min-wrapper">
                                        {{ __('Minimum') }}:
                                        <span id="loyalty-min-points">0</span> @lang('loyalty::app.points')
                                    </span>
                                    <span id="loyalty-max-wrapper" class="ml-1" style="display:none;">
                                        | {{ __('Maximum') }}:
                                        <span id="loyalty-max-points">0</span> @lang('loyalty::app.points')
                                    </span>
                                </p>
                                <p
                                    class="mt-1 text-xs text-blue-600 dark:text-blue-400"
                                    id="loyalty-multiple-row"
                                    data-template="{{ __('Points must be in multiples of :min', ['min' => ':min']) }}"
                                    style="display:none;">
                                </p>
                                <div id="loyaltyError" class="mt-2 text-red-600 text-sm" style="display:none;"></div>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-300">
                                    @lang('loyalty::app.loyaltyDiscount')
                                </span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400" id="loyalty-discount-preview">
                                    {{ $loyaltyDiscountAmount ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-between items-center w-full pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button
                            type="button"
                            onclick="skipLoyaltyRedemption()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @lang('app.skip')
                        </button>
                        <div class="flex gap-2">
                            <button type="button" id="loyalty-use-max-btn" onclick="applyLoyaltyRedemptionMax()" style="display: none;"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                @lang('loyalty::app.useMax') (<span id="loyalty-use-max-value">0</span>)
                            </button>
                            <button
                                type="button"
                                onclick="applyLoyaltyRedemption()"
                                class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">
                                @lang('loyalty::app.applyDiscount')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Print Options Modal (AJAX/JS-based) --}}
        <div id="printOptionsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2m8-12V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.order.printOptions')</h3>
                        </div>
                        <button type="button" onclick="closePrintOptionsModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            @lang('modules.order.selectPrintOption')
                        </p>

                        {{-- Print All Option --}}
                        <button type="button" onclick="handlePrintOption('all')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all group">
                            <div class="p-3 bg-gradient-to-br from-indigo-700 to-indigo-500 rounded-lg text-white flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7z"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    @lang('modules.order.printAll')
                                </h4>
                                @if(isset($orderDetail) && $orderDetail && $orderDetail->relationLoaded('splitOrders'))
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        @lang('modules.order.printAllDesc', ['count' => $orderDetail->splitOrders->where('status', 'paid')->count() + 1])
                                    </p>
                                @endif
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                        </button>

                        {{-- Summary Only Option --}}
                        <button type="button" onclick="handlePrintOption('summary')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all group">
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

                        {{-- Individual Only Option (mapped to split receipts) --}}
                        <button type="button" onclick="handlePrintOption('individual')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all group">
                            <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg text-white flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0m6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0M7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                    @lang('modules.order.individualOnly')
                                </h4>
                                @if(isset($orderDetail) && $orderDetail && $orderDetail->relationLoaded('splitOrders'))
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        @lang('modules.order.individualOnlyDesc', ['count' => $orderDetail->splitOrders->where('status', 'paid')->count()])
                                    </p>
                                @endif
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                        </button>

                        {{-- Single Guest Option (mapped to main order print) --}}
                        <button type="button" onclick="handlePrintOption('single')" class="w-full flex items-start gap-4 p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all group">
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
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" onclick="closePrintOptionsModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('app.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Modal --}}
        <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-xl w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-red-600">@lang('app.error')</h3>
                        <button type="button" onclick="closeErrorModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="errorModalContent" class="space-y-3"></div>
                    <div class="mt-4 flex justify-end gap-2">
                        <div id="errorModalNewKotButton" style="display: none;">
                            <a href="#" id="errorModalNewKotLink" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">@lang('modules.order.newKot')</a>
                        </div>
                        <button type="button" onclick="closeErrorModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">@lang('app.close')</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modifiers Modal: compact width, scroll body, pinned actions --}}
        <div id="modifiersModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3 sm:p-4" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[min(92vh,640px)] flex flex-col overflow-hidden border border-gray-200/80 dark:border-gray-700">
                <div class="shrink-0 flex justify-between items-center gap-3 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 min-w-0">
                        <svg viewBox="0 0 32 32" width="22" height="22" fill="currentColor" class="shrink-0 text-skin-base" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 14h13V1C6.82 1 1 6.82 1 14m5-7a1 1 0 1 1 2 0 1 1 0 0 1-2 0m1.4 4.25a.751.751 0 0 1-.586-1.219l3.199-4a.751.751 0 0 1 1.172.938l-3.199 4a.75.75 0 0 1-.586.281M11 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2m4.5-6.725v-2.25C22.446 2.29 28 7.989 28 15c-.873 0-1.65.357-2.297.926.026-.306.047-.614.047-.926 0-5.759-4.555-10.461-10.25-10.725m3.097 23.21A13 13 0 0 1 15 28C7.989 28 2.29 22.446 2.025 15.5h2.25C4.539 21.195 9.241 25.75 15 25.75c1.288 0 2.518-.239 3.663-.656z"/><path d="M16.25 17.3v4.2c0 .97.43 1.838 1.107 2.434A9.2 9.2 0 0 1 15 24.25c-4.932 0-8.963-3.882-9.225-8.75H14.5a1 1 0 0 0 1-1V5.775c4.868.262 8.75 4.293 8.75 9.225 0 .178-.017.352-.027.528-.09-.014-.18-.028-.273-.028h-5.9c-.992 0-1.8.808-1.8 1.8m12.538 6.084L28.972 30a.972.972 0 1 1-1.944 0l.184-6.617c-1.132-.312-1.962-1.302-1.962-2.884 0-1.933 1.231-4 2.75-4s2.75 2.067 2.75 4c0 1.583-.83 2.573-1.962 2.885"/><path d="M24.25 17.3v4.2a1.75 1.75 0 0 1-1.75 1.75h-.715l.187 6.75a.972.972 0 1 1-1.944 0l.188-6.75H19.5a1.75 1.75 0 0 1-1.75-1.75v-4.2a.3.3 0 0 1 .3-.3h.9a.3.3 0 0 1 .3.3v4.2c0 .138.112.25.25.25h.75V17.3a.3.3 0 0 1 .3-.3h.9a.3.3 0 0 1 .3.3v4.45h.75a.25.25 0 0 0 .25-.25v-4.2a.3.3 0 0 1 .3-.3h.9a.3.3 0 0 1 .3.3"/></svg>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">@lang('modules.modifier.itemModifiers')</h3>
                    </div>
                    <button type="button" onclick="closeModifiersModal()" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-100" aria-label="@lang('app.close')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="modifiersModalContent" class="flex-1 min-h-0 overflow-hidden flex flex-col"></div>
            </div>
        </div>

        {{-- Table Change Modal --}}
        <div id="tableChangeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <svg fill="currentColor" class="w-5 h-5 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.999 44.999" xml:space="preserve">
                            <g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m42.558 23.378 2.406-10.92a1.512 1.512 0 0 0-2.954-.652l-2.145 9.733h-9.647a1.512 1.512 0 0 0 0 3.026h.573l-3.258 7.713a1.51 1.51 0 0 0 1.393 2.102c.59 0 1.15-.348 1.394-.925l2.974-7.038 4.717.001 2.971 7.037a1.512 1.512 0 1 0 2.787-1.177l-3.257-7.713h.573a1.51 1.51 0 0 0 1.473-1.187m-28.35 1.186h.573a1.512 1.512 0 0 0 0-3.026H5.134L2.99 11.806a1.511 1.511 0 1 0-2.954.652l2.406 10.92a1.51 1.51 0 0 0 1.477 1.187h.573L1.234 32.28a1.51 1.51 0 0 0 .805 1.98 1.515 1.515 0 0 0 1.982-.805l2.971-7.037 4.717-.001 2.972 7.038a1.514 1.514 0 0 0 1.982.805 1.51 1.51 0 0 0 .805-1.98z"/><path d="M24.862 31.353h-.852V18.308h8.13a1.513 1.513 0 1 0 0-3.025H12.856a1.514 1.514 0 0 0 0 3.025h8.13v13.045h-.852a1.514 1.514 0 0 0 0 3.027h4.728a1.513 1.513 0 1 0 0-3.027"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.order.changeTable')</h3>
                    </div>
                    <button onclick="closeTableChangeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4" id="setTableContainer">
                    @livewire('pos.set-table')
                </div>
                <div class="flex justify-end gap-2 p-4 border-t dark:border-gray-700">
                    <button onclick="closeTableChangeModal()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none disabled:opacity-25 transition">
                        @lang('app.close')
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/froiden-helper/helper.js') }}"></script>
@vite(['resources/js/pos-offline.js'])
<script>
// Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/**
 * Use Froiden easyAjax when loaded. After Livewire wire:navigate, helper.js may not attach again,
 * so $.easyAjax can be undefined — fall back to jQuery.ajax (same as runPosAjax for save-order).
 */
window.__posRunAjax = function(options) {
    if (typeof $ === 'undefined') {
        return;
    }
    if (typeof $.easyAjax === 'function') {
        return $.easyAjax(options);
    }
    return $.ajax({
        url: options.url,
        type: options.type || 'GET',
        data: options.data || {},
        dataType: options.dataType || 'json',
        success: options.success,
        error: options.error
    });
};

/**
 * Same rules as resources/js/pos-offline.js (navigator + window.__posForceOfflineTest).
 * Used for catalog/tax refresh and anywhere else POS should mirror offline simulation.
 */
window.__posIsEffectiveOnline = function() {
    if (typeof navigator !== 'undefined' && navigator.onLine === false) {
        return false;
    }
    if (window.__posForceOfflineTest) {
        return false;
    }
    return true;
};

@php
    $posLoyaltyConfig = [
        'enabled' => false,
        'maxDiscountPercent' => 0,
        'valuePerPoint' => 1,
        'minRedeemPoints' => 0,
    ];
    if (function_exists('module_enabled') && module_enabled('Loyalty')) {
        try {
            $posLoyaltySettings = \Modules\Loyalty\Entities\LoyaltySetting::getForRestaurant(restaurant()->id);
            if ($posLoyaltySettings && $posLoyaltySettings->isEnabled()) {
                $posLoyaltyConfig = [
                    'enabled' => true,
                    'maxDiscountPercent' => (float) ($posLoyaltySettings->max_discount_percent ?? 0),
                    'valuePerPoint' => (float) ($posLoyaltySettings->value_per_point ?? 1),
                    'minRedeemPoints' => (int) ($posLoyaltySettings->min_redeem_points ?? 0),
                ];
            }
        } catch (\Throwable $e) {
            // Keep defaults when loyalty settings are unavailable.
        }
    }
@endphp

// POS State Management
window.posState = {
    orderTypeId: {{ $orderTypeId ?? 'null' }},
    orderTypeSlug: @json($orderTypeSlug ?? ''),
    orderType: @json($orderType ?? ''),
    orderNumber: @json($orderNumber ?? ''),
    formattedOrderNumber: @json($formattedOrderNumber ?? ''),
    tableId: {{ $tableId ?? 'null' }},
    tableNo: @json($tableNo ?? ''),
    tableSeatingCapacity: @json($tableSeatingCapacity ?? null),
    tableRemainingSeats: null,
    customerId: {{ $customerId ?? 'null' }},
    customer: @json($customer ?? null),
    orderItemList: @json($orderItemList ?? []),
    orderItemVariation: @json($orderItemVariation ?? []),
    orderItemQty: @json($orderItemQty ?? []),
    orderItemAmount: @json($orderItemAmount ?? []),
    itemModifiersSelected: @json($itemModifiersSelected ?? []),
    orderItemModifiersPrice: @json($orderItemModifiersPrice ?? []),
    itemNotes: @json($itemNotes ?? []),
    modifierOptions: @json($modifierOptions ?? []),
    subTotal: {{ $subTotal ?? 0 }},
    total: {{ $total ?? 0 }},
    discountType: @json($discountType ?? ''),
    discountValue: {{ $discountValue ?? 0 }},
    discountAmount: {{ $discountAmount ?? 0 }},
    discountApplyOn: @json(optional($orderDetail)->discount_apply_on ?? (($restaurant->tax_inclusive ?? 0) ? 'total' : 'sub_total')),
    discountedTotal: {{ $discountedTotal ?? 0 }},
    loyaltyPointsRedeemed: {{ optional($orderDetail)->loyalty_points_redeemed ?? 0 }},
    loyaltyDiscountAmount: {{ optional($orderDetail)->loyalty_discount_amount ?? 0 }},
    stampDiscountAmount: {{ optional($orderDetail)->stamp_discount_amount ?? 0 }},
    hasFreeStampItems: {{ (isset($orderDetail) && $orderDetail && $orderDetail->items()->where('is_free_item_from_stamp', true)->exists()) ? 'true' : 'false' }},
    availableLoyaltyPoints: 0,
    pointsToRedeem: 0,
    maxRedeemablePoints: 0,
    minRedeemPoints: {{ (int) ($posLoyaltyConfig['minRedeemPoints'] ?? 0) }},
    valuePerPoint: {{ (float) ($posLoyaltyConfig['valuePerPoint'] ?? 1) }},
    maxDiscountPercent: {{ (float) ($posLoyaltyConfig['maxDiscountPercent'] ?? 0) }},
    loyaltyPointsValue: 0,
    maxLoyaltyDiscount: 0,
    deliveryFee: {{ $deliveryFee ?? 0 }},
    tipAmount: {{ $tipAmount ?? 0 }},
    totalTaxAmount: {{ $totalTaxAmount ?? 0 }},
    orderItemTaxDetails: @json($orderItemTaxDetails ?? []),
    // Pickup date/time (for pickup orders) – initialize from PHP so JS validation works even before user changes inputs
    // IMPORTANT: use restaurant()->date_format to match x-datepicker output and backend parsing.
    // Priority: explicit $pickupDate → existing order's pickup_date → "today" in restaurant date format
    pickupDate: @json(
        $pickupDate
        ?? (
            isset($orderDetail, $orderDetail->pickup_date) && $orderDetail->pickup_date
                ? $orderDetail->pickup_date->format(restaurant()->date_format ?? (global_setting()->date_format ?? 'd-m-Y'))
                : null
        )
        ?? now(restaurant()->timezone)->format(restaurant()->date_format ?? (global_setting()->date_format ?? 'd-m-Y'))
    ),
    pickupTime: @json(
        $pickupTime
        ?? (
            isset($orderDetail, $orderDetail->pickup_date) && $orderDetail->pickup_date
                ? $orderDetail->pickup_date->copy()->format('H:i')
                : null
        )
        ?? now(restaurant()->timezone)->format('H:i')
    ),
    noOfPax: {{ $noOfPax ?? 1 }},
    selectWaiter: {{ $selectWaiter ?? 'null' }},
    selectedDeliveryExecutive: {{ $selectDeliveryExecutive ?? 'null' }},
    selectedDeliveryApp: {{ $selectedDeliveryApp ?? 'null' }},
    // Hotel room service (AJAX POS) — keep keys present so Hotel partials / Alpine never read undefined.
    selectedStayId: @json(isset($selectedStayId) ? (int) $selectedStayId : null),
    selectedStayRoomNumber: @json($orderDetail->context_room_number ?? null),
    selectedStayNumber: @json($orderDetail->context_stay_number ?? null),
    billTo: @json($billTo ?? 'POST_TO_ROOM'),
    orderID: {{ $orderID ?? 'null' }},
    orderStatus: @json($orderStatus ?? 'confirmed'),
    deliveryDateTime: @json($deliveryDateTime ?? ''),
    orderNote: @json($orderNote ?? ''),
    orderDetail: @json($orderDetail ?? null),
    menuItem: null,
    selectedModifierItem: null,
    showOrderDetail: {{ request()->get('show-order-detail') == 'true' ? 'true' : 'false' }},
    canCreateOrder: {{ user_can('Create Order') ? 'true' : 'false' }},
    canUpdateOrder: {{ user_can('Update Order') ? 'true' : 'false' }},
    canBillAndPayment: {{ user_can('Bill and Payment') ? 'true' : 'false' }},
    canDeleteItemAfterKot: {{ user_can('Delete Item After KOT') ? 'true' : 'false' }},
    // Flag to indicate totals were pre-calculated by PHP (e.g., from merged tables or existing orders)
    totalsPreCalculated: {{ ($subTotal > 0 || $total > 0) ? 'true' : 'false' }},
    isWaiterLocked: {{ $isWaiterLocked ? 'true' : 'false' }},
    loyaltyEnabled: {{ ($posLoyaltyEnabled ?? false) ? 'true' : 'false' }}
};

/** Whether the current user may remove a cart line (KOT lines need Delete Item After KOT). */
window.__posNormalizeCartItemKey = function(itemKey) {
    return String(itemKey || '').replace(/"/g, '');
};

/** Resolve cart key as stored in posState (Livewire may wrap keys in quotes). */
window.__posResolveCartItemKey = function(itemKey) {
    const clean = window.__posNormalizeCartItemKey(itemKey);
    if (!window.posState?.orderItemList) {
        return clean;
    }
    if (window.posState.orderItemList[clean] !== undefined) {
        return clean;
    }
    const quoted = '"' + clean + '"';
    if (window.posState.orderItemList[quoted] !== undefined) {
        return quoted;
    }
    return itemKey;
};

window.__posCanDeleteCartItem = function(itemKey) {
    if (!itemKey || !window.posState) {
        return false;
    }
    itemKey = window.__posNormalizeCartItemKey(itemKey);
    const parts = itemKey.split('_');
    if (parts.length >= 3 && parts[0] === 'kot') {
        return !!window.posState.canDeleteItemAfterKot;
    }
    if (parts.length >= 3 && parts[0] === 'order' && parts[1] === 'item') {
        return !!window.posState.canUpdateOrder;
    }
    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : (window.posState.orderID || null);
    if (orderId) {
        return !!window.posState.canUpdateOrder;
    }
    return !!window.posState.canCreateOrder;
};

/** Toast message when pax exceeds assigned table remaining seats (placeholders :pax :table :remaining). */
window.__posPaxExceedsTableMsgTpl = @json(':pax pax exceeds remaining seats (:remaining) for table :table.');

/**
 * Whether a table cannot be selected because it already has another active order.
 */
window.__posTableBlockedForSelection = function(table, currentOrderId) {
    if (!table || !table.has_active_order) {
        return false;
    }
    const activeOrderId = table.active_order_id != null ? parseInt(table.active_order_id, 10) : null;
    const currentId = currentOrderId != null ? parseInt(currentOrderId, 10) : null;
    if (!activeOrderId) {
        return false;
    }
    return !currentId || activeOrderId !== currentId;
};

window.__posShowTableHasActiveOrderMessage = function(tableCode) {
    const tpl = @json(__('messages.tableHasActiveOrder', ['table' => ':table']));
    const message = tpl.replace(':table', tableCode || '');
    if (typeof window.showToast === 'function') {
        window.showToast('error', message);
    } else {
        alert(message);
    }
};

/** True when MultiPOS requires registration / pending / declined — block cart actions even if menu is reachable (e.g. z-index overlap). */
window.__multiposBlocksPosInteraction = {{ !empty($shouldBlockPos) ? 'true' : 'false' }};

/** Server-rendered order context — used to drop stale Livewire/SPA posState after navigating to bare /pos. */
window.__posBootstrapHasServerOrder = {{ ($orderID || (isset($orderDetail) && $orderDetail)) ? 'true' : 'false' }};

/**
 * Align client posState with this page load so leftover orderDetail/showOrderDetail cannot block adds on a fresh POS.
 */
window.__posNormalizeClientOrderContext = function() {
    if (!window.posState) {
        return;
    }
    try {
        var sp = new URLSearchParams(window.location.search || '');
        var urlShowDetail = sp.get('show-order-detail') === 'true';
        if (!window.__posBootstrapHasServerOrder) {
            window.posState.orderID = null;
            window.posState.orderDetail = null;
            window.posState.showOrderDetail = false;
            return;
        }
        if (!urlShowDetail) {
            window.posState.showOrderDetail = false;
        }
    } catch (e) {
        /* ignore */
    }
};
window.__posNormalizeClientOrderContext();

// Persist in-progress POS draft cart across page reloads (new draft only).
window.__posDraftStorage = (function() {
    const branchId = {{ (int) branch()->id }};
    const key = 'pos_draft_cart_v1_' + branchId;
    let saveTimer = null;

    function canUseStorage() {
        try {
            return typeof window.localStorage !== 'undefined';
        } catch (e) {
            return false;
        }
    }

    function shouldRestoreDraft() {
        const isTruthyFlag = function(v) {
            return v === true || v === 1 || v === '1' || v === 'true';
        };
        const hasServerOrder = !!(
            window.posState &&
            (
                window.posState.orderID ||
                (window.posState.orderDetail && window.posState.orderDetail.id)
            )
        );
        const inOrderDetailMode = !!(window.posState && isTruthyFlag(window.posState.showOrderDetail));
        return !hasServerOrder && !inOrderDetailMode;
    }

    function snapshot() {
        const s = window.posState || {};
        const toPlainObjectMap = function(source) {
            const obj = {};
            const src = source || {};
            Object.keys(src).forEach(function(k) {
                obj[k] = src[k];
            });
            return obj;
        };

        const list = toPlainObjectMap(s.orderItemList);
        const keyOrder = Object.keys(list);
        return {
            saved_at: Date.now(),
            orderTypeId: s.orderTypeId || null,
            orderTypeSlug: s.orderTypeSlug || '',
            orderType: s.orderType || '',
            tableId: s.tableId || null,
            tableNo: s.tableNo || '',
            customerId: s.customerId || null,
            customer: s.customer || null,
            selectWaiter: s.selectWaiter || null,
            orderItemList: list,
            orderItemOrder: keyOrder,
            orderItemVariation: toPlainObjectMap(s.orderItemVariation),
            orderItemQty: toPlainObjectMap(s.orderItemQty),
            orderItemAmount: toPlainObjectMap(s.orderItemAmount),
            itemModifiersSelected: toPlainObjectMap(s.itemModifiersSelected),
            orderItemModifiersPrice: toPlainObjectMap(s.orderItemModifiersPrice),
            itemNotes: toPlainObjectMap(s.itemNotes),
            modifierOptions: toPlainObjectMap(s.modifierOptions),
            orderItemTaxDetails: toPlainObjectMap(s.orderItemTaxDetails),
            discountType: s.discountType || '',
            discountValue: s.discountValue || 0,
            discountAmount: s.discountAmount || 0,
            discountApplyOn: s.discountApplyOn || ((window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }}) ? 'total' : 'sub_total'),
            deliveryFee: s.deliveryFee || 0,
            tipAmount: s.tipAmount || 0,
            orderNote: s.orderNote || ''
        };
    }

    function saveNow() {
        if (window.__posDraftRestoreCompleted !== true) {
            return;
        }
        if (!canUseStorage() || !shouldRestoreDraft()) {
            return;
        }
        try {
            const cartSize = Object.keys(window.posState?.orderItemList || {}).length;
            if (!cartSize) {
                // Do not auto-wipe persisted draft on transient empty states during boot/render.
                // Draft is explicitly cleared via reset/submit/manual clear flows.
                return;
            }
            window.localStorage.setItem(key, JSON.stringify(snapshot()));
        } catch (e) {
            // Ignore storage quota/private mode errors.
        }
    }

    function saveDebounced() {
        if (saveTimer) {
            clearTimeout(saveTimer);
        }
        saveTimer = setTimeout(saveNow, 150);
    }

    function restore() {
        if (!canUseStorage() || !shouldRestoreDraft()) {
            return false;
        }
        try {
            const raw = window.localStorage.getItem(key);
            if (!raw) {
                return false;
            }
            const data = JSON.parse(raw);
            if (!data || typeof data !== 'object') {
                return false;
            }
            if (!data.orderItemList || !Object.keys(data.orderItemList).length) {
                return false;
            }

            const rawOrderItemList = data.orderItemList || {};
            const rawOrderItemOrder = Array.isArray(data.orderItemOrder) ? data.orderItemOrder : [];
            const reorderedOrderItemList = {};

            if (rawOrderItemOrder.length) {
                rawOrderItemOrder.forEach(function(k) {
                    if (Object.prototype.hasOwnProperty.call(rawOrderItemList, k)) {
                        reorderedOrderItemList[k] = rawOrderItemList[k];
                    }
                });
                Object.keys(rawOrderItemList).forEach(function(k) {
                    if (!Object.prototype.hasOwnProperty.call(reorderedOrderItemList, k)) {
                        reorderedOrderItemList[k] = rawOrderItemList[k];
                    }
                });
            } else {
                Object.keys(rawOrderItemList).forEach(function(k) {
                    reorderedOrderItemList[k] = rawOrderItemList[k];
                });
            }

            Object.assign(window.posState, {
                orderTypeId: data.orderTypeId ?? window.posState.orderTypeId,
                orderTypeSlug: data.orderTypeSlug ?? window.posState.orderTypeSlug,
                orderType: data.orderType ?? window.posState.orderType,
                tableId: data.tableId ?? window.posState.tableId,
                tableNo: data.tableNo ?? window.posState.tableNo,
                customerId: data.customerId ?? window.posState.customerId,
                customer: data.customer ?? window.posState.customer,
                selectWaiter: data.selectWaiter ?? window.posState.selectWaiter,
                orderItemList: reorderedOrderItemList,
                orderItemVariation: data.orderItemVariation || {},
                orderItemQty: data.orderItemQty || {},
                orderItemAmount: data.orderItemAmount || {},
                itemModifiersSelected: data.itemModifiersSelected || {},
                orderItemModifiersPrice: data.orderItemModifiersPrice || {},
                itemNotes: data.itemNotes || {},
                modifierOptions: data.modifierOptions || {},
                orderItemTaxDetails: data.orderItemTaxDetails || {},
                discountType: data.discountType || '',
                discountValue: Number(data.discountValue || 0),
                discountAmount: Number(data.discountAmount || 0),
                discountApplyOn: data.discountApplyOn || ((window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }}) ? 'total' : 'sub_total'),
                deliveryFee: Number(data.deliveryFee || 0),
                tipAmount: Number(data.tipAmount || 0),
                orderNote: data.orderNote || ''
            });
            return true;
        } catch (e) {
            return false;
        }
    }

    function clear() {
        if (!canUseStorage()) {
            return;
        }
        try {
            window.localStorage.removeItem(key);
        } catch (e) {
            // Ignore
        }
    }

    return {
        saveNow,
        saveDebounced,
        restore,
        clear
    };
})();

window.persistPosDraftCart = function() {
    if (window.__posDraftStorage && typeof window.__posDraftStorage.saveDebounced === 'function') {
        window.__posDraftStorage.saveDebounced();
    }
};

window.clearPersistedPosDraftCart = function() {
    if (window.__posDraftStorage && typeof window.__posDraftStorage.clear === 'function') {
        window.__posDraftStorage.clear();
    }
};

function restorePosDraftCartOnLoad() {
    window.__posDraftRestoreCompleted = false;
    const restored = window.__posDraftStorage?.restore?.();
    if (restored) {
        if (typeof window.updateOrderItemsContainer === 'function') {
            window.updateOrderItemsContainer();
        }
        if (typeof window.calculateTotal === 'function') {
            window.calculateTotal();
        }
        if (typeof window.updateCustomerDisplay === 'function') {
            window.updateCustomerDisplay(window.posState.customer || null);
        }
        if (typeof window.updateTableDisplay === 'function' && window.posState.tableId && window.posState.tableNo) {
            window.updateTableDisplay({ id: window.posState.tableId, table_code: window.posState.tableNo });
        }
    }
    // Enable persistence only after restore attempt to prevent empty-boot overwrite.
    window.__posDraftRestoreCompleted = true;
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.__posNormalizeClientOrderContext === 'function') {
            window.__posNormalizeClientOrderContext();
        }
        restorePosDraftCartOnLoad();
    });
} else {
    if (typeof window.__posNormalizeClientOrderContext === 'function') {
        window.__posNormalizeClientOrderContext();
    }
    restorePosDraftCartOnLoad();
}

window.addEventListener('beforeunload', function() {
    window.__posDraftStorage?.saveNow?.();
});

/**
 * Current order id for POS AJAX (parity with Livewire: orderID || orderDetail.id || server-rendered order).
 * Without this, waiter/table APIs skip when only orderDetail is populated.
 */
window.getCurrentPosOrderId = function() {
    const toPositiveInt = function(v) {
        if (v === null || v === undefined || v === '' || v === 'null') {
            return null;
        }
        const n = parseInt(String(v), 10);
        return (Number.isNaN(n) || n <= 0) ? null : n;
    };
    let id = toPositiveInt(window.posState?.orderID);
    if (id) {
        return id;
    }
    id = toPositiveInt(window.posState?.orderDetail?.id);
    if (id) {
        return id;
    }
    if (window.__posForceFreshOrder === true) {
        return null;
    }
    if (typeof window.__posInitialServerOrderId === 'undefined') {
        window.__posInitialServerOrderId = toPositiveInt(@json(optional($orderDetail)->id ?? null));
    }
    return toPositiveInt(window.__posInitialServerOrderId);
};

// Unified free-stamp detector (tt parity + robust note fallback).
window.isFreeStampItemByMeta = function(itemKey, itemMeta, itemNote) {
    const key = (itemKey || '').toString();
    const item = itemMeta || {};
    const note = (itemNote || '').toString();
    const rawFlag = item.is_free_item_from_stamp;
    const byFlag = rawFlag === true || rawFlag === 1 || rawFlag === '1'
        || (typeof rawFlag === 'string' && rawFlag.toLowerCase() === 'true');
    const byKey = key.startsWith('free_stamp_');
    const noteToken = @json(strtolower(__('loyalty::app.freeItemFromStamp')));
    const noteLower = note.toLowerCase();
    const byToken = noteToken && noteLower.includes(noteToken);
    const byGenericFreeStamp = noteLower.includes('free') && (noteLower.includes('stamp') || noteLower.includes('(st'));
    const byGenericFreeItem = noteLower.startsWith('free') || noteLower.includes('free item');
    return byKey || byFlag || byToken || byGenericFreeStamp || byGenericFreeItem;
};

// TT parity: auto-apply stamp preview in cart when customer is selected first.
window.autoApplyStampPreviewForItem = function(itemKey) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    const item = window.posState?.orderItemList?.[itemKey];
    if (!item || !window.posState?.customerId) {
        return;
    }

    const note = window.posState?.itemNotes?.[itemKey] || '';
    if (window.isFreeStampItemByMeta(itemKey, item, note)) {
        return;
    }

    const qty = Math.max(1, parseInt(window.posState?.orderItemQty?.[itemKey] || 1, 10));
    // Use variation price when present, else base item price; add modifier price so stamp preview matches displayed line total.
    const variation = window.posState?.orderItemVariation?.[itemKey];
    const basePrice = variation
        ? parseFloat(variation.price ?? item.price ?? 0)
        : parseFloat(item.price || 0);
    const modifierPrice = parseFloat(window.posState?.orderItemModifiersPrice?.[itemKey] || 0);
    const unitPrice = basePrice + modifierPrice;

    window.posLoyaltyApi.stampPreview({
        customer_id: window.posState.customerId,
        menu_item_id: item.id,
        quantity: qty,
        unit_price: unitPrice
    }, function(response) {
            if (!response || !response.success) {
                return;
            }

            const ruleId = parseInt(response.rule_id || 0, 10);
            const keyPrefix = ruleId > 0 ? `free_stamp_${ruleId}` : null;
            const props = [
                'orderItemList',
                'orderItemQty',
                'orderItemAmount',
                'orderItemVariation',
                'itemModifiersSelected',
                'orderItemModifiersPrice',
                'itemNotes',
                'orderItemTaxDetails'
            ];
            const removeKeyFromState = function(key) {
                props.forEach(function(prop) {
                    if (window.posState?.[prop] && window.posState[prop][key] !== undefined) {
                        delete window.posState[prop][key];
                    }
                });
                if (window.posState?.freeStampSourceByKey && window.posState.freeStampSourceByKey[key] !== undefined) {
                    delete window.posState.freeStampSourceByKey[key];
                }
            };

            // Only cleanup free-stamp keys and return when we're NOT going to apply a discount.
            // For discount_amount/discount_percent we must fall through to the block below.
            const isDiscountResponse = response.applied
                && (response.reward_type === 'discount_amount' || response.reward_type === 'discount_percent')
                && (typeof response.preview_discount_amount !== 'undefined' && parseFloat(response.preview_discount_amount) > 0);
            if (keyPrefix && !isDiscountResponse && (!response.applied || response.reward_type !== 'free_item' || !response.reward_item)) {
                Object.keys(window.posState?.orderItemList || {}).forEach(function(existingKey) {
                    if (existingKey.indexOf(keyPrefix) === 0) {
                        removeKeyFromState(existingKey);
                    }
                });
                // Clear stamp discount note and amount for this item when stamp no longer applied
                if (window.posState.orderItemList[itemKey] && window.posState.orderItemList[itemKey].stamp_rule_id) {
                    window.posState.orderItemList[itemKey].stamp_rule_id = null;
                    window.posState.orderItemAmount[itemKey] = Math.max(0, unitPrice * qty);
                    var stampLabel = (window.posConfig && window.posConfig.stampDiscountLabel) || 'Stamp Discount';
                    var existingNote = (window.posState.itemNotes && window.posState.itemNotes[itemKey]) || '';
                    if (existingNote && typeof existingNote === 'string' && existingNote.indexOf(stampLabel) !== -1) {
                        var parts = existingNote.split('|').map(function(s) { return s.trim(); }).filter(function(s) { return s.indexOf(stampLabel) !== 0; });
                        window.posState.itemNotes[itemKey] = parts.join(' | ').trim();
                    }
                }

                window.updateOrderItemsContainer?.();
                window.calculateTotal?.();
                return;
            }

            if (response.reward_type === 'free_item' && response.reward_item && keyPrefix) {
                const reward = response.reward_item;
                const appliedQty = Math.max(1, parseInt(response.applied_qty || 1, 10));

                // Single canonical key per rule so only one free-item card ever shows
                const freeKey = keyPrefix;
                Object.keys(window.posState?.orderItemList || {}).forEach(function(existingKey) {
                    if (existingKey !== freeKey && existingKey.indexOf(keyPrefix) === 0) {
                        removeKeyFromState(existingKey);
                    }
                });

                window.posState.orderItemList[freeKey] = {
                    ...reward,
                    is_free_item_from_stamp: true,
                    stamp_rule_id: ruleId
                };
                window.posState.orderItemQty[freeKey] = appliedQty;
                window.posState.orderItemAmount[freeKey] = 0;
                window.posState.itemNotes[freeKey] = response.free_item_note || @json(__('loyalty::app.freeItemFromStamp'));
                window.posState.orderItemVariation[freeKey] = response.reward_variation || null;
                window.posState.itemModifiersSelected[freeKey] = [];
                window.posState.orderItemModifiersPrice[freeKey] = 0;
                if (window.posState.orderItemTaxDetails && window.posState.orderItemTaxDetails[freeKey] !== undefined) {
                    delete window.posState.orderItemTaxDetails[freeKey];
                }
                if (!window.posState.freeStampSourceByKey) {
                    window.posState.freeStampSourceByKey = {};
                }
                window.posState.freeStampSourceByKey[freeKey] = itemKey;

                window.posState.hasFreeStampItems = true;

                window.updateOrderItemsContainer?.();
                window.calculateTotal?.();
                return;
            }

            // Handle percentage / fixed-amount stamp rewards.
            // Be tolerant of different backend reward_type labels by relying primarily on preview_discount_amount.
            if (
                response.applied
                && typeof response.preview_discount_amount !== 'undefined'
                && response.preview_discount_amount !== null
                && parseFloat(response.preview_discount_amount) > 0
                && (response.reward_type !== 'free_item')
            ) {
                const discountAmount = Math.max(0, parseFloat(response.preview_discount_amount || 0));
                const expectedAmount = Math.max(0, unitPrice * qty);
                window.posState.orderItemAmount[itemKey] = Math.max(0, expectedAmount - discountAmount);
                window.posState.orderItemList[itemKey].stamp_rule_id = ruleId || null;
                window.posState.stampDiscountAmount = discountAmount;

                // Set note on item like free stamp items: "Stamp Discount: -$12.00" (saved with order)
                var stampLabel = (window.posConfig && window.posConfig.stampDiscountLabel) || 'Stamp Discount';
                var existingNote = (window.posState.itemNotes && window.posState.itemNotes[itemKey]) || '';
                var stampNotePart = stampLabel + ': -' + (typeof window.formatCurrency === 'function' ? window.formatCurrency(discountAmount) : ('$' + discountAmount.toFixed(2)));
                var otherParts = existingNote && typeof existingNote === 'string'
                    ? existingNote.split('|').map(function(s) { return s.trim(); }).filter(function(s) { return s.indexOf(stampLabel) !== 0; })
                    : [];
                window.posState.itemNotes[itemKey] = (otherParts.join(' | ').trim() ? otherParts.join(' | ').trim() + ' | ' : '') + stampNotePart;

                window.updateOrderItemsContainer?.();
                window.calculateTotal?.();
            }
    });
};

window.refreshAutoStampPreviews = function() {
    if (!window.posState?.customerId) {
        return;
    }
    Object.keys(window.posState?.orderItemList || {}).forEach(function(key) {
        const item = window.posState?.orderItemList?.[key] || {};
        const note = window.posState?.itemNotes?.[key] || '';
        if (!window.isFreeStampItemByMeta(key, item, note)) {
            window.autoApplyStampPreviewForItem(key);
        }
    });
};

window.getNonFreeCartSummary = function() {
    let nonFreeLineCount = 0;
    let nonFreeQtyTotal = 0;

    Object.keys(window.posState?.orderItemList || {}).forEach(function(key) {
        const item = window.posState?.orderItemList?.[key] || {};
        const note = window.posState?.itemNotes?.[key] || '';
        if (window.isFreeStampItemByMeta(key, item, note)) {
            return;
        }

        nonFreeLineCount++;
        const qty = Math.max(0, parseInt(window.posState?.orderItemQty?.[key] || 1, 10) || 0);
        nonFreeQtyTotal += qty;
    });

    return {
        nonFreeLineCount,
        nonFreeQtyTotal
    };
};

// Tax and charge configuration for JavaScript calculations
@php
    // Keep JS tax config in sync with what should be displayed:
    // - existing order: use attached order taxes
    // - fresh order: use branch/restaurant tax list
    $posTaxes = [];

    if (isset($orderDetail) && $orderDetail && $orderDetail->relationLoaded('taxes') && $orderDetail->taxes->count() > 0) {
        $posTaxes = $orderDetail->taxes
            ->filter(fn($ot) => $ot->tax)
            ->map(fn($ot) => [
                'id' => $ot->tax->id,
                'tax_name' => $ot->tax->tax_name,
                'tax_percent' => $ot->tax->tax_percent,
            ])
            ->unique('id')
            ->values()
            ->toArray();
    } else {
        $posTaxes = collect($taxes ?? [])
            ->map(fn($tax) => [
                'id' => $tax->id ?? null,
                'tax_name' => $tax->tax_name ?? '',
                'tax_percent' => $tax->tax_percent ?? 0,
            ])
            ->filter(fn($tax) => !empty($tax['tax_name']))
            ->unique(function ($tax) {
                return $tax['id'] ?: strtolower(trim($tax['tax_name']));
            })
            ->values()
            ->toArray();
    }
@endphp

@php
    $posTaxRevision = \Illuminate\Support\Facades\Cache::get('pos.tax.rev.branch.' . (int) branch()->id, 0);
    $posOnSetting = getOrderNumberSetting(branch()->id);
    $posOrderNumberFormat = [
        'enable' => (bool) ($posOnSetting->enable_feature ?? false),
        'prefix' => (string) ($posOnSetting->prefix ?? ''),
        'separator' => (string) ($posOnSetting->separator ?? '-'),
        'digits' => (int) ($posOnSetting->digits ?? 4),
        'includeDate' => (bool) ($posOnSetting->include_date ?? false),
        'showYear' => (bool) ($posOnSetting->show_year ?? false),
        'showMonth' => (bool) ($posOnSetting->show_month ?? false),
        'showDay' => (bool) ($posOnSetting->show_day ?? false),
        'showTime' => (bool) ($posOnSetting->show_time ?? false),
        'resetDaily' => (bool) ($posOnSetting->reset_daily ?? false),
    ];
    $posBusinessDayUtc = null;
    try {
        $bd = getBusinessDayBoundaries(branch(), now(restaurant()->timezone ?? 'UTC'));
        $posBusinessDayUtc = [
            'start' => $bd['start']->copy()->utc()->toIso8601String(),
            'end' => $bd['end']->copy()->utc()->toIso8601String(),
        ];
    } catch (\Throwable $e) {
        $posBusinessDayUtc = null;
    }

    $posEuSelectable = restaurant()->selectableEuAllergenKeys();
    $posEuAllergensEnabled = count($posEuSelectable) > 0;
    $posEuKeyOrder = array_values(array_intersect(
        \App\Support\EuAnnexIiAllergens::keys(),
        $posEuSelectable
    ));
    $posEuIconUrls = [];
    $posEuLabels = [];
    foreach ($posEuSelectable as $k) {
        $posEuIconUrls[$k] = \App\Support\EuAnnexIiAllergens::defaultIconUrl($k);
        $posEuLabels[$k] = __(\App\Support\EuAnnexIiAllergens::langKey($k));
    }
    $posDietaryLabelOrder = \App\Support\DietaryLabels::keys();
    $posDietaryLabelText = [];
    $posDietaryLabelIconUrls = [];
    foreach ($posDietaryLabelOrder as $k) {
        $posDietaryLabelText[$k] = __(\App\Support\DietaryLabels::langKey($k));
        $posDietaryLabelIconUrls[$k] = \App\Support\DietaryLabels::defaultIconUrl($k);
    }

@endphp

window.posConfig = {
    branchId: {{ (int) branch()->id }},
    locale: @json(app()->getLocale()),
    taxRevision: {{ (int) $posTaxRevision }},
    taxMode: @json($taxMode ?? 'order'),
    taxInclusive: {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }},
    orderPrefixEnabled: {{ isOrderPrefixEnabled() ? 'true' : 'false' }},
    restaurantTimezone: @json($restaurant->timezone ?? 'UTC'),
    orderNumberFormat: @json($posOrderNumberFormat),
    posBusinessDayUtc: @json($posBusinessDayUtc),
    includeChargesInTaxBase: {{ $includeChargesInTaxBase ?? true ? 'true' : 'false' }},
    taxes: @json($posTaxes),
    extraCharges: @json($extraCharges ?? []),
    currencyId: {{ $restaurant->currency_id ?? 1 }},
    currencyCode: @json($restaurant->currency->currency_code ?? 'USD'),
    currencySymbol: @json($restaurant->currency->currency_symbol ?? '$'),
    moveToLabel: @json(__('modules.order.moveTo')),
    stampDiscountLabel: @json(__('app.stampDiscount')),
    hideMenuItemImageOnPos: {{ (restaurant() && restaurant()->hide_menu_item_image_on_pos) ? 'true' : 'false' }},
    posEuAllergensEnabled: {{ $posEuAllergensEnabled ? 'true' : 'false' }},
    posEuAllergenKeyOrder: @json($posEuKeyOrder),
    posEuAllergenIconUrls: @json($posEuIconUrls),
    posEuAllergenLabels: @json($posEuLabels),
    posEuAllergenGroupAriaLabel: @json(__('modules.settings.euAllergensFicTitle')),
    posDietaryLabelOrder: @json($posDietaryLabelOrder),
    posDietaryLabelText: @json($posDietaryLabelText),
    posDietaryLabelIconUrls: @json($posDietaryLabelIconUrls),
    posDietaryLabelsAria: @json(__('modules.menu.dietaryLabelsSectionTitle')),
    loyaltyEnabled: {{ ($posLoyaltyConfig['enabled'] ?? false) ? 'true' : 'false' }},
    loyaltyMaxDiscountPercent: {{ (float) ($posLoyaltyConfig['maxDiscountPercent'] ?? 0) }},
    loyaltyValuePerPoint: {{ (float) ($posLoyaltyConfig['valuePerPoint'] ?? 1) }},
    loyaltyMinRedeemPoints: {{ (int) ($posLoyaltyConfig['minRedeemPoints'] ?? 0) }}
};

window.__posEscapeHtmlAttr = function(s) {
    if (s == null || s === '') {
        return '';
    }
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
};

window.__posBuildAllergenIconsHtml = function(storedKeys, opts) {
    opts = opts || {};
    var inActionRow = !!opts.inActionRow;
    if (!window.posConfig || !window.posConfig.posEuAllergensEnabled) {
        return '';
    }
    if (!Array.isArray(storedKeys) || !storedKeys.length) {
        return '';
    }
    var order = window.posConfig.posEuAllergenKeyOrder || [];
    var urls = window.posConfig.posEuAllergenIconUrls || {};
    var labels = window.posConfig.posEuAllergenLabels || {};
    var ordered = [];
    for (var i = 0; i < order.length; i++) {
        if (storedKeys.indexOf(order[i]) !== -1) {
            ordered.push(order[i]);
        }
    }
    if (!ordered.length) {
        return '';
    }
    var parts = [];
    for (var j = 0; j < ordered.length; j++) {
        var k = ordered[j];
        var u = urls[k];
        if (!u) {
            continue;
        }
        var t = labels[k] || k;
        var tipClasses =
            'pointer-events-none absolute left-1/2 top-full z-30 mt-1.5 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-[10px] font-medium text-white opacity-0 shadow-lg transition-opacity duration-150 invisible group-hover:opacity-100 group-hover:visible dark:bg-gray-700';
        parts.push(
            '<span class="group relative inline-flex shrink-0">' +
                '<img src="' +
                window.__posEscapeHtmlAttr(u) +
                '" alt="" aria-label="' +
                window.__posEscapeHtmlAttr(t) +
                '" class="h-5 w-5 object-contain" width="20" height="20" loading="lazy" />' +
                '<span class="' +
                tipClasses +
                '" role="tooltip" aria-hidden="true">' +
                window.__posEscapeHtmlAttr(t) +
                '</span>' +
                '</span>'
        );
    }
    if (!parts.length) {
        return '';
    }
    var wrapClass = inActionRow
        ? 'inline-flex flex-wrap items-center gap-1 shrink-0'
        : 'inline-flex flex-wrap items-center gap-1 mt-0.5';
    var aria = window.posConfig.posEuAllergenGroupAriaLabel || 'Allergens';
    return (
        '<div class="' +
        wrapClass +
        '" role="group" aria-label="' +
        window.__posEscapeHtmlAttr(aria) +
        '">' +
        parts.join('') +
        '</div>'
    );
};

window.__posGetEuAllergenKeysFromMenuInput = function($input) {
    if (!$input || !$input.length) {
        return [];
    }
    var raw = $input.attr('data-item-eu-allergens');
    if (!raw || typeof raw !== 'string') {
        return [];
    }
    try {
        var parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed.filter(function(k) {
            return typeof k === 'string';
        }) : [];
    } catch (e) {
        return [];
    }
};

window.__posGetDietaryLabelsFromMenuInput = function($input) {
    if (!$input || !$input.length) {
        return [];
    }
    var raw = $input.attr('data-item-dietary-labels');
    if (!raw || typeof raw !== 'string') {
        return [];
    }
    try {
        var parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed.filter(function(k) {
            return typeof k === 'string';
        }) : [];
    } catch (e) {
        return [];
    }
};

window.__posBuildDietaryLabelsHtml = function(storedKeys, opts) {
    opts = opts || {};
    var inActionRow = !!opts.inActionRow;
    if (!window.posConfig) {
        return '';
    }
    if (!Array.isArray(storedKeys) || !storedKeys.length) {
        return '';
    }
    var order = window.posConfig.posDietaryLabelOrder || [];
    var labels = window.posConfig.posDietaryLabelText || {};
    var iconUrls = window.posConfig.posDietaryLabelIconUrls || {};
    if (!order.length) {
        return '';
    }
    var allowed = {};
    for (var i = 0; i < order.length; i++) {
        allowed[order[i]] = true;
    }
    var tipClasses =
        'pointer-events-none absolute left-1/2 top-full z-30 mt-1.5 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-[10px] font-medium text-white opacity-0 shadow-lg transition-opacity duration-150 invisible group-hover:opacity-100 group-hover:visible dark:bg-gray-700';
    var parts = [];
    for (var j = 0; j < order.length; j++) {
        var k = order[j];
        if (storedKeys.indexOf(k) !== -1 && allowed[k]) {
            var t = labels[k] || k;
            var u = iconUrls[k] || '';
            if (!u) {
                continue;
            }
            parts.push(
                '<span class="group relative inline-flex shrink-0">' +
                    '<img src="' +
                    window.__posEscapeHtmlAttr(u) +
                    '" alt="" aria-label="' +
                    window.__posEscapeHtmlAttr(t) +
                    '" class="h-5 w-5 object-contain" width="20" height="20" loading="lazy" />' +
                    '<span class="' +
                    tipClasses +
                    '" role="tooltip" aria-hidden="true">' +
                    window.__posEscapeHtmlAttr(t) +
                    '</span>' +
                    '</span>'
            );
        }
    }
    if (!parts.length) {
        return '';
    }
    var wrapClass = inActionRow
        ? 'inline-flex flex-wrap items-center gap-1 shrink-0'
        : 'inline-flex flex-wrap items-center gap-1 mt-0.5';
    var aria = window.posConfig.posDietaryLabelsAria || 'Additional options';
    return (
        '<div class="' +
        wrapClass +
        '" role="group" aria-label="' +
        window.__posEscapeHtmlAttr(aria) +
        '">' +
        parts.join('') +
        '</div>'
    );
};

window.syncPosTaxRevisionCache = function() {
    if (!window.posConfig || typeof window.localStorage === 'undefined') {
        return;
    }

    const branchId = window.posConfig.branchId;
    const currentRevision = String(window.posConfig.taxRevision ?? 0);
    const markerKey = `pos_tax_revision_branch_${branchId}`;

    let previousRevision = null;
    try {
        previousRevision = window.localStorage.getItem(markerKey);
    } catch (e) {
        return;
    }

    if (previousRevision === currentRevision) {
        return;
    }

    try {
        // POS menu catalog cache used by Blade POS menu (order-type scoped keys).
        const catalogPrefix = `pos_menu_catalog_v4_${branchId}_`;
        const keysToRemove = [];
        for (let i = 0; i < window.localStorage.length; i++) {
            const key = window.localStorage.key(i);
            if (!key) continue;
            if (key.indexOf(catalogPrefix) === 0) {
                keysToRemove.push(key);
            }
        }
        keysToRemove.forEach(function(key) {
            window.localStorage.removeItem(key);
        });

        window.posMenuClientCatalog = null;
        window.posMenuClientCatalogLoading = false;

        // Vue POS cache keys (used by PosApp.vue build).
        [
            'pos_menus',
            'pos_categories',
            'pos_menu_items',
            'pos_waiters',
            'pos_cache_timestamp',
            'pos_menus_timestamp',
            'pos_categories_timestamp',
            'pos_menu_items_timestamp',
            'pos_waiters_timestamp',
        ].forEach(function(key) {
            window.localStorage.removeItem(key);
        });

        window.localStorage.setItem(markerKey, currentRevision);
    } catch (e) {
        // ignore localStorage failures
    }
};
window.syncPosTaxRevisionCache();

window.__posModifiersModalI18n = {
    optionName: @json(__('modules.modifier.optionName')),
    setPrice: @json(__('modules.menu.setPrice')),
    select: @json(__('app.select')),
    save: @json(__('app.save')),
    cancel: @json(__('app.cancel')),
    notAvailable: @json(__('modules.menu.notAvailable')),
    requiredGroupTpl: @json(__('validation.requiredModifierGroup', ['name' => ':name']))
};

window.posOrderTypePriceMaps = @json($posOrderTypePriceMaps ?? (object) []);
window.posExtraChargesBySlug = @json($posExtraChargesBySlug ?? (object) []);
window.posDeliveryDefaultFee = {{ json_encode((float) ($posDeliveryDefaultFee ?? 0)) }};
window.posOrderTypesForModal = @json($posOrderTypesForModal ?? []);
window.posDeliveryPlatformsForModal = @json($posDeliveryPlatformsForModal ?? []);
window.posOrderTypeSelectionPolicy = @json($posOrderTypeSelectionPolicy ?? ['mode' => 'choose', 'shouldPromptModalOnLoad' => true, 'allowOrderTypeChange' => true]);
window.allowOrderTypeChange = @json($allowOrderTypeChange ?? true);
window.posOrderTypeDefaultSaveUrl = @json(route('ajax.pos.order-type-default'));

// Fresh-order tax reliability: keep a backend-derived tax index by menu item id.
// This avoids item-wise tax loss when DOM data attributes are malformed/partial.
@php
    $menuItemTaxesIndexData = collect($menuItems ?? [])->mapWithKeys(function ($menuItem) {
        $itemTaxes = collect($menuItem->taxes ?? [])
            ->map(function ($tax) {
                return [
                    'id' => $tax->id ?? null,
                    'tax_name' => $tax->tax_name ?? '',
                    'tax_percent' => (float)($tax->tax_percent ?? 0),
                ];
            })
            ->filter(function ($tax) {
                return !empty($tax['tax_name']);
            })
            ->values()
            ->toArray();
        return [$menuItem->id => $itemTaxes];
    })->toArray();
@endphp
window.menuItemTaxesIndex = Object.assign(
    {},
    window.menuItemTaxesIndex || {},
    @json($menuItemTaxesIndexData)
);

@include('pos.partials.pos-sidebar-order-type-sync')

// Drop portaled tooltips on <body> when the order panel is replaced (switch order / refresh).
window.cleanupPosPortaledTooltips = function() {
    document.querySelectorAll(
        'body > [role="tooltip"][id^="tooltip-order-detail-status-"], body > [role="tooltip"][data-pos-tt-in-body="1"]'
    ).forEach((el) => el.remove());
};

// Order status step tooltips (delegated — survives DOM updates and wider hover targets).
window.initOrderStatusStepTooltips = function() {
    try {
        const container = document.getElementById('order-status-steps');
        if (!container || container.dataset.statusTtDelegated === '1') return;

        const getTooltip = (trigger) => {
            const id = trigger.getAttribute('data-pos-status-tooltip');
            return id ? document.getElementById(id) : null;
        };

        const show = (trigger) => {
            const tooltip = getTooltip(trigger);
            if (!tooltip) return;
            if (!tooltip.dataset.posTtInBody) {
                document.body.appendChild(tooltip);
                tooltip.dataset.posTtInBody = '1';
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

        const hide = (trigger) => {
            const tooltip = getTooltip(trigger);
            if (!tooltip) return;
            tooltip.classList.add('hidden', 'invisible', 'opacity-0');
            tooltip.classList.remove('opacity-100');
            tooltip.style.display = '';
        };

        const shouldKeepOpen = (trigger, related) => {
            if (!related) return false;
            const tooltip = getTooltip(trigger);
            return trigger.contains(related) || (tooltip && tooltip.contains(related));
        };

        container.addEventListener('mouseover', (e) => {
            const trigger = e.target.closest('[data-pos-status-tooltip^="tooltip-order-detail-status-"]');
            if (!trigger || !container.contains(trigger)) return;
            show(trigger);
        });

        container.addEventListener('mouseout', (e) => {
            const trigger = e.target.closest('[data-pos-status-tooltip^="tooltip-order-detail-status-"]');
            if (!trigger || !container.contains(trigger)) return;
            if (shouldKeepOpen(trigger, e.relatedTarget)) return;
            hide(trigger);
        });

        container.addEventListener('focusin', (e) => {
            const trigger = e.target.closest('[data-pos-status-tooltip^="tooltip-order-detail-status-"]');
            if (trigger && container.contains(trigger)) show(trigger);
        });

        container.addEventListener('focusout', (e) => {
            const trigger = e.target.closest('[data-pos-status-tooltip^="tooltip-order-detail-status-"]');
            if (!trigger || !container.contains(trigger)) return;
            if (shouldKeepOpen(trigger, e.relatedTarget)) return;
            hide(trigger);
        });

        container.dataset.statusTtDelegated = '1';
    } catch (e) {
        console.warn('Order status step tooltip init failed:', e);
    }
};

// Lightweight tooltip initializer for POS icon buttons.
// Works without depending on Flowbite initialization on this page.
window.initPosIconTooltips = function(root = document) {
    try {
        const triggers = root.querySelectorAll('[data-tooltip-target]');
        triggers.forEach((trigger) => {
            if (trigger.closest('#order-status-steps')) return;

            const tooltipId = trigger.getAttribute('data-tooltip-target');
            if (!tooltipId) return;
            const tooltip = document.getElementById(tooltipId);
            if (!tooltip) return;
            if (trigger.dataset.posTooltipBound === '1') return;

            const show = () => {
                if (!tooltip.dataset.posTtInBody) {
                    document.body.appendChild(tooltip);
                    tooltip.dataset.posTtInBody = '1';
                }
                const rect = trigger.getBoundingClientRect();
                tooltip.style.position = 'fixed';
                tooltip.style.zIndex = '2147483646';
                tooltip.style.display = 'block';
                tooltip.style.width = 'max-content';
                tooltip.style.maxWidth = '14rem';
                tooltip.style.whiteSpace = 'nowrap';
                tooltip.style.top = `${Math.max(8, rect.top - 8)}px`;
                tooltip.style.left = `${Math.max(8, rect.left + (rect.width / 2))}px`;
                tooltip.style.transform = 'translate(-50%, -100%)';
                tooltip.classList.remove('hidden', 'invisible', 'opacity-0');
                tooltip.classList.add('opacity-100');
            };

            const hide = () => {
                tooltip.classList.add('hidden', 'invisible', 'opacity-0');
                tooltip.classList.remove('opacity-100');
                tooltip.style.display = '';
            };

            trigger.addEventListener('mouseenter', show);
            trigger.addEventListener('mouseleave', hide);
            trigger.addEventListener('focus', show);
            trigger.addEventListener('blur', hide);
            trigger.dataset.posTooltipBound = '1';
        });
    } catch (e) {
        console.warn('Tooltip init failed:', e);
    }
};

// Wait for jQuery to be available
(function() {
    let jqueryWaitAttempts = 0;
    const MAX_JQUERY_WAIT_ATTEMPTS = 150; // ~15s at 100ms, then stop (avoids endless timers)

    function initPosScripts() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            jqueryWaitAttempts++;
            if (jqueryWaitAttempts >= MAX_JQUERY_WAIT_ATTEMPTS) {
                console.error('POS: jQuery did not load in time; initialization aborted.');
                return;
            }
            setTimeout(initPosScripts, 100);
            return;
        }

        // Initialize POS
        $(document).ready(function() {
            if (typeof window.initPosIconTooltips === 'function') {
                window.initPosIconTooltips(document);
            }
            if (typeof window.initOrderStatusStepTooltips === 'function') {
                window.initOrderStatusStepTooltips();
            }

            const isOrderDetailView = !!(
                window.posState && (
                    window.posState.showOrderDetail === true
                    || window.posState.showOrderDetail === 'true'
                )
            );
            const hasExistingOrderContext = !!(
                window.posState && (
                    window.posState.orderID
                    || (window.posState.orderDetail && window.posState.orderDetail.id)
                )
            );
            if (typeof window.posInitOrderTypeOnLoad === 'function') {
                window.posInitOrderTypeOnLoad({
                    isOrderDetailView: isOrderDetailView,
                    hasExistingOrderContext: hasExistingOrderContext,
                });
            }
            if (typeof window.syncPosSidebarOrderTypeSections === 'function') {
                window.syncPosSidebarOrderTypeSections();
            }
            if (typeof window.syncPosRoomServiceBillToSelect === 'function') {
                window.syncPosRoomServiceBillToSelect();
            }
            if (typeof window.prefetchPosHotelRoomPickerIfRoomService === 'function') {
                window.prefetchPosHotelRoomPickerIfRoomService();
            }
            if (typeof window.ensurePosNumberInputNoSpinnerCss === 'function') {
                window.ensurePosNumberInputNoSpinnerCss();
            }
            if (typeof window.initPosAssigneeSearchableSelects === 'function') {
                window.initPosAssigneeSearchableSelects();
            }

            // Initialize totals display if there are items in cart.
            // Merged-table flow may have no orderDetail yet, so allow recalculation in that case.
            const hasCartItems = window.posState.orderItemList && Object.keys(window.posState.orderItemList).length > 0;
            const isLockedFinalStatus = window.posState.orderDetail && ['billed', 'paid', 'payment_due'].includes(window.posState.orderDetail.status);
            const showOrderDetail = window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true');
            const isFreshKotEntry = !showOrderDetail
                && window.posState
                && window.posState.orderDetail
                && window.posState.orderDetail.status === 'kot';

            // Fresh KOT entry (/pos/kot/{id} without order-detail panel) should start from empty cart totals.
            // Prevent stale order-level totals/taxes from being shown when item list is empty.
            if (!hasCartItems && isFreshKotEntry) {
                window.posState.subTotal = 0;
                window.posState.total = 0;
                window.posState.totalTaxAmount = 0;
                window.posState.taxBase = 0;
                window.posState.discountedTotal = 0;
                window.posState.discountAmount = 0;
                window.posState.orderItemTaxDetails = {};
                window.posState.totalsPreCalculated = false;
                window.posState.hasFreeStampItems = false;
                window.posState.loyaltyPointsRedeemed = 0;
                window.posState.loyaltyDiscountAmount = 0;
                window.posState.stampDiscountAmount = 0;
                window.posState.availableLoyaltyPoints = 0;

                // Ensure any loyalty redemption UI (inputs/previews) is also cleared
                if (typeof window.resetLoyaltyRedemption === 'function') {
                    window.resetLoyaltyRedemption();
                }

                if (typeof window.updateTotalsDisplay === 'function') {
                    window.updateTotalsDisplay();
                }
            }

            if (hasCartItems && !isLockedFinalStatus) {

                if (typeof window.updateOrderItemsContainer === 'function') {
                window.updateOrderItemsContainer();
                }

                // Always recalculate totals on load so that:
                // - merged tables get fresh tax/charge computation
                // - any client-side stamp/loyalty changes are reflected
                // The backend still trusts incoming numbers, but the UI
                // should never show a zero-tax state after merges.
                if (typeof window.calculateTotal === 'function') {
                    if (window.posState.totalsPreCalculated) {
                        if (typeof window.updateTotalsDisplay === 'function') {
                            window.updateTotalsDisplay();
                            window.calculateTotal();
                        }
                    } else {
                        window.calculateTotal();
                    }
                }
            }

            // Play beep sound function
            window.playBeep = function() {
                new Audio("{{ asset('sound/sound_beep-29.mp3') }}").play();
            };

            // Keep cart list pinned to latest added item.
            window.scrollPosCartToLatest = function() {
                if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                    return;
                }
                setTimeout(function() {
                    $('#order-items-container .pos-hover-scrollbar').each(function() {
                        this.scrollTop = this.scrollHeight;
                    });
                }, 30);
            };

            // Print location function
            window.printLocation = function(url) {
                if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                    window.open(url, '_blank');
                    return;
                }

                const isPWA = (window.matchMedia('(display-mode: standalone)').matches) ||
                             (window.navigator.standalone === true) ||
                             (document.referrer.includes('android-app://'));

                if (isPWA) {
                    window.location.href = url;
                } else {
                const $anchor = $('<a>', { href: url, target: '_blank' });
                    $('body').append($anchor);
                    $anchor[0].click();
                    $anchor.remove();
                }
            };

            // Close modals when clicking on backdrop (outside the modal content)
            function bindBackdropClose(modalSelector, closeFn) {
                const $modal = $(modalSelector);
                if (!$modal.length) return;
                $modal.off('click.posBackdrop').on('click.posBackdrop', function(e) {
                    // Only close when the click is on the backdrop itself, not inside modal content
                    if (e.target === this) {
                        if (typeof closeFn === 'function') {
                            closeFn();
                        } else {
                            $modal.hide();
                        }
                    }
                });
            }

            bindBackdropClose('#kotNoteModal', window.closeKotNoteModal);
            bindBackdropClose('#itemNoteModal', window.closeItemNoteModal);
            bindBackdropClose('#discountModal', window.closeDiscountModal);
            bindBackdropClose('#variationModal', window.closeVariationModal);
            bindBackdropClose('#tableChangeModal', window.closeTableChangeModal);
            bindBackdropClose('#loyaltyRedemptionModal', window.closeLoyaltyRedemptionModal);
            bindBackdropClose('#pos-order-type-modal', function() {
                if (window.posState && window.posState.orderTypeId) {
                    window.hidePosOrderTypeModal();
                }
            });
        });

        window.toggleSingleActionButton = function(button, isLoading) {
            if (!button) return;
            const textEl = button.querySelector('[data-btn-text]');
            const loadingEl = button.querySelector('[data-btn-loading]');
            button.disabled = !!isLoading;
            button.classList.toggle('opacity-50', !!isLoading);
            if (textEl) textEl.classList.toggle('hidden', !!isLoading);
            if (loadingEl) loadingEl.classList.toggle('hidden', !isLoading);
        };
        window.setGlobalOrderActionLock = function(isLocked) {
            if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
            $('.pos-order-action-btn')
                .prop('disabled', !!isLocked)
                .toggleClass('opacity-50 pointer-events-none', !!isLocked);
            $('.pos-new-kot-link').each(function() {
                var $link = $(this);
                $link.toggleClass('opacity-50', !!isLocked);
                if (isLocked) {
                    $link.addClass('pointer-events-none').attr('aria-disabled', 'true');
                } else {
                    $link.removeClass('pointer-events-none').removeAttr('aria-disabled');
                }
            });
        };

        window.__posOrderActionInProgress = false;
        window.setGlobalOrderActionLock(false);

        // Helper function to wait for Livewire and dispatch payment modal
        window.showPaymentModalForOrder = function(orderId, triggerButton = null) {
            if (!orderId) {
                console.error('Order ID is required to show payment modal');
                return;
            }
            window.setGlobalOrderActionLock(true);
            window.toggleSingleActionButton(triggerButton, true);

            if (window.PosOffline && typeof window.PosOffline.shouldQueueNow === 'function' && window.PosOffline.shouldQueueNow()) {
                var due = 0;
                if (window.posState) {
                    due = parseFloat(window.posState.total);
                    if (!Number.isFinite(due) && window.posState.orderDetail) {
                        due = parseFloat(window.posState.orderDetail.total);
                    }
                }
                if (!Number.isFinite(due)) {
                    due = 0;
                }
                if (typeof window.openPosOfflinePaymentModal === 'function') {
                    window.openPosOfflinePaymentModal({
                        order_id: orderId,
                        offline_queue_group_key: null,
                        due_amount: due,
                        formatted_order_number: (window.posState && (window.posState.formattedOrderNumber || window.posState.orderNumber)) ? String(window.posState.formattedOrderNumber || window.posState.orderNumber) : ''
                    });
                }
                setTimeout(function() {
                    window.setGlobalOrderActionLock(false);
                    window.toggleSingleActionButton(triggerButton, false);
                }, 350);
                return true;
            }

            // Check if Livewire is available
            if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                try {
                    // Dispatch the event to show payment modal
                    Livewire.dispatch('showPaymentModal', { id: orderId });
                    setTimeout(function() {
                        window.setGlobalOrderActionLock(false);
                        window.toggleSingleActionButton(triggerButton, false);
                    }, 350);
                    return true;
                } catch (e) {
                    console.error('Error dispatching payment modal event:', e);
                }
            }

            // If Livewire is not available, wait and retry
            let attempts = 0;
            const maxAttempts = 10;
            const checkInterval = setInterval(function() {
                attempts++;
                if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                    clearInterval(checkInterval);
                    try {
                        Livewire.dispatch('showPaymentModal', { id: orderId });
                        console.log('Payment modal event dispatched for order:', orderId);
                        setTimeout(function() {
                            window.setGlobalOrderActionLock(false);
                            window.toggleSingleActionButton(triggerButton, false);
                        }, 350);
                    } catch (e) {
                        console.error('Error dispatching payment modal event:', e);
                        // Fallback: redirect to payment page
                        window.location.href = "{{ route('orders.show', ['order' => ':id']) }}".replace(':id', orderId) + '?payment=true';
                    }
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    window.setGlobalOrderActionLock(false);
                    window.toggleSingleActionButton(triggerButton, false);
                    // Fallback: redirect to payment page
                    window.location.href = "{{ route('orders.show', ['order' => ':id']) }}".replace(':id', orderId) + '?payment=true';
                }
            }, 200);
        };
    }

    // Start initialization using jQuery
    $(document).ready(function() {
        initPosScripts();
    });
})();

(function($) {
    window.__posOfflinePaymentCtx = null;

    function closePosOfflinePaymentModal() {
        var m = document.getElementById('pos-offline-payment-modal');
        if (m) {
            m.classList.add('hidden');
            m.setAttribute('aria-hidden', 'true');
        }
        window.__posOfflinePaymentCtx = null;
    }

    function updateOfflinePaymentChangeDisplay() {
        var ctx = window.__posOfflinePaymentCtx;
        var methodEl = document.getElementById('pos-offline-payment-method');
        var tenderWrap = document.getElementById('pos-offline-payment-tendered-wrap');
        var tenderInput = document.getElementById('pos-offline-payment-tendered');
        var changeEl = document.getElementById('pos-offline-payment-change-display');
        if (!ctx || !methodEl || !tenderWrap || !tenderInput || !changeEl) {
            return;
        }
        var due = parseFloat(ctx.due_amount) || 0;
        var method = methodEl.value || 'cash';
        if (method === 'cash') {
            tenderWrap.classList.remove('hidden');
            var t = parseFloat(tenderInput.value);
            if (!Number.isFinite(t)) {
                t = 0;
            }
            var ch = Math.max(0, Math.round((t - due) * 100) / 100);
            changeEl.textContent = typeof window.formatCurrency === 'function'
                ? window.formatCurrency(ch)
                : ch.toFixed(2);
        } else {
            tenderWrap.classList.add('hidden');
            changeEl.textContent = '—';
        }
    }

    window.openPosOfflinePaymentModal = function(opts) {
        opts = opts || {};
        var due = parseFloat(opts.due_amount);
        if (!Number.isFinite(due)) {
            due = 0;
        }
        var rawOid = opts.order_id;
        var oid = rawOid != null && rawOid !== '' ? parseInt(String(rawOid), 10) : null;
        if (!Number.isFinite(oid) || oid <= 0) {
            oid = null;
        }
        window.__posOfflinePaymentCtx = {
            order_id: oid,
            offline_queue_group_key: opts.offline_queue_group_key || null,
            due_amount: due,
            formatted_order_number: opts.formatted_order_number ? String(opts.formatted_order_number) : ''
        };
        var m = document.getElementById('pos-offline-payment-modal');
        var labelEl = document.getElementById('pos-offline-payment-order-label');
        var dueEl = document.getElementById('pos-offline-payment-due-display');
        var tenderInput = document.getElementById('pos-offline-payment-tendered');
        var methodEl = document.getElementById('pos-offline-payment-method');
        var errEl = document.getElementById('pos-offline-payment-error');
        if (!m || !labelEl || !dueEl || !tenderInput || !methodEl) {
            return;
        }
        if (errEl) {
            errEl.classList.add('hidden');
            errEl.textContent = '';
        }
        labelEl.textContent = window.__posOfflinePaymentCtx.formatted_order_number || '';
        dueEl.textContent = typeof window.formatCurrency === 'function'
            ? window.formatCurrency(due)
            : due.toFixed(2);
        methodEl.value = 'cash';
        tenderInput.value = String(Math.max(0, due).toFixed(2));
        m.classList.remove('hidden');
        m.setAttribute('aria-hidden', 'false');
        updateOfflinePaymentChangeDisplay();
    };

    $(document).on('change', '#pos-offline-payment-method', updateOfflinePaymentChangeDisplay);
    $(document).on('input', '#pos-offline-payment-tendered', updateOfflinePaymentChangeDisplay);
    $(document).on('click', '#pos-offline-payment-close, #pos-offline-payment-cancel', function(e) {
        e.preventDefault();
        closePosOfflinePaymentModal();
    });
    $(document).on('click', '#pos-offline-payment-modal', function(e) {
        if (e.target.id === 'pos-offline-payment-modal') {
            closePosOfflinePaymentModal();
        }
    });
    $(document).on('click', '#pos-offline-payment-confirm', function(e) {
        e.preventDefault();
        var ctx = window.__posOfflinePaymentCtx;
        var errEl = document.getElementById('pos-offline-payment-error');
        if (!ctx || !window.PosOffline || typeof window.PosOffline.queueRecordPayment !== 'function') {
            closePosOfflinePaymentModal();
            return;
        }
        var methodEl = document.getElementById('pos-offline-payment-method');
        var tenderInput = document.getElementById('pos-offline-payment-tendered');
        var method = methodEl ? methodEl.value : 'cash';
        var due = parseFloat(ctx.due_amount) || 0;
        var tendered = 0;
        if (method === 'cash') {
            tendered = parseFloat(tenderInput ? tenderInput.value : '0');
            if (!Number.isFinite(tendered) || tendered < 0) {
                tendered = 0;
            }
        } else if (method === 'due') {
            tendered = 0;
        } else {
            tendered = due;
        }
        var change = Math.max(0, Math.round((tendered - due) * 100) / 100);
        if (method === 'cash' && tendered + 1e-9 < due) {
            if (errEl) {
                errEl.textContent = @json(__('messages.posOfflineCashMustCoverDue'));
                errEl.classList.remove('hidden');
            }
            return;
        }
        var payload = {
            order_id: ctx.order_id || null,
            offline_queue_group_key: ctx.offline_queue_group_key || null,
            payment_method: method,
            payment_amount: tendered,
            return_amount: change
        };
        var summary = {
            payment_method: method,
            tendered: tendered,
            change: change,
            due_amount: due,
            order_number_label: ctx.formatted_order_number || null
        };
        window.PosOffline.queueRecordPayment(payload, summary);
        closePosOfflinePaymentModal();
        var msg = @json(__('messages.posOfflinePaymentQueued'));
        if (typeof window.__posShowOfflineQueuedBanner === 'function') {
            window.__posShowOfflineQueuedBanner(msg);
        } else if (typeof window.showToast === 'function') {
            window.showToast('success', msg);
        }
    });
})(jQuery);

window.__posOrderTypeModalPending = { orderTypeId: null, slug: null };

window.posNormalizedDeliveryAppId = function(v) {
    if (v === undefined || v === null || v === '' || v === 'default') {
        return null;
    }
    const n = parseInt(String(v), 10);
    return Number.isNaN(n) ? null : n;
};

window.posOrderTypePriceLookupKey = function(orderTypeId, deliveryAppIdNormalized) {
    return String(orderTypeId) + '__' + (deliveryAppIdNormalized === null || deliveryAppIdNormalized === undefined ? 'none' : String(deliveryAppIdNormalized));
};

window.applyPosMenuPricesForCurrentOrderContext = function() {
    if (!window.__posMenuClientSideCatalog || !Array.isArray(window.posMenuClientCatalog)) {
        return;
    }
    const oid = window.posState && window.posState.orderTypeId;
    if (!oid) {
        return;
    }
    const app = window.posNormalizedDeliveryAppId(window.posState && window.posState.selectedDeliveryApp);
    const key = window.posOrderTypePriceLookupKey(oid, app);
    const map = window.posOrderTypePriceMaps && window.posOrderTypePriceMaps[key];
    if (!map) {
        return;
    }
    window.posMenuClientCatalog.forEach(function(item) {
        const pid = map[String(item.id)];
        if (pid !== undefined && pid !== null) {
            item.price = parseFloat(pid);
        }
    });
    if (typeof window.applyClientMenuFilter === 'function') {
        window.applyClientMenuFilter();
    }
    if (typeof window.updateCategoryCounts === 'function') {
        window.updateCategoryCounts();
    }
};

window.updatePosOrderTypeUiLabels = function() {
    const oid = window.posState && window.posState.orderTypeId;
    const meta = (window.posOrderTypesForModal || []).find(function(t) {
        return String(t.id) === String(oid);
    });
    const ind = document.getElementById('pos-order-type-indicator');
    if (ind) {
        if (oid) {
            ind.classList.remove('hidden');
        } else {
            ind.classList.add('hidden');
        }
    }
    const nameEl = document.getElementById('pos-order-type-display-name');
    if (nameEl) {
        if (meta) {
            nameEl.textContent = meta.order_type_name || meta.slug || '';
        } else if (window.posState && window.posState.orderType) {
            nameEl.textContent = String(window.posState.orderType);
        } else {
            nameEl.textContent = '';
        }
    }
    const platRow = document.getElementById('pos-delivery-platform-row');
    const platEl = document.getElementById('pos-delivery-platform-display-name');
    const slug = window.posState && window.posState.orderTypeSlug;
    const app = window.posState && window.posState.selectedDeliveryApp;
    if (platRow && platEl) {
        if (slug === 'delivery') {
            platRow.classList.remove('hidden');
            if (app === null || app === undefined || app === '' || app === 'default') {
                platEl.textContent = @json(__('modules.order.defaultDeliveryPlatform'));
            } else {
                const pmeta = (window.posDeliveryPlatformsForModal || []).find(function(p) {
                    return String(p.id) === String(app);
                });
                platEl.textContent = pmeta && pmeta.name ? pmeta.name : '';
            }
        } else {
            platRow.classList.add('hidden');
        }
    }
    if (typeof window.syncPosSidebarOrderTypeSections === 'function') {
        window.syncPosSidebarOrderTypeSections();
    }
    if (typeof window.syncPosRoomServiceBillToSelect === 'function') {
        window.syncPosRoomServiceBillToSelect();
    }
    if (typeof window.syncSelectedHotelStaySummaryFromState === 'function') {
        window.syncSelectedHotelStaySummaryFromState();
    }
    if (typeof window.prefetchPosHotelRoomPickerIfRoomService === 'function') {
        window.prefetchPosHotelRoomPickerIfRoomService();
    }
};

window.applyPosOrderTypeSelection = function(orderTypeId, slug, deliveryPlatform) {
    const appNorm = window.posNormalizedDeliveryAppId(deliveryPlatform === 'default' ? null : deliveryPlatform);
    window.posState.orderTypeId = orderTypeId;
    window.posState.orderTypeSlug = slug;
    const meta = (window.posOrderTypesForModal || []).find(function(t) {
        return String(t.id) === String(orderTypeId);
    });
    window.posState.orderType = (meta && meta.type) ? meta.type : slug;
    if (deliveryPlatform === undefined || deliveryPlatform === null || deliveryPlatform === 'default') {
        window.posState.selectedDeliveryApp = null;
    } else {
        window.posState.selectedDeliveryApp = deliveryPlatform;
    }

    const rawCharges = window.posExtraChargesBySlug && window.posExtraChargesBySlug[slug];
    window.posConfig.extraCharges = rawCharges ? JSON.parse(JSON.stringify(rawCharges)) : [];

    if (slug === 'delivery') {
        window.posState.deliveryFee = parseFloat(window.posDeliveryDefaultFee || 0) || 0;
    } else {
        window.posState.deliveryFee = 0;
    }

    window.applyPosMenuPricesForCurrentOrderContext();
    window.updatePosOrderTypeUiLabels();

    // Keep the address bar on /pos (or current path) without order-type query params — selection lives in posState only.
    const url = new URL(window.location.href);
    ['orderType', 'orderTypeId', 'deliveryPlatform', 'changeOrderType', 'allowOrderTypeSelection'].forEach(function(key) {
        url.searchParams.delete(key);
    });
    const qs = url.searchParams.toString();
    const nextPath = qs ? (url.pathname + '?' + qs) : url.pathname;
    window.history.replaceState({}, '', nextPath);

    window.hidePosOrderTypeModal();
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }

    // Base item prices for every order type (and each delivery platform) are prefetched in
    // window.posOrderTypePriceMaps; applyPosMenuPricesForCurrentOrderContext() already ran above.
    // Re-render filters/counts locally — do not refetch the whole catalog on order-type change.
    if (window.__posMenuClientSideCatalog) {
        if (typeof window.applyClientMenuFilter === 'function') {
            window.applyClientMenuFilter();
        }
        if (typeof window.updateCategoryCounts === 'function') {
            window.updateCategoryCounts();
        }
    }
};

window.posOrderTypeModalPickDelivery = function(orderTypeId, platform) {
    window.posFinalizeOrderTypeSelection(orderTypeId, 'delivery', platform);
};

window.posOrderTypeModalPickType = function(orderTypeId, slug) {
    window.__posOrderTypeModalPending = { orderTypeId: orderTypeId, slug: slug };
    if (slug === 'delivery') {
        window.posOrderTypeModalPickDelivery(orderTypeId, 'default');
        return;
    }
    window.posFinalizeOrderTypeSelection(orderTypeId, slug, null);
};

window.posOrderTypeModalPickPlatform = function(platform) {
    const p = window.__posOrderTypeModalPending;
    if (!p || !p.orderTypeId || !p.slug) {
        return;
    }
    window.posFinalizeOrderTypeSelection(p.orderTypeId, p.slug, platform);
};

window.posOrderTypeModalResetView = function() {
    if (typeof jQuery !== 'undefined') {
        $('#pos-otm-stage-types').removeClass('hidden');
        $('#pos-otm-stage-platforms').addClass('hidden');
        $('#pos-otm-back-btn').addClass('hidden');
        $('#pos-otm-order-types-section').removeClass('hidden');
        $('#pos-otm-delivery-section').removeClass('hidden');
        $('#pos-otm-title').text(@json(__('modules.order.selectOrderType')));
        const hasDeliveryTiles = $('#pos-otm-delivery-section').length > 0;
        $('#pos-otm-description').text(hasDeliveryTiles
            ? @json(__('modules.order.selectOrderTypeWithDeliveryDescription'))
            : @json(__('modules.order.selectOrderTypeDescription')));
    }
    window.__posOrderTypeModalPending = { orderTypeId: null, slug: null };
};

window.posOrderTypeModalGoBack = function() {
    window.posOrderTypeModalResetView();
};

window.showPosOrderTypeModal = function() {
    window.posOrderTypeModalResetView();
    if (typeof jQuery !== 'undefined') {
        $('#pos-set-order-type-default').prop('checked', false);
        $('#pos-order-type-modal').css('display', 'flex');
    }
};

window.showPosOrderTypeModalDeliveryPlatformsOnly = function() {
    window.posOrderTypeModalResetView();
    if (typeof jQuery !== 'undefined') {
        $('#pos-otm-order-types-section').addClass('hidden');
        $('#pos-otm-delivery-section').removeClass('hidden');
        $('#pos-otm-stage-types').removeClass('hidden');
        $('#pos-otm-stage-platforms').addClass('hidden');
        $('#pos-otm-back-btn').addClass('hidden');
        $('#pos-otm-title').text(@json(__('modules.order.selectDeliveryPlatform')));
        $('#pos-otm-description').text(@json(__('modules.order.selectDeliveryPlatformDescription')));
        $('#pos-set-order-type-default').prop('checked', false);
        $('#pos-order-type-modal').css('display', 'flex');
    }
};

window.posInitOrderTypeOnLoad = function(ctx) {
    ctx = ctx || {};
    const policy = window.posOrderTypeSelectionPolicy || { mode: 'choose', shouldPromptModalOnLoad: true };
    if (ctx.isOrderDetailView || ctx.hasExistingOrderContext) {
        return;
    }

    const platforms = window.posDeliveryPlatformsForModal || [];
    const hasPlatform = window.posState && window.posState.selectedDeliveryApp !== null
        && window.posState.selectedDeliveryApp !== undefined
        && window.posState.selectedDeliveryApp !== '';

    if (policy.mode === 'locked_single' && policy.autoOrderTypeId) {
        if (!window.posState.orderTypeId && typeof window.posFinalizeOrderTypeSelection === 'function') {
            window.posFinalizeOrderTypeSelection(policy.autoOrderTypeId, policy.autoSlug, null);
        }
        return;
    }

    if (policy.mode === 'delivery_only' && policy.autoOrderTypeId) {
        if (!hasPlatform) {
            if (platforms.length === 0) {
                if (typeof window.posFinalizeOrderTypeSelection === 'function') {
                    window.posFinalizeOrderTypeSelection(policy.autoOrderTypeId, 'delivery', 'default');
                }
            } else if (platforms.length === 1) {
                if (typeof window.posFinalizeOrderTypeSelection === 'function') {
                    window.posFinalizeOrderTypeSelection(policy.autoOrderTypeId, 'delivery', platforms[0].id);
                }
            } else if (typeof window.showPosOrderTypeModalDeliveryPlatformsOnly === 'function') {
                window.showPosOrderTypeModalDeliveryPlatformsOnly();
            }
        }
        return;
    }

    const shouldPromptOrderType = !window.posState.orderTypeId && policy.shouldPromptModalOnLoad !== false;
    if (shouldPromptOrderType && typeof window.showPosOrderTypeModal === 'function') {
        window.showPosOrderTypeModal();
    }
};

window.hidePosOrderTypeModal = function() {
    if (typeof jQuery !== 'undefined') {
        $('#pos-order-type-modal').hide();
    }
    window.posOrderTypeModalGoBack();
};

window.posFinalizeOrderTypeSelection = function(orderTypeId, slug, deliveryPlatform) {
    window.applyPosOrderTypeSelection(orderTypeId, slug, deliveryPlatform);
    window.repricePosCartForOrderTypeChange();
    if (typeof jQuery !== 'undefined' && $('#pos-set-order-type-default').is(':checked')) {
        $.easyAjax({
            url: window.posOrderTypeDefaultSaveUrl,
            type: 'POST',
            data: {
                order_type_id: orderTypeId,
                _token: '{{ csrf_token() }}'
            },
            success: function() {},
            error: function() {}
        });
    }
    if (typeof jQuery !== 'undefined') {
        $('#pos-set-order-type-default').prop('checked', false);
    }
};

window.clearPosCartForOrderTypeChange = function() {
    window.posState.orderItemList = {};
    window.posState.orderItemQty = {};
    window.posState.orderItemAmount = {};
    window.posState.orderItemVariation = {};
    window.posState.itemModifiersSelected = {};
    window.posState.orderItemModifiersPrice = {};
    window.posState.itemNotes = {};
    window.posState.orderItemTaxDetails = {};
    window.posState.subTotal = 0;
    window.posState.total = 0;
    window.posState.discountAmount = 0;
    window.posState.discountType = null;
    window.posState.discountValue = null;
    window.posState.discountApplyOn = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }})
        ? 'total'
        : 'sub_total';
    window.posState.discountedTotal = 0;
    window.posState.totalTaxAmount = 0;
    window.posState.taxBase = 0;
    window.posState.tipAmount = 0;
    window.posState.totalsPreCalculated = false;
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }

    if (typeof window.flushCustomerDisplayUpdate === 'function') {
        window.flushCustomerDisplayUpdate();
    }
};

window.repricePosCartForOrderTypeChange = function() {
    const oid = window.posState && window.posState.orderTypeId;
    if (!oid || !window.posState || !window.posState.orderItemList) {
        return;
    }

    const app = window.posNormalizedDeliveryAppId(window.posState && window.posState.selectedDeliveryApp);
    const lookupKey = window.posOrderTypePriceLookupKey(oid, app);
    const map = window.posOrderTypePriceMaps && window.posOrderTypePriceMaps[lookupKey];
    if (!map) {
        return;
    }

    Object.keys(window.posState.orderItemList).forEach(function(itemKey) {
        if (/^kot_\d+_\d+$/.test(itemKey)) {
            return;
        }
        const item = window.posState.orderItemList[itemKey];
        if (!item || !item.id) {
            return;
        }

        const hasVariation = !!(window.posState.orderItemVariation && window.posState.orderItemVariation[itemKey]);
        const mapped = parseFloat(map[String(item.id)]);
        const isStampLine = typeof window.isFreeStampItemByMeta === 'function'
            ? window.isFreeStampItemByMeta(itemKey, item, window.posState.itemNotes && window.posState.itemNotes[itemKey])
            : false;

        if (!hasVariation && Number.isFinite(mapped)) {
            item.price = mapped;
        }

        if (isStampLine) {
            return;
        }

        const qty = parseFloat((window.posState.orderItemQty && window.posState.orderItemQty[itemKey]) || 1) || 1;
        const variation = window.posState.orderItemVariation && window.posState.orderItemVariation[itemKey];
        const basePrice = variation ? parseFloat(variation.price || item.price || 0) : parseFloat(item.price || 0);
        const modifierPrice = parseFloat((window.posState.orderItemModifiersPrice && window.posState.orderItemModifiersPrice[itemKey]) || 0) || 0;
        window.posState.orderItemAmount[itemKey] = qty * (basePrice + modifierPrice);
    });

    window.posState.totalsPreCalculated = false;
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
};

window.updateOrderTypeDisplay = function() {
    window.updatePosOrderTypeUiLabels();
};

window.changeOrderType = function() {
    if (window.allowOrderTypeChange === false) {
        return;
    }
    const policy = window.posOrderTypeSelectionPolicy || {};
    if (policy.mode === 'delivery_only' && typeof window.showPosOrderTypeModalDeliveryPlatformsOnly === 'function') {
        window.showPosOrderTypeModalDeliveryPlatformsOnly();
        return;
    }
    if (typeof window.showPosOrderTypeModal === 'function') {
        window.showPosOrderTypeModal();
    }
};

function resetOrderTypeSelection() {
    window.showPosOrderTypeModal();
}

// Backwards-compatible name used by menu / other scripts
window.redirectToPOS = function(orderTypeId, slug, deliveryPlatform) {
    window.posFinalizeOrderTypeSelection(orderTypeId, slug, deliveryPlatform);
};

// Modal Functions
window.closeVariationModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#variationModal').hide();
    }
};

window.closeKotNoteModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#kotNoteModal').hide();
    }
};

window.closeItemNoteModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#itemNoteModal').hide();
        $('#itemNoteError').hide();
        // Do not clear pending key here; user might reopen quickly.
    }
};

window.closeTableModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#tableModal').hide();
    }
};

window.closeDiscountModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#discountModal').hide();
    }
};

// Loyalty API: routes and token in one place (flat structure, no nested functions)
window.posLoyaltyApi = {
    summaryUrl: "{{ route('ajax.pos.loyalty.summary') }}",
    redeemUrl: "{{ route('ajax.pos.loyalty.redeem') }}",
    stampPreviewUrl: "{{ route('ajax.pos.loyalty.stamp-auto-preview') }}",
    token: '{{ csrf_token() }}',
    _defaultErrorMsg: @json(__('messages.somethingWentWrong')),
    getSummary: function(successCb, errorCb) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
        var api = window.posLoyaltyApi;
        $.easyAjax({
            url: api.summaryUrl,
            type: 'GET',
            data: {
                customer_id: window.posState?.customerId,
                sub_total: window.posState?.subTotal || 0,
                _token: api.token
            },
            success: successCb || function() {},
            error: errorCb || function(xhr) {
                var msg = xhr.responseJSON?.message || api._defaultErrorMsg;
                window.showToast?.('error', msg);
            }
        });
    },
    redeem: function(points, successCb, errorCb) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
        var api = window.posLoyaltyApi;
        $.easyAjax({
            url: api.redeemUrl,
            type: 'POST',
            data: {
                customer_id: window.posState?.customerId,
                sub_total: window.posState?.subTotal || 0,
                points: points,
                _token: api.token
            },
            success: successCb || function() {},
            error: errorCb || function() {}
        });
    },
    stampPreview: function(payload, successCb, errorCb) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
        var api = window.posLoyaltyApi;
        var data = Object.assign({ _token: api.token }, payload);
        $.ajax({
            url: api.stampPreviewUrl,
            type: 'POST',
            data: data,
            success: successCb || function() {},
            error: errorCb || function() {}
        });
    }
};

// Re-cap loyalty redemption when subtotal changes (e.g. stamp free item lowers subtotal).
window.clampLoyaltyRedemptionToSubtotal = function(subTotal) {
    var s = window.posState;
    if (!s || !(parseInt(s.loyaltyPointsRedeemed || 0, 10) > 0)) {
        return;
    }

    subTotal = typeof subTotal === 'number' ? subTotal : parseFloat(s.subTotal || 0);
    var valuePerPoint = parseFloat(s.valuePerPoint || window.posConfig?.loyaltyValuePerPoint || 1);
    if (!(valuePerPoint > 0)) {
        valuePerPoint = 1;
    }

    var maxPercent = parseFloat(s.maxDiscountPercent ?? window.posConfig?.loyaltyMaxDiscountPercent ?? 0);
    if (!(maxPercent > 0) || !(subTotal > 0)) {
        return;
    }

    var maxDiscount = Math.round((subTotal * maxPercent) / 100 * 100) / 100;
    s.maxLoyaltyDiscount = maxDiscount;

    var minRedeem = parseInt(s.minRedeemPoints || window.posConfig?.loyaltyMinRedeemPoints || 0, 10);
    var maxPoints = Math.floor(maxDiscount / valuePerPoint);
    if (minRedeem > 0 && maxPoints > 0) {
        maxPoints = Math.floor(maxPoints / minRedeem) * minRedeem;
    }

    var points = parseInt(s.loyaltyPointsRedeemed || 0, 10);
    if (points > maxPoints) {
        points = maxPoints;
    }

    var discount = Math.min(points * valuePerPoint, maxDiscount);
    discount = Math.round(discount * 100) / 100;

    if (points <= 0) {
        s.loyaltyPointsRedeemed = 0;
        s.loyaltyDiscountAmount = 0;
    } else {
        s.loyaltyPointsRedeemed = points;
        s.loyaltyDiscountAmount = discount;
    }

    var available = parseInt(s.availableLoyaltyPoints || 0, 10);
    s.maxRedeemablePoints = available > 0 ? Math.min(available, maxPoints) : maxPoints;
};

// Apply loyalty summary response to posState and loyalty modal (top-level, same style as rest of POS)
window.applyLoyaltySummaryToStateAndModal = function(resp) {
    if (!resp || !resp.success || resp.enabled === false) return false;
    var s = window.posState;
    if (!s) return false;
    s.availableLoyaltyPoints = resp.available_points || 0;
    s.valuePerPoint = resp.value_per_point || 0;
    s.minRedeemPoints = resp.min_redeem_points || 0;
    s.maxDiscountPercent = resp.max_discount_percent ?? s.maxDiscountPercent ?? 0;
    s.maxRedeemablePoints = resp.max_redeemable_points || 0;
    s.maxLoyaltyDiscount = resp.max_loyalty_discount || 0;
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return true;
    $('#loyalty-available-points').text((s.availableLoyaltyPoints || 0).toLocaleString());
    $('#loyalty-max-discount').text((s.maxLoyaltyDiscount || 0).toFixed(2));
    if (s.valuePerPoint > 0 && $('#loyalty-points-value').length) $('#loyalty-points-value').text(s.valuePerPoint.toFixed(2));
    var min = s.minRedeemPoints || 0, max = s.maxRedeemablePoints || 0;
    var $minMaxRow = $('#loyalty-min-max-row'), $minWrapper = $('#loyalty-min-wrapper'), $maxWrapper = $('#loyalty-max-wrapper');
    if (min > 0 || max > 0) {
        if (min > 0) { $('#loyalty-min-points').text(min.toLocaleString()); $minWrapper.show(); } else { $minWrapper.hide(); }
        if (max > 0) { $('#loyalty-max-points').text(max.toLocaleString()); $maxWrapper.show(); } else { $maxWrapper.hide(); }
        $minMaxRow.show();
    } else { $minMaxRow.hide(); }
    var $multipleRow = $('#loyalty-multiple-row');
    if (min > 0 && max > 0) {
        var template = $multipleRow.data('template') || '', text = template.replace(':min', min.toLocaleString());
        $multipleRow.text(text).show();
    } else { $multipleRow.hide().text(''); }
    $('#loyaltyPointsInput').val(s.loyaltyPointsRedeemed || '');
    $('#loyalty-discount-preview').text((s.loyaltyDiscountAmount || 0).toFixed(2));
    $('#loyaltyError').hide().text('');
    if ((s.maxRedeemablePoints || 0) > 0) {
        $('#loyalty-use-max-value').text(s.maxRedeemablePoints.toLocaleString());
        $('#loyalty-use-max-btn').show();
    } else { $('#loyalty-use-max-btn').hide(); }
    var hasPoints = (s.availableLoyaltyPoints || 0) > 0, cust = s.customer;
    if (hasPoints) {
        if (cust && cust.name) $('#loyalty-customer-name').text(cust.name);
        $('#loyalty-customer-row').show(); $('#loyalty-no-customer-row').hide();
    } else { $('#loyalty-no-customer-row').show(); $('#loyalty-customer-row').hide(); }
    $('#loyaltyRedemptionModal').show();
    return true;
};

// Loyalty Redemption (AJAX POS)
window.openLoyaltyRedemptionModal = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    if (!(window.posState?.loyaltyEnabled || window.posConfig?.loyaltyEnabled)) {
        return;
    }
    if (!window.posState.customerId) {
        window.showToast?.('error', {!! json_encode(__('modules.order.addCustomerDetails')) !!});
        return;
    }

    window.posLoyaltyApi.getSummary(function(resp) {
        window.applyLoyaltySummaryToStateAndModal(resp);
    }, function(xhr) {
        var msg = xhr.responseJSON?.message || window.posLoyaltyApi._defaultErrorMsg;
        window.showToast?.('error', msg);
    });
};

window.closeLoyaltyRedemptionModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#loyaltyRedemptionModal').hide();
    }
};

window.updateLoyaltyRedeemButtonVisibility = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    const $btn = $('#loyalty-redeem-button-container');
    if (!$btn.length) {
        return;
    }
    const loyaltyEnabled = !!(window.posState?.loyaltyEnabled || window.posConfig?.loyaltyEnabled);
    const hasCustomer = !!(window.posState?.customerId);
    const hasItems = Object.keys(window.posState?.orderItemList || {}).length > 0;
    const notRedeemed = (parseInt(window.posState?.loyaltyPointsRedeemed || 0, 10) || 0) === 0;
    const hasSubtotal = parseFloat(window.posState?.subTotal || 0) > 0;
    const canUpdate = window.posState?.canUpdateOrder !== false;
    const show = loyaltyEnabled && hasCustomer && hasItems && notRedeemed && hasSubtotal && canUpdate;
    $btn.toggle(show);
};

// Skip = close modal without applying (tt parity)
window.skipLoyaltyRedemption = function() {
    window.closeLoyaltyRedemptionModal();
};

// Use Max = apply redemption with max redeemable points (tt parity)
window.applyLoyaltyRedemptionMax = function() {
    var max = window.posState.maxRedeemablePoints || 0;
    if (max > 0) {
        if (typeof jQuery !== 'undefined' && $('#loyaltyPointsInput').length) {
            $('#loyaltyPointsInput').val(max);
        }
        window.applyLoyaltyRedemption();
    }
};

// -------------------------------------------------------------
// Hotel room-service stay selection modal for AJAX POS
// Full room list + localStorage cache + client-side search
// -------------------------------------------------------------
window.__posHotelRoomPickerItems = window.__posHotelRoomPickerItems || null;

window.__posHotelRoomPickerStorageKey = function() {
    var bid = window.posConfig && window.posConfig.branchId ? window.posConfig.branchId : '0';
    return 'pos_hotel_room_picker_v1_' + bid;
};

window.readPosHotelRoomPickerCache = function() {
    try {
        if (typeof localStorage === 'undefined') {
            return null;
        }
        var raw = localStorage.getItem(window.__posHotelRoomPickerStorageKey());
        if (!raw) {
            return null;
        }
        return JSON.parse(raw);
    } catch (e) {
        return null;
    }
};

window.writePosHotelRoomPickerCache = function(payload) {
    try {
        if (typeof localStorage === 'undefined') {
            return;
        }
        localStorage.setItem(window.__posHotelRoomPickerStorageKey(), JSON.stringify(payload));
    } catch (e) {
        // ignore quota / private mode
    }
};

window.filterPosHotelRoomPickerItems = function(items, term) {
    if (!term) {
        return items || [];
    }
    var t = String(term).toLowerCase();
    return (items || []).filter(function(it) {
        return (String(it.room_number || '').toLowerCase().indexOf(t) !== -1)
            || (String(it.stay_number || '').toLowerCase().indexOf(t) !== -1)
            || (String(it.guest_name || '').toLowerCase().indexOf(t) !== -1)
            || (String(it.room_type_name || '').toLowerCase().indexOf(t) !== -1);
    });
};

window.escapeHtmlPos = function(s) {
    if (s === undefined || s === null) {
        return '';
    }
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
};

window.renderHotelRoomPickerListAjax = function(searchTerm) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    var items = window.__posHotelRoomPickerItems || [];
    var filtered = window.filterPosHotelRoomPickerItems(items, searchTerm);
    var $list = $('#hotel-room-stay-list-ajax');
    var $empty = $('#hotel-room-stay-empty-ajax');
    $list.empty();
    if (!filtered.length) {
        $empty.removeClass('hidden');
        return;
    }
    $empty.addClass('hidden');
    var selectedId = parseInt(window.posState && window.posState.selectedStayId || 0, 10);
    var noStayMsg = @json(__('modules.order.roomServiceNoActiveStay'));
    filtered.forEach(function(it) {
        var guest = it.guest_name
            ? '<div class="text-xs text-gray-500 dark:text-gray-500 mt-1 truncate">' + window.escapeHtmlPos(it.guest_name) + '</div>'
            : '';
        var typeLine = it.room_type_name
            ? '<div class="text-[10px] text-gray-400 dark:text-gray-400 mt-0.5 truncate">' + window.escapeHtmlPos(it.room_type_name) + '</div>'
            : '';
        var stayLine = it.stay_number
            ? '<div class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">' + window.escapeHtmlPos(it.stay_number) + '</div>'
            : '';
        var unavail = (!it.selectable || !it.stay_id)
            ? '<div class="text-[10px] text-amber-700 dark:text-amber-400 mt-1 leading-tight">' + window.escapeHtmlPos(noStayMsg) + '</div>'
            : '';
        var isSelected = it.stay_id && selectedId && parseInt(it.stay_id, 10) === selectedId;
        var cardClasses = (!it.selectable || !it.stay_id)
            ? 'opacity-70 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 cursor-not-allowed'
            : (isSelected
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-sm cursor-pointer'
                : 'border-gray-200 dark:border-gray-700 hover:border-blue-500 hover:shadow-sm cursor-pointer');
        var roomLabel = window.escapeHtmlPos(it.room_number || 'N/A');
        var inputHtml = '';
        var optionAttrs = '';
        if (it.selectable && it.stay_id) {
            var checkedAttr = isSelected ? 'checked' : '';
            optionAttrs = ' data-stay-id="' + it.stay_id + '"'
                + ' data-room-number="' + window.escapeHtmlPos(String(it.room_number || 'N/A')) + '"'
                + ' data-stay-number="' + window.escapeHtmlPos(String(it.stay_number || '')) + '"';
            inputHtml = '<input type="radio" name="hotel_room_stay_radio" value="' + it.stay_id + '" class="w-4 h-4 mt-0.5 text-blue-600 bg-white border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 dark:bg-gray-800 dark:border-gray-600 shrink-0" '
                + checkedAttr + '>';
        } else {
            inputHtml = '<span class="w-4 h-4 mt-0.5 shrink-0 inline-block rounded-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800" title=""> </span>';
        }
        var html = ''
            + '<label class="block js-hotel-stay-option ' + ((!it.selectable || !it.stay_id) ? 'cursor-not-allowed' : 'cursor-pointer') + '" ' + optionAttrs + '>'
            + '<div class="border rounded-lg p-2.5 transition-all duration-200 bg-white dark:bg-gray-800 ' + cardClasses + '">'
            + '<div class="flex items-start gap-2">'
            + inputHtml
            + '<div class="flex-1 min-w-0">'
            + '<div class="font-semibold text-sm text-gray-900 dark:text-white truncate">' + roomLabel + '</div>'
            + typeLine + stayLine + guest + unavail
            + '</div></div></div></label>';
        $list.append(html);
    });
};

window.refreshHotelRoomPickerFromServer = function(done) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        if (typeof done === 'function') {
            done();
        }
        return;
    }
    $.easyAjax({
        url: "{{ route('ajax.pos.hotel.room-picker') }}",
        type: 'GET',
        success: function(response) {
            if (response && response.success && Array.isArray(response.items)) {
                window.__posHotelRoomPickerItems = response.items;
                window.writePosHotelRoomPickerCache({
                    fetched_at: response.fetched_at || null,
                    items: response.items
                });
            }
            if (typeof done === 'function') {
                done();
            }
        },
        error: function() {
            if (typeof done === 'function') {
                done();
            }
        }
    });
};

window.prefetchPosHotelRoomPickerIfRoomService = function() {
    if (!window.posState || !window.posConfig) {
        return;
    }
    var slug = window.posState.orderTypeSlug;
    var typeF = window.posState.orderType;
    var isRoom = false;
    if (typeof window.posNormalizeSidebarOrderTypeKey === 'function') {
        isRoom = window.posNormalizeSidebarOrderTypeKey(slug) === 'room_service'
            || window.posNormalizeSidebarOrderTypeKey(typeF) === 'room_service';
    } else {
        isRoom = String(slug || '') === 'room_service' || String(typeF || '') === 'room_service';
    }
    if (!isRoom) {
        return;
    }
    if (typeof window.__posIsEffectiveOnline === 'function' && !window.__posIsEffectiveOnline()) {
        return;
    }
    if (window.__posHotelRoomPickerPrefetching) {
        return;
    }
    window.__posHotelRoomPickerPrefetching = true;
    window.refreshHotelRoomPickerFromServer(function() {
        window.__posHotelRoomPickerPrefetching = false;
    });
};

window.openHotelRoomModalAjax = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    let $modal = $('#hotel-room-modal-ajax');
    if (!$modal.length) {
        $('body').append(`
            <div id="hotel-room-modal-ajax"
                 class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40"
                 onclick="if (event.target === this) { window.closeHotelRoomModalAjax && window.closeHotelRoomModalAjax(); }">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[85vh] flex flex-col"
                     onclick="event.stopPropagation();">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('hotel::modules.roomService.selectRoom') }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-snug">
                                @lang('modules.order.roomServicePickerHint')
                            </p>
                        </div>
                        <button type="button"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 shrink-0"
                                onclick="window.closeHotelRoomModalAjax && window.closeHotelRoomModalAjax()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input id="hotel-room-search-ajax"
                                   type="text"
                                   autocomplete="off"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                                   placeholder="{{ __('hotel::modules.roomService.searchByRoomOrStay') }}">
                        </div>
                    </div>
                    <div class="px-6 py-3 flex-1 min-h-0 overflow-y-auto">
                        <div id="hotel-room-stay-list-ajax" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 text-sm text-gray-800 dark:text-gray-100"></div>
                        <div id="hotel-room-stay-empty-ajax" class="hidden text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                            {{ __('messages.noRecordFound') }}
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                        <button type="button"
                                class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                                onclick="window.closeHotelRoomModalAjax && window.closeHotelRoomModalAjax()">
                            {{ __('app.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        `);
        $modal = $('#hotel-room-modal-ajax');
    }

    $modal.removeClass('hidden').addClass('flex');

    if (!window.__posHotelRoomPickerItems || !window.__posHotelRoomPickerItems.length) {
        var cached = window.readPosHotelRoomPickerCache();
        if (cached && Array.isArray(cached.items) && cached.items.length) {
            window.__posHotelRoomPickerItems = cached.items;
        }
    }

    var $search = $('#hotel-room-search-ajax');
    if ($search.length && !$search.data('hotel-search-bound')) {
        var timer = null;
        $search.on('input', function() {
            clearTimeout(timer);
            var term = $(this).val();
            timer = setTimeout(function() {
                window.renderHotelRoomPickerListAjax(term);
            }, 200);
        });
        $search.data('hotel-search-bound', '1');
    }

    var $list = $('#hotel-room-stay-list-ajax');
    if ($list.length && !$list.data('hotel-list-bound')) {
        $list.on('click', '.js-hotel-stay-option', function() {
            var $opt = $(this);
            var stayId = parseInt($opt.attr('data-stay-id') || 0, 10);
            if (!stayId) {
                return;
            }
            var roomNumber = $opt.attr('data-room-number') || '';
            var stayNumber = $opt.attr('data-stay-number') || '';
            var $radio = $opt.find('input[type="radio"][name="hotel_room_stay_radio"]');
            if ($radio.length) {
                $radio.prop('checked', true);
            }
            if (typeof window.selectHotelStayAjax === 'function') {
                window.selectHotelStayAjax(stayId, roomNumber, stayNumber);
            }
        });
        $list.on('change', 'input[type="radio"][name="hotel_room_stay_radio"]', function() {
            var stayId = parseInt($(this).val() || 0, 10);
            if (!stayId) {
                return;
            }
            var $opt = $(this).closest('.js-hotel-stay-option');
            var roomNumber = $opt.attr('data-room-number') || '';
            var stayNumber = $opt.attr('data-stay-number') || '';
            if (typeof window.selectHotelStayAjax === 'function') {
                window.selectHotelStayAjax(stayId, roomNumber, stayNumber);
            }
        });
        $list.data('hotel-list-bound', '1');
    }

    var currentTerm = $search.length ? String($search.val() || '') : '';
    window.renderHotelRoomPickerListAjax(currentTerm);

    window.refreshHotelRoomPickerFromServer(function() {
        var t = $('#hotel-room-search-ajax').length ? String($('#hotel-room-search-ajax').val() || '') : '';
        window.renderHotelRoomPickerListAjax(t);
    });
};

window.closeHotelRoomModalAjax = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    $('#hotel-room-modal-ajax').addClass('hidden').removeClass('flex');
};

window.selectHotelStayAjax = function(id, roomNumber, stayNumber) {
    if (!window.posState) {
        window.posState = {};
    }
    window.posState.selectedStayId = id;
    window.posState.selectedStayRoomNumber = roomNumber || null;
    window.posState.selectedStayNumber = stayNumber || null;
    window.posState.billTo = window.posState.billTo || 'POST_TO_ROOM';

    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#ajax-room-stay-summary-room').text(roomNumber || 'N/A');
        if (stayNumber) {
            $('#ajax-room-stay-summary-stay').text(stayNumber).removeClass('hidden');
        } else {
            $('#ajax-room-stay-summary-stay').addClass('hidden').text('');
        }
    }

    window.closeHotelRoomModalAjax();
};

window.syncSelectedHotelStaySummaryFromState = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined' || !window.posState) {
        return;
    }
    var room = window.posState.selectedStayRoomNumber;
    var stay = window.posState.selectedStayNumber;
    if (room) {
        $('#ajax-room-stay-summary-room').text(String(room));
    }
    if (stay) {
        $('#ajax-room-stay-summary-stay').text(String(stay)).removeClass('hidden');
    } else {
        $('#ajax-room-stay-summary-stay').addClass('hidden').text('');
    }
};

// Update room-service bill_to from AJAX POS select
window.setRoomServiceBillTo = function(value) {
    if (!window.posState) {
        window.posState = {};
    }
    window.posState.billTo = value || 'POST_TO_ROOM';
};

// Keep Pay type <select> in sync with posState (order load + order-type changes).
window.syncPosRoomServiceBillToSelect = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    var $el = $('#pos-room-service-bill-to');
    if (!$el.length) {
        return;
    }
    var v = (window.posState && window.posState.billTo) ? String(window.posState.billTo) : 'POST_TO_ROOM';
    if (v !== 'POST_TO_ROOM' && v !== 'PAY_NOW') {
        v = 'POST_TO_ROOM';
    }
    $el.val(v);
};

window.applyLoyaltyRedemption = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
    var points = parseInt($('#loyaltyPointsInput').val() || '0', 10);
    $('#loyaltyError').hide().text('');

    window.posLoyaltyApi.redeem(points, function(resp) {
        if (!resp || !resp.success) return;
        var data = resp.data || {};
        window.posState.loyaltyPointsRedeemed = data.points_redeemed || 0;
        window.posState.loyaltyDiscountAmount = data.discount_amount || 0;
        window.posState.discountType = '';
        window.posState.discountValue = 0;
        window.posState.discountAmount = 0;
        window.posState.discountApplyOn = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }})
            ? 'total'
            : 'sub_total';
        if (typeof window.calculateTotal === 'function') {
            window.calculateTotal();
        }
        if (typeof window.updateLoyaltyRedeemButtonVisibility === 'function') {
            window.updateLoyaltyRedeemButtonVisibility();
        }
        window.closeLoyaltyRedemptionModal();
        window.showToast?.('success', {!! json_encode(__('loyalty::app.loyaltyPointsRedeemedSuccessfully')) !!});
    }, function(xhr) {
        var msg = xhr.responseJSON?.message || @json(__('messages.somethingWentWrong'));
        $('#loyaltyError').show().text(msg);
    });
};

window.resetLoyaltyRedemption = function() {
    window.posState.loyaltyPointsRedeemed = 0;
    window.posState.loyaltyDiscountAmount = 0;
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#loyaltyPointsInput').val('');
        $('#loyalty-discount-preview').text('0');
        $('#loyaltyError').hide().text('');
    }
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateLoyaltyRedeemButtonVisibility === 'function') {
        window.updateLoyaltyRedeemButtonVisibility();
    }
};

// Print options helpers (AJAX POS)
window.openPrintOptionsModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#printOptionsModal').show();
    }
};

window.closePrintOptionsModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#printOptionsModal').hide();
    }
};

window.handlePrintOption = function(mode) {
    const orderId = window.posState ? (window.posState.orderID || (window.posState.orderDetail?.id || null)) : null;
    if (!orderId) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }

    let url = null;
    switch (mode) {
        case 'all':
            url = "{{ route('orders.print-split', ['orderId' => '__ORDER_ID__']) }}".replace('__ORDER_ID__', orderId);
            break;
        case 'summary':
        case 'individual':
            url = "{{ route('orders.print-split-receipts', ['orderId' => '__ORDER_ID__']) }}".replace('__ORDER_ID__', orderId);
            break;
        case 'single':
            url = "{{ route('orders.print', ['id' => '__ORDER_ID__']) }}".replace('__ORDER_ID__', orderId);
            break;
    }

    if (!url) {
        return;
    }

    // Use existing printLocation helper if available for PWA handling
    if (typeof window.printLocation === 'function') {
        window.printLocation(url);
    } else {
        window.open(url, '_blank');
    }

    window.closePrintOptionsModal();
};

/**
 * Run browser events queued by PosAjaxController (Livewire v3 has no PHP Livewire::dispatch).
 * Uses window.Livewire.dispatch for saveOrderImageFromPrint / saveKotImageFromPrint listeners.
 */
window.runPosAjaxLivewireDispatches = function(dispatches) {
    if (!dispatches || !dispatches.length) {
        return;
    }
    dispatches.forEach(function(item) {
        if (!item || !item.name) {
            return;
        }
        if (item.name === 'print_location' && item.params && item.params.length) {
            var u = item.params[0];
            if (typeof window.printLocation === 'function') {
                window.printLocation(u);
            } else {
                window.open(u, '_blank');
            }
            return;
        }
        if (typeof window.Livewire !== 'undefined' && typeof window.Livewire.dispatch === 'function') {
            var params = item.params;
            if (params === undefined || params === null) {
                window.Livewire.dispatch(item.name);
            } else {
                window.Livewire.dispatch(item.name, params);
            }
        }
    });
};

/** AJAX print order — same server logic as PosAjaxController::ajaxPrintOrder / Livewire printOrder */
window.handleAjaxPrintOrderResponse = function(res) {
    if (!res || !res.success) {
        const msg = res && res.message ? res.message : {!! json_encode(__('messages.printerNotConnected')) !!};
        if (typeof window.showToast === 'function') {
            window.showToast('error', msg);
        } else {
            alert(msg);
        }
        return;
    }
    if (res.mode === 'print_options') {
        if (typeof window.openPrintOptionsModal === 'function') {
            window.openPrintOptionsModal();
        }
        return;
    }
    if (res.message && typeof window.showToast === 'function') {
        window.showToast('success', res.message);
    }
    if (res.dispatches && res.dispatches.length && typeof window.runPosAjaxLivewireDispatches === 'function') {
        window.runPosAjaxLivewireDispatches(res.dispatches);
    }
    if (res.mode === 'url' && res.url) {
        if (typeof window.printLocation === 'function') {
            window.printLocation(res.url);
        } else {
            window.open(res.url, '_blank');
        }
    }
};

window.ajaxPrintOrderById = function(orderId) {
    if (!orderId) {
        return;
    }
    $.ajax({
        url: '/ajax/pos/orders/' + orderId + '/print',
        type: 'POST',
        dataType: 'json',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (typeof window.handleAjaxPrintOrderResponse === 'function') {
                window.handleAjaxPrintOrderResponse(res);
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : null;
            if (!msg && xhr.status === 419) {
                msg = 'Session expired. Please refresh the page.';
            }
            if (!msg) {
                msg = (xhr.statusText && xhr.statusText !== 'error') ? xhr.statusText : {!! json_encode(__('messages.printerNotConnected')) !!};
            }
            if (typeof window.showToast === 'function') {
                window.showToast('error', msg);
            } else {
                alert(msg);
            }
        }
    });
};

/**
 * AJAX print KOT for an order — same as Livewire Pos::printKot($order, $kotContext, $kotIds).
 * One request: kitchen ON → filtered KOT rows, each split by item kot_place; kitchen OFF → default station per kot_id.
 *
 * @param {number} orderId
 * @param {number[]} kotIds Empty array = all KOTs on order when kitchen module is on (matches Livewire empty $kotIds).
 * @param {object} [options] { onComplete: function () }
 */
window.ajaxPrintKotForOrder = function(orderId, kotIds, options) {
    options = options || {};
    var oid = parseInt(orderId, 10);
    if (!oid) {
        if (typeof options.onComplete === 'function') {
            options.onComplete();
        }
        return;
    }
    var ids = (kotIds || []).map(function(id) { return parseInt(id, 10); }).filter(function(id) { return id > 0; });
    var data = {
        _token: '{{ csrf_token() }}',
        kot_ids: ids
    };
    var url = "{{ route('ajax.pos.print-kot-order', ['orderId' => '__OID__']) }}".replace('__OID__', String(oid));
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(res) {
            if (typeof window.handleAjaxPrintKotResponse === 'function') {
                window.handleAjaxPrintKotResponse(res, {
                    afterScheduled: options.onComplete
                });
            } else if (typeof options.onComplete === 'function') {
                options.onComplete();
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : null;
            if (!msg && xhr.status === 419) {
                msg = 'Session expired. Please refresh the page.';
            }
            if (!msg) {
                msg = (xhr.statusText && xhr.statusText !== 'error') ? xhr.statusText : {!! json_encode(__('messages.printerNotConnected')) !!};
            }
            if (typeof window.showToast === 'function') {
                window.showToast('error', msg);
            } else {
                alert(msg);
            }
            if (typeof options.onComplete === 'function') {
                options.onComplete();
            }
        }
    });
};

/**
 * AJAX print KOT — same server logic as PosAjaxController::ajaxPrintKot / Livewire printKot
 * @param {object} [hooks] Optional { afterScheduled: function () } — called after browser print tabs
 *   have been scheduled (respects staggered window.open). Use this to navigate away without
 *   aborting the print request or blocking popups.
 */
window.handleAjaxPrintKotResponse = function(res, hooks) {
    const runAfterScheduled = function() {
        if (hooks && typeof hooks.afterScheduled === 'function') {
            hooks.afterScheduled();
        }
    };
    if (!res || !res.success) {
        const msg = res && res.message ? res.message : {!! json_encode(__('messages.printerNotConnected')) !!};
        if (typeof window.showToast === 'function') {
            window.showToast('error', msg);
        } else {
            alert(msg);
        }
        runAfterScheduled();
        return;
    }
    if (res.dispatches && res.dispatches.length && typeof window.runPosAjaxLivewireDispatches === 'function') {
        window.runPosAjaxLivewireDispatches(res.dispatches);
    }
    const urls = (res.urls && res.urls.length) ? res.urls : (res.url ? [res.url] : []);
    const staggerMs = 350;
    urls.forEach(function(url, i) {
        setTimeout(function() {
            if (typeof window.printLocation === 'function') {
                window.printLocation(url);
            } else {
                window.open(url, '_blank');
            }
        }, i * staggerMs);
    });
    if (res.warnings && res.warnings.length && typeof window.showToast === 'function') {
        window.showToast('warning', res.warnings.join(' '));
    } else if (res.message && typeof window.showToast === 'function') {
        window.showToast('success', res.message);
    }
    // Defer follow-up (e.g. redirect) until after print URLs run so the tab opens before unload.
    var waitMs = 0;
    if (urls.length > 1) {
        waitMs = (urls.length - 1) * staggerMs + 150;
    } else if (urls.length === 1) {
        waitMs = 150;
    } else {
        waitMs = 50;
    }
    setTimeout(runAfterScheduled, waitMs);
};

/**
 * @param {number} kotId
 * @param {object} [options] Optional { onComplete: function () } — called after print handling finishes
 *   (including delayed scheduling for browser popup print). Safe for redirect / resetPosState.
 */
window.ajaxPrintKotById = function(kotId, options) {
    options = options || {};
    if (!kotId) {
        if (typeof options.onComplete === 'function') {
            options.onComplete();
        }
        return;
    }
    $.ajax({
        url: '/ajax/pos/kot/' + kotId + '/print',
        type: 'POST',
        dataType: 'json',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (typeof window.handleAjaxPrintKotResponse === 'function') {
                window.handleAjaxPrintKotResponse(res, {
                    afterScheduled: options.onComplete
                });
            } else if (typeof options.onComplete === 'function') {
                options.onComplete();
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : null;
            if (!msg && xhr.status === 419) {
                msg = 'Session expired. Please refresh the page.';
            }
            if (!msg) {
                msg = (xhr.statusText && xhr.statusText !== 'error') ? xhr.statusText : {!! json_encode(__('messages.printerNotConnected')) !!};
            }
            if (typeof window.showToast === 'function') {
                window.showToast('error', msg);
            } else {
                alert(msg);
            }
            if (typeof options.onComplete === 'function') {
                options.onComplete();
            }
        }
    });
};

/**
 * Print several KOTs — prefers one Livewire-equivalent call ajaxPrintKotForOrder(orderId, kotIds).
 * Falls back to per-KOT requests if order id is missing.
 * @param {number[]} kotIds
 * @param {object} [options] { onComplete: function (), orderId: number }
 */
window.ajaxPrintKotIdsSequential = function(kotIds, options) {
    options = options || {};
    var ids = (kotIds || []).map(function(id) { return parseInt(id, 10); }).filter(function(id) { return id > 0; });
    if (ids.length === 0) {
        if (typeof options.onComplete === 'function') {
            options.onComplete();
        }
        return;
    }
    var oid = options.orderId || (window.posState && window.posState.orderID);
    oid = oid ? parseInt(oid, 10) : 0;
    if (oid && typeof window.ajaxPrintKotForOrder === 'function') {
        window.ajaxPrintKotForOrder(oid, ids, { onComplete: options.onComplete });
        return;
    }
    var rest = ids.slice(1);
    window.ajaxPrintKotById(ids[0], {
        onComplete: function() {
            if (rest.length) {
                window.ajaxPrintKotIdsSequential(rest, options);
            } else if (typeof options.onComplete === 'function') {
                options.onComplete();
            }
        }
    });
};

window.closeErrorModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#errorModal').hide();
    }
};

window.closeModifiersModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#modifiersModal').hide();
    }
};

// While the item modifiers modal is open, Enter confirms the same as the Save button.
(function() {
    function posModifiersModalEnterToSave(e) {
        if (e.key !== 'Enter' || e.repeat) {
            return;
        }
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            return;
        }
        var $modal = $('#modifiersModal');
        if (!$modal.length || !$modal.is(':visible')) {
            return;
        }
        var st = window.posState;
        if (!st || st.pendingMenuItemId == null) {
            return;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        if (typeof window.saveModifiers === 'function') {
            window.saveModifiers(st.pendingMenuItemId, st.pendingVariationId || null);
        }
    }
    document.addEventListener('keydown', posModifiersModalEnterToSave, true);
})();

// KOT-only click guard to block rapid duplicate clicks without changing saveOrder flow.
/** Labels for client-side KOT HTML (offline / blob print); refreshed on each page load. */
window.__posResolveWaiterNameForPrint = function(orderData) {
    const wid =
        orderData && orderData.waiter_id != null ? String(orderData.waiter_id) : '';
    if (!wid || typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return null;
    }
    const opt = $('#waiter-select option').filter(function() {
        return String($(this).val()) === wid;
    }).first();
    if (!opt.length) {
        return null;
    }
    const t = (opt.text() || '').trim();
    return t || null;
};

window.__posOfflineKotLabels = {
    pageTitleSuffix: @json(__('modules.order.kotTicket')),
    table: @json(__('modules.table.table')),
    date: @json(__('app.date')),
    time: @json(__('app.time')),
    waiter: @json(__('modules.order.waiter')),
    orderType: @json(__('modules.settings.orderType')),
    pickupAt: @json(__('modules.order.pickupAt')),
    itemName: @json(__('modules.menu.itemName')),
    qty: @json(__('modules.order.qty')),
    note: @json(__('modules.order.note')),
    specialInstructions: @json(__('modules.order.specialInstructions')),
    tokenNumber: @json(__('modules.order.tokenNumber')),
    back: @json(__('app.back')),
};

/** Slug -> translated label for offline KOT/bill blob prints (matches POS offline modal). */
@php
    $__posPrintOrderTypeMap = [
        'dine_in' => __('modules.order.dine_in'),
        'delivery' => __('modules.order.delivery'),
        'pickup' => __('modules.order.pickup'),
        'room_service' => __('modules.order.room_service'),
    ];
@endphp
window.__posPrintOrderTypeMap = @json($__posPrintOrderTypeMap);

window.__posFormatOrderTypeLabelForPrint = function(rawDisplay, slugHint) {
    const map = window.__posPrintOrderTypeMap || {};
    const slug = (slugHint || '').toString().trim().toLowerCase().replace(/[\s-]+/g, '_');
    if (slug && Object.prototype.hasOwnProperty.call(map, slug)) {
        return String(map[slug]);
    }
    const raw = (rawDisplay || '').toString().trim();
    if (!raw) {
        return '';
    }
    const key = raw.toLowerCase().replace(/[\s-]+/g, '_');
    if (Object.prototype.hasOwnProperty.call(map, key)) {
        return String(map[key]);
    }
    return raw
        .replace(/[_-]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .replace(/\b\w/g, function(m) {
            return m.toUpperCase();
        });
};

/**
 * Snapshot for printing a KOT without a server-side Kot id (offline queue).
 * Omits i18n strings; use window.__posOfflineKotLabels at print time.
 */
window.buildPosOfflineKotPrintContext = function(orderData, orderItems) {
    const actions = Array.isArray(orderData && orderData.actions) ? orderData.actions : [];
    if (actions[0] !== 'kot') {
        return null;
    }
    const st = window.posState || {};
    const now = new Date();
    const waiterName =
        typeof window.__posResolveWaiterNameForPrint === 'function'
            ? window.__posResolveWaiterNameForPrint(orderData)
            : null;
    const orderTypeSlug = (st.orderTypeSlug || orderData.order_type || '').toString();
    let pickupLine = null;
    if (orderTypeSlug === 'pickup' && st.pickupDate && st.pickupTime) {
        pickupLine = (st.pickupDate + ' ' + st.pickupTime).trim();
    }
    const modMap = st.modifierOptions || {};
    const items = (orderItems || []).map(function(row) {
        const item = st.orderItemList && st.orderItemList[row.key];
        const nm = item ? (item.item_name || item.name || ('#' + row.id)) : ('#' + row.id);
        const variation = st.orderItemVariation && st.orderItemVariation[row.key];
        const variationLabel = variation
            ? (variation.variation || variation.name || null)
            : null;
        const modIds = (st.itemModifiersSelected && st.itemModifiersSelected[row.key]) || [];
        const modifiers = [];
        (Array.isArray(modIds) ? modIds : []).forEach(function(mid) {
            const key = String(mid);
            const mo = modMap[key] || modMap[mid];
            const label = mo
                ? (mo.name || mo.option_name || mo.modifier_name || null)
                : null;
            if (label) {
                modifiers.push(label);
            }
        });
        const note = row.note != null && row.note !== ''
            ? row.note
            : (st.itemNotes && st.itemNotes[row.key]) || null;
        return {
            name: nm,
            quantity: row.quantity,
            variation_label: variationLabel,
            modifiers: modifiers,
            note: note,
        };
    });
    return {
        dir: @json(isRtl() ? 'rtl' : 'ltr'),
        restaurant_name: @json($restaurant->name ?? 'Restaurant'),
        kot_number_label: @json(__('messages.posOfflineKotNumberPending')),
        token_number: null,
        order_number_label: (st.formattedOrderNumber || st.orderNumber || '').toString().trim() || '—',
        table_code: (st.tableNo || '').toString().trim() || '—',
        printed_at_iso: now.toISOString(),
        waiter_name: waiterName,
        order_type_display: (function() {
            const raw = (orderData.order_type_display || orderData.order_type || '').toString();
            return typeof window.__posFormatOrderTypeLabelForPrint === 'function'
                ? window.__posFormatOrderTypeLabelForPrint(raw, orderTypeSlug)
                : raw;
        })(),
        pickup_line: pickupLine,
        order_note: orderData.order_note || null,
        items: items,
    };
};

window.__posEscHtml = function(s) {
    if (s == null || s === '') {
        return '';
    }
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
};

window.__buildOfflineKotPrintHtml = function(ctx) {
    // helpers and config
    const labels = window.__posOfflineKotLabels || {};
    const dir = (ctx && ctx.dir) || 'ltr';
    const esc = window.__posEscHtml;
    const widthMm = 80;
    const baseFont = '9pt', smallFont = '8pt', titleFont = '14pt';
    let dateStr = '', timeStr = '';

    // Format date/time when possible
    try {
        const d = ctx.printed_at_iso ? new Date(ctx.printed_at_iso) : new Date();
        const loc = (window.posConfig && window.posConfig.locale) || undefined;
        dateStr = d.toLocaleDateString(loc, { year: 'numeric', month: 'short', day: 'numeric' });
        timeStr = d.toLocaleTimeString(loc, { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        // fallback: leave date/time empty
    }

    // Table rows for each item
    const itemsHtml = (ctx.items || []).map(it => {
        const modLines = (it.modifiers || [])
            .map(m => `<div class="modifiers">• ${esc(m)}</div>`)
            .join('');
        const varLine = it.variation_label
            ? `<br><small>(${esc(it.variation_label)})</small>`
            : '';
        const noteLine = it.note
            ? `<div class="modifiers"><strong>${esc(labels.note)}:</strong> ${esc(it.note)}</div>`
            : '';
        return `
            <tr>
                <td class="description">
                    ${esc(it.name)}
                    ${varLine}
                    ${modLines}
                    ${noteLine}
                </td>
                <td class="qty">${esc(it.quantity)}</td>
            </tr>
        `;
    }).join('');

    // Rows for waiter, order type etc
    const waiterRow = ctx.waiter_name
        ? `
            <div class="order-row">
                <table>
                    <tr>
                        <td class="order-left">${esc(labels.waiter)}: <span class="bold">${esc(ctx.waiter_name)}</span></td>
                        <td class="order-right"></td>
                    </tr>
                </table>
            </div>
        `
        : '';

    const orderTypeRow = ctx.order_type_display
        ? `
            <div class="order-row">
                <table>
                    <tr>
                        <td class="order-left">${esc(labels.orderType)}: <span class="bold">${esc(ctx.order_type_display)}</span></td>
                        <td class="order-right">
                            ${ctx.pickup_line
                                ? `${esc(labels.pickupAt)}: <span class="bold">${esc(ctx.pickup_line)}</span>`
                                : ''}
                        </td>
                    </tr>
                </table>
            </div>
        `
        : '';

    const kotNoteBlock = ctx.order_note
        ? `
            <div class="footer">
                <strong>${esc(labels.specialInstructions)}:</strong>
                <div class="italic">${esc(ctx.order_note)}</div>
            </div>
        `
        : '';

    // Title for document
    const title = `${esc(ctx.restaurant_name)} - ${esc(labels.pageTitleSuffix)}`;

    // Compose full HTML (split blocks for legibility)
    const html = `
<!DOCTYPE html>
<html lang="${esc((window.posConfig && window.posConfig.locale) || '')}" dir="${esc(dir)}">
<head>
    <meta charset="UTF-8">
    <title>${title}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:DejaVu Sans,Arial,sans-serif }
        [dir="rtl"] { text-align:right }
        [dir="ltr"] { text-align:left }
        .receipt { width:${widthMm - 10}mm; padding:6.35mm }
        .header { text-align:center; margin-bottom:3mm }
        .restaurant-info { font-size:${baseFont}; margin-bottom:1mm }
        .order-info {
            text-align:center;
            border-top:1px dashed #000;
            border-bottom:1px dashed #000;
            padding:2mm 0;
            margin-bottom:3mm;
            font-size:${baseFont}
        }
        .kot-title {
            font-size:${titleFont};
            font-weight:bold;
            text-align:center;
            margin-bottom:2mm
        }
        .items-table {
            width:100%;
            border-collapse:collapse;
            margin-bottom:3mm;
            font-size:${baseFont}
        }
        .items-table th { padding:1mm; border-bottom:1px solid #000 }
        [dir="rtl"] .items-table th { text-align:right }
        [dir="ltr"] .items-table th { text-align:left }
        .items-table td { padding:1mm 0; vertical-align:top }
        .qty { width:15%; text-align:center }
        .description { width:85% }
        .modifiers { font-size:${smallFont}; color:#555 }
        .footer {
            text-align:center;
            margin-top:3mm;
            font-size:9pt;
            padding-top:2mm;
            border-top:1px dashed #000
        }
        .italic { font-style:italic }
        .bold { font-weight:bold }
        .order-row { width:100%; margin-bottom:5px }
        .order-row table { width:100%; border-collapse:collapse }
        .order-left { text-align:left; width:50% }
        .order-right { text-align:right; width:50% }
        .back-button {
            position:fixed;
            top:10px;
            left:10px;
            z-index:1000;
            padding:10px 20px;
            background:#3b82f6;
            color:#fff;
            border:none;
            border-radius:5px;
            cursor:pointer;
            font-size:14px
        }
        @media print {
            @page { margin:0; size:80mm auto }
            .back-button { display:none!important }
        }
    </style>
</head>
<body>
    <button
        type="button"
        class="back-button"
        onclick="goBack()"
        id="backButton"
        style="display:none"
    >← ${esc(labels.back)}</button>
    <div class="receipt">
        <div class="header">
            <div class="restaurant-info">${esc(ctx.restaurant_name)}</div>
        </div>
        <div class="kot-title">
            KOT <span class="bold">#${esc(ctx.kot_number_label)}</span>
        </div>
        <div class="order-info" style="margin-bottom:3mm">
            <div class="order-row">
                <table>
                    <tr>
                        <td class="order-left"><span class="bold">${esc(ctx.order_number_label)}</span></td>
                        <td class="order-right">
                            <span>${esc(labels.table)}: <span class="bold">${esc(ctx.table_code)}</span></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="order-row">
                <table>
                    <tr>
                        <td class="order-left">${esc(labels.date)}: ${esc(dateStr)}</td>
                        <td class="order-right">${esc(labels.time)}: ${esc(timeStr)}</td>
                    </tr>
                </table>
            </div>
            ${waiterRow}
            ${orderTypeRow}
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="description">${esc(labels.itemName)}</th>
                    <th class="qty">${esc(labels.qty)}</th>
                </tr>
            </thead>
            <tbody>
                ${itemsHtml}
            </tbody>
        </table>
        ${kotNoteBlock}
    </div>
    <script>
        function isPWA() {
            return (
                window.matchMedia("(display-mode: standalone)").matches ||
                window.navigator.standalone === true ||
                document.referrer.includes("android-app://")
            );
        }
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }
        if (isPWA()) {
            var b = document.getElementById("backButton");
            if (b) b.style.display = "block";
        }
        window.onload = function() {
            if (window.self !== window.top) return;
            var closeAfter = function() {
                if (isPWA()) {
                    goBack();
                } else {
                    window.close();
                }
            };
            if ('onafterprint' in window) {
                window.onafterprint = closeAfter;
            } else {
                setTimeout(closeAfter, 1000);
            }
            window.print();
        };
    <\/script>
</body>
</html>
`;
    return html;
};

/**
 * Open a new tab with KOT HTML (blob URL) and trigger print — works fully offline.
 */
window.openPosOfflineKotPrintTab = function(context) {
    if (!context || !Array.isArray(context.items) || context.items.length === 0) {
        return;
    }
    const ctx = Object.assign({}, context, {
        printed_at_iso: new Date().toISOString(),
    });
    const html = window.__buildOfflineKotPrintHtml(ctx);
    let url = '';
    try {
        const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
        url = URL.createObjectURL(blob);
    } catch (e) {
        console.error('Offline KOT print: blob failed', e);
        return;
    }
    const w = window.open(url, '_blank');
    if (!w) {
        URL.revokeObjectURL(url);
        if (typeof window.showToast === 'function') {
            window.showToast('warning', @json(__('messages.posOfflineKotPrintPopupBlocked')));
        } else {
            alert(@json(__('messages.posOfflineKotPrintPopupBlocked')));
        }
        return;
    }
    setTimeout(function() {
        try {
            URL.revokeObjectURL(url);
        } catch (e2) {
            // ignore
        }
    }, 120000);
};

window.__posOfflineBillLabels = {
    billReceipt: @json(__('modules.order.bill')),
    qty: @json(__('modules.order.qty')),
    itemName: @json(__('modules.menu.itemName')),
    price: @json(__('modules.order.price')),
    amount: @json(__('modules.order.amount')),
    subTotal: @json(__('modules.order.subTotal')),
    discount: @json(__('modules.order.discount')),
    tip: @json(__('modules.order.tip')),
    deliveryFee: @json(__('modules.delivery.deliveryFee')),
    freeDelivery: @json(__('modules.delivery.freeDelivery')),
    totalTax: @json(__('modules.order.totalTax')),
    total: @json(__('modules.order.total')),
    table: @json(__('modules.table.table')),
    noOfPax: @json(__('modules.order.noOfPax')),
    waiter: @json(__('modules.order.waiter')),
    orderType: @json(__('modules.settings.orderType')),
    pickupAt: @json(__('modules.order.pickupAt')),
    customer: @json(__('modules.customer.customer')),
    phone: @json(__('modules.customer.phone')),
    orderNote: @json(__('modules.order.specialInstructions')),
    thankYou: @json(__('messages.thankYouVisit')),
    unpaid: @json(__('modules.order.unpaid')),
    paymentStatus: @json(__('modules.order.paymentStatus')),
    loyaltyDiscount: @json(__('app.loyaltyDiscount')),
    points: @json(__('app.points')),
    back: @json(__('app.back')),
};

/**
 * Snapshot for printing a bill (order receipt) without a server order id — offline queue.
 */
window.buildPosOfflineBillPrintContext = function(orderData, orderItems) {
    const actions = Array.isArray(orderData && orderData.actions) ? orderData.actions : [];
    if (!actions.includes('bill')) {
        return null;
    }
    const st = window.posState || {};
    const fc =
        typeof window.formatCurrency === 'function'
            ? function(n) {
                return window.formatCurrency(Number(n) || 0);
            }
            : function(n) {
                return String((Number(n) || 0).toFixed(2));
            };
    const waiterName =
        typeof window.__posResolveWaiterNameForPrint === 'function'
            ? window.__posResolveWaiterNameForPrint(orderData)
            : null;
    const orderTypeSlug = (st.orderTypeSlug || orderData.order_type || '').toString();
    let pickupLine = null;
    if (orderTypeSlug === 'pickup' && st.pickupDate && st.pickupTime) {
        pickupLine = (st.pickupDate + ' ' + st.pickupTime).trim();
    }
    const modMap = st.modifierOptions || {};
    const items = (orderItems || []).map(function(row) {
        const item = st.orderItemList && st.orderItemList[row.key];
        const nm = item ? (item.item_name || item.name || ('#' + row.id)) : ('#' + row.id);
        const variation = st.orderItemVariation && st.orderItemVariation[row.key];
        const variationLabel = variation
            ? (variation.variation || variation.name || null)
            : null;
        const modIds = (st.itemModifiersSelected && st.itemModifiersSelected[row.key]) || [];
        const modifierLines = [];
        (Array.isArray(modIds) ? modIds : []).forEach(function(mid) {
            const key = String(mid);
            const mo = modMap[key] || modMap[mid];
            const label = mo
                ? (mo.name || mo.option_name || mo.modifier_name || null)
                : null;
            if (label) {
                const mp =
                    mo && (mo.price != null || mo.option_price != null)
                        ? Number(mo.price != null ? mo.price : mo.option_price)
                        : 0;
                modifierLines.push({
                    text: label,
                    extra_fmt: mp > 0 ? '(+' + fc(mp) + ')' : '',
                });
            }
        });
        return {
            name: nm,
            quantity: row.quantity,
            price_fmt: fc(row.price),
            amount_fmt: fc(row.amount),
            variation_label: variationLabel,
            modifier_lines: modifierLines,
        };
    });
    const extraChargeRows = [];
    (Array.isArray(orderData.extra_charges) ? orderData.extra_charges : []).forEach(function(ch) {
        if (!ch || ch.is_enabled === false) {
            return;
        }
        const label = ch.name || ch.charge_name || '—';
        const amt = Number(ch.amount != null ? ch.amount : 0);
        if (amt !== 0) {
            extraChargeRows.push({ label: label, amount_fmt: fc(amt) });
        }
    });
    const loyaltyAmt = Number(orderData.loyalty_discount_amount || 0);
    const discAmt = Number(orderData.discount_amount || 0);
    const taxAmt = Number(orderData.total_tax_amount || 0);
    const tipAmt = Number(orderData.tip_amount || 0);
    const delivAmt = Number(orderData.delivery_fee || 0);
    return {
        dir: @json(isRtl() ? 'rtl' : 'ltr'),
        restaurant_name: @json($restaurant->name ?? ''),
        restaurant_phone: @json($restaurant->phone_number ?? ''),
        order_number_label: (st.formattedOrderNumber || st.orderNumber || '').toString().trim() || '—',
        printed_at_iso: new Date().toISOString(),
        table_code: (st.tableNo || '').toString().trim() || '—',
        no_of_pax: orderData.pax != null ? orderData.pax : st.noOfPax,
        waiter_name: waiterName,
        order_type_display: (function() {
            const raw = (orderData.order_type_display || orderData.order_type || '').toString();
            return typeof window.__posFormatOrderTypeLabelForPrint === 'function'
                ? window.__posFormatOrderTypeLabelForPrint(raw, orderTypeSlug)
                : raw;
        })(),
        pickup_line: pickupLine,
        customer: orderData.customer || null,
        order_note: orderData.order_note || null,
        items: items,
        sub_total_fmt: fc(orderData.sub_total),
        discount_amount: discAmt,
        discount_type: orderData.discount_type || null,
        discount_value: orderData.discount_value,
        discount_fmt: fc(discAmt),
        loyalty_discount_amount: loyaltyAmt,
        loyalty_discount_fmt: fc(loyaltyAmt),
        loyalty_points: parseInt(orderData.loyalty_points_redeemed || 0, 10) || 0,
        extra_charge_rows: extraChargeRows,
        tip_amount: tipAmt,
        tip_fmt: fc(tipAmt),
        delivery_fee: delivAmt,
        delivery_fmt: fc(delivAmt),
        order_type_slug: orderTypeSlug,
        total_tax_amount: taxAmt,
        total_tax_fmt: fc(taxAmt),
        total_fmt: fc(orderData.total),
    };
};

window.__buildOfflineBillPrintHtml = function (ctx) {
    // Helper Vars
    const labels = window.__posOfflineBillLabels || {};
    const esc = window.__posEscHtml;
    const dir = (ctx && ctx.dir) || 'ltr';
    const widthMm = 80;

    // Format current date/time string
    let dateTimeStr = '';
    try {
        const d = ctx.printed_at_iso ? new Date(ctx.printed_at_iso) : new Date();
        const loc = (window.posConfig && window.posConfig.locale) || undefined;
        dateTimeStr =
            d.toLocaleDateString(loc, { year: 'numeric', month: 'short', day: 'numeric' }) +
            ' ' +
            d.toLocaleTimeString(loc, { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        dateTimeStr = '';
    }

    // Render item rows
    const itemsRows = (ctx.items || [])
        .map(function (item) {
            // Item modifiers, if any
            const modifierHtml = (item.modifier_lines || [])
                .map(m =>
                    `<div class="modifiers">• ${esc(m.text)}${m.extra_fmt ? ' ' + esc(m.extra_fmt) : ''}</div>`
                )
                .join('');
            // Variation label, if present
            const variationHtml = item.variation_label
                ? `<br><small>(${esc(item.variation_label)})</small>`
                : '';
            return `
                <tr>
                    <td class="qty">${esc(item.quantity)}</td>
                    <td class="description">${esc(item.name)}${variationHtml}${modifierHtml}</td>
                    <td class="price">${esc(item.price_fmt)}</td>
                    <td class="amount">${esc(item.amount_fmt)}</td>
                </tr>
            `;
        })
        .join('');

    // Discount row
    let discountRow = '';
    if (ctx.discount_amount > 0) {
        let discLabel = esc(labels.discount);
        if (ctx.discount_type === 'percent' && ctx.discount_value != null) {
            const dv = String(ctx.discount_value).replace(/\.?0+$/, '');
            discLabel += ` (${esc(dv)}%)`;
        }
        discountRow = `
            <div class="summary-row">
                <table>
                    <tr>
                        <td>${discLabel}</td>
                        <td>-${esc(ctx.discount_fmt)}</td>
                    </tr>
                </table>
            </div>
        `;
    }

    // Loyalty row
    let loyaltyRow = '';
    if (ctx.loyalty_discount_amount > 0 && ctx.loyalty_points > 0) {
        loyaltyRow = `
            <div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(labels.loyaltyDiscount)} (${esc(String(ctx.loyalty_points))} ${esc(labels.points)})</td>
                        <td>-${esc(ctx.loyalty_discount_fmt)}</td>
                    </tr>
                </table>
            </div>
        `;
    }

    // Extra charges rows
    const extraRows = (ctx.extra_charge_rows || [])
        .map(row =>
            `<div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(row.label)}</td>
                        <td>${esc(row.amount_fmt)}</td>
                    </tr>
                </table>
            </div>`
        )
        .join('');

    // Delivery row
    let deliveryRow = '';
    if (ctx.order_type_slug === 'delivery') {
        deliveryRow = `
            <div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(labels.deliveryFee)}</td>
                        <td>${ctx.delivery_fee > 0 ? esc(ctx.delivery_fmt) : esc(labels.freeDelivery)}</td>
                    </tr>
                </table>
            </div>
        `;
    }

    // Tip row
    let tipRow = '';
    if (ctx.tip_amount > 0) {
        tipRow = `
            <div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(labels.tip)}</td>
                        <td>${esc(ctx.tip_fmt)}</td>
                    </tr>
                </table>
            </div>
        `;
    }

    // Tax row
    let taxRow = '';
    if (ctx.total_tax_amount > 0) {
        taxRow = `
            <div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(labels.totalTax)}</td>
                        <td>${esc(ctx.total_tax_fmt)}</td>
                    </tr>
                </table>
            </div>
        `;
    }

    // Customer block
    const cust = ctx.customer;
    let custBlock = '';
    if (cust && (cust.name || cust.phone)) {
        custBlock = `
            <div class="summary-row">
                <span>${esc(labels.customer)}: ${esc(cust.name || '—')}</span>
            </div>
        `;
        if (cust.phone) {
            custBlock += `
                <div class="summary-row">
                    <span>${esc(labels.phone)}: <span dir="ltr">${esc(cust.phone)}</span></span>
                </div>
            `;
        }
    }

    // Note block
    const noteBlock = ctx.order_note
        ? `<div class="summary-row" style="margin-top:2mm"><em>${esc(labels.orderNote)}: ${esc(ctx.order_note)}</em></div>`
        : '';

    // Waiter row
    const waiterRow = ctx.waiter_name
        ? `<div class="summary-row"><span>${esc(labels.waiter)}: ${esc(ctx.waiter_name)}</span></div>`
        : '';

    // Pax row
    const paxRow = ctx.no_of_pax
        ? `<div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(labels.table)}: ${esc(ctx.table_code)}</td>
                        <td>${esc(labels.noOfPax)}: ${esc(String(ctx.no_of_pax))}</td>
                    </tr>
                </table>
            </div>`
        : `<div class="summary-row"><span>${esc(labels.table)}: ${esc(ctx.table_code)}</span></div>`;

    // Order type row
    const orderTypeRow = ctx.order_type_display
        ? `<div class="summary-row">
                <span>${esc(labels.orderType)}: ${esc(ctx.order_type_display)}${
                ctx.pickup_line ? ' — ' + esc(labels.pickupAt) + ': ' + esc(ctx.pickup_line) : ''
            }</span>
           </div>`
        : '';

    // Title and phone line
    const title = `${esc(ctx.restaurant_name)} — ${esc(labels.billReceipt)} ${esc(ctx.order_number_label)}`;
    const phoneLine = ctx.restaurant_phone
        ? `<div class="restaurant-info">${esc(labels.phone)}: <span dir="ltr">${esc(ctx.restaurant_phone)}</span></div>`
        : '';

    // HTML Template
    return `
<!DOCTYPE html>
<html lang="${esc((window.posConfig && window.posConfig.locale) || '')}" dir="${esc(dir)}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>${title}</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:DejaVu Sans,Arial,sans-serif
        }
        [dir="rtl"]{text-align:right}
        [dir="ltr"]{text-align:left}
        .receipt{width:${widthMm - 5}mm;padding:6.35mm}
        .restaurant-name{text-align:center;font-size:14pt;font-weight:bold;margin-bottom:1mm}
        .restaurant-info{font-size:9pt;margin-bottom:1mm;text-align:center}
        .order-info{border-top:1px dashed #000;border-bottom:1px dashed #000;padding:2mm 0;margin-bottom:3mm;font-size:9pt}
        .summary-row{margin-bottom:1mm;font-size:9pt}
        .summary-row table{width:100%;border-collapse:collapse}
        .summary-row td:first-child{text-align:left}
        .summary-row td:last-child{text-align:right}
        .items-table{width:100%;border-collapse:collapse;margin-bottom:3mm;font-size:9pt}
        .items-table th{padding:1mm;border-bottom:1px solid #000}
        [dir="rtl"] .items-table th{text-align:right}
        [dir="ltr"] .items-table th{text-align:left}
        .items-table td{padding:1mm 0.5mm;vertical-align:top}
        .qty{width:10%;text-align:center}
        .description{width:52%}
        .price{width:18%;text-align:right;padding-right:1mm}
        .amount{width:16%;text-align:right}
        .modifiers{font-size:8pt;color:#555}
        .summary{margin-top:2mm}
        .total{
            font-weight:bold;
            font-size:11pt;
            border-top:1px solid #000;
            padding-top:1mm;
            margin-top:1mm
        }
        .footer{
            text-align:center;
            margin-top:3mm;
            font-size:9pt;
            padding-top:2mm;
            border-top:1px dashed #000
        }
        .back-button{
            position:fixed;
            top:10px;
            left:10px;
            z-index:1000;
            padding:10px 20px;
            background:#3b82f6;
            color:#fff;
            border:none;
            border-radius:5px;
            cursor:pointer;
            font-size:14px
        }
        @media print{
            @page{margin:0;size:80mm auto}
            .back-button{display:none!important}
        }
    </style>
</head>
<body>
    <button type="button" class="back-button" onclick="goBack()" id="backButton" style="display:none">
        ← ${esc(labels.back)}
    </button>
    <div class="receipt">
        <div class="header">
            <div class="restaurant-name">${esc(ctx.restaurant_name)}</div>
            ${phoneLine}
        </div>
        <div class="order-info">
            <div class="summary-row">
                <table>
                    <tr>
                        <td><span style="font-weight:bold">${esc(ctx.order_number_label)}</span></td>
                        <td style="text-align:right">${esc(dateTimeStr)}</td>
                    </tr>
                </table>
            </div>
            ${paxRow}
            ${waiterRow}
            ${orderTypeRow}
            ${custBlock}
            ${noteBlock}
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="qty">${esc(labels.qty)}</th>
                    <th class="description">${esc(labels.itemName)}</th>
                    <th class="price">${esc(labels.price)}</th>
                    <th class="amount">${esc(labels.amount)}</th>
                </tr>
            </thead>
            <tbody>
                ${itemsRows}
            </tbody>
        </table>
        <div class="summary">
            <div class="summary-row">
                <table>
                    <tr>
                        <td>${esc(labels.subTotal)}:</td>
                        <td>${esc(ctx.sub_total_fmt)}</td>
                    </tr>
                </table>
            </div>
            ${discountRow}
            ${loyaltyRow}
            ${extraRows}
            ${deliveryRow}
            ${tipRow}
            ${taxRow}
            <div class="summary-row total">
                <table>
                    <tr>
                        <td>${esc(labels.total)}:</td>
                        <td>${esc(ctx.total_fmt)}</td>
                    </tr>
                </table>
            </div>
            <div class="summary-row" style="margin-top:2mm;padding-top:2mm;border-top:1px dashed #000">
                <table>
                    <tr>
                        <td style="font-weight:bold">${esc(labels.paymentStatus)}</td>
                        <td style="font-weight:bold;text-align:right">${esc(labels.unpaid)}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="footer"><p>${esc(labels.thankYou)}</p></div>
    </div>

    <script>
        function isPWA() {
            return (
                (window.matchMedia("(display-mode: standalone)").matches) ||
                (window.navigator.standalone === true) ||
                (document.referrer.includes("android-app://"))
            );
        }
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.close();
            }
        }
        if (isPWA()) {
            var b = document.getElementById("backButton");
            if (b) b.style.display = "block";
        }
        window.onload = function () {
            if (window.self !== window.top) return;
            var closeAfter = function () {
                if (isPWA()) {
                    goBack();
                } else {
                    window.close();
                }
            };
            if ('onafterprint' in window) {
                window.onafterprint = closeAfter;
            } else {
                setTimeout(closeAfter, 1000);
            }
            window.print();
        };
    <\/script>
</body>
</html>
    `;
};

window.openPosOfflineBillPrintTab = function(context) {
    if (!context || !Array.isArray(context.items) || context.items.length === 0) {
        return;
    }
    const ctx = Object.assign({}, context, {
        printed_at_iso: new Date().toISOString(),
    });
    const html = window.__buildOfflineBillPrintHtml(ctx);
    let url = '';
    try {
        const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
        url = URL.createObjectURL(blob);
    } catch (e) {
        console.error('Offline bill print: blob failed', e);
        return;
    }
    const w = window.open(url, '_blank');
    if (!w) {
        URL.revokeObjectURL(url);
        if (typeof window.showToast === 'function') {
            window.showToast('warning', @json(__('messages.posOfflineKotPrintPopupBlocked')));
        } else {
            alert(@json(__('messages.posOfflineKotPrintPopupBlocked')));
        }
        return;
    }
    setTimeout(function() {
        try {
            URL.revokeObjectURL(url);
        } catch (e2) {
            // ignore
        }
    }, 120000);
};

/**
 * Rebuild posState KOT lines from a fresh /ajax/pos/orders/{id} payload (no full page reload).
 * Keeps non–kot_* cart keys (e.g. newly typed lines) intact; replaces all kot_* rows from server.
 */
window.applyPosKotOrderSnapshotFromAjaxOrder = function(order) {
    if (!order || !window.posState) {
        return;
    }

    const kotKeyRe = /^kot_\d+_\d+$/;
    const existingKotKeys = Object.keys(window.posState.orderItemList || {}).filter(function(k) {
        return kotKeyRe.test(k);
    });
    if (existingKotKeys.length && typeof window.removePosCartLinesByKeys === 'function') {
        window.removePosCartLinesByKeys(existingKotKeys);
    }

    const kotRows = Array.isArray(order.kot) ? order.kot.slice() : (order.kot ? [order.kot] : []);
    kotRows.sort(function(a, b) {
        const ta = a && a.created_at ? new Date(a.created_at).getTime() : 0;
        const tb = b && b.created_at ? new Date(b.created_at).getTime() : 0;
        return ta - tb;
    });

    kotRows.forEach(function(kotRow) {
        if (!kotRow || !kotRow.id) {
            return;
        }
        const rawItems = kotRow.items || [];
        const items = rawItems.filter(function(it) {
            return it && String(it.status || '').toLowerCase() !== 'cancelled';
        });
        items.forEach(function(item) {
            const key = 'kot_' + kotRow.id + '_' + item.id;
            const mi = item.menu_item || {};
            const menuPayload = {
                id: mi.id,
                name: mi.name || mi.item_name,
                item_name: mi.item_name || mi.name,
                price: parseFloat(mi.price != null ? mi.price : 0) || 0,
            };
            if (Array.isArray(mi.taxes)) {
                menuPayload.taxes = mi.taxes;
            }
            if (Array.isArray(mi.eu_allergen_keys)) {
                menuPayload.eu_allergen_keys = mi.eu_allergen_keys;
            }
            if (Array.isArray(mi.dietary_labels)) {
                menuPayload.dietary_labels = mi.dietary_labels;
            }
            menuPayload.is_free_item_from_stamp = !!item.is_free_item_from_stamp;
            menuPayload.stamp_rule_id = item.stamp_rule_id || null;

            window.posState.orderItemList[key] = menuPayload;
            window.posState.orderItemQty[key] = parseInt(item.quantity, 10) || 1;
            window.posState.orderItemAmount[key] = parseFloat(item.amount != null ? item.amount : 0) || 0;

            const mods = item.modifier_options || item.modifierOptions || [];
            const modIds = [];
            let modPrice = 0;
            if (Array.isArray(mods)) {
                mods.forEach(function(m) {
                    if (!m) {
                        return;
                    }
                    const mid = parseInt(m.id, 10);
                    if (mid) {
                        modIds.push(mid);
                    }
                    modPrice += parseFloat(m.price != null ? m.price : 0) || 0;
                });
            }
            window.posState.itemModifiersSelected[key] = modIds;
            window.posState.orderItemModifiersPrice[key] = modPrice;

            const mv = item.menu_item_variation || item.menuItemVariation;
            if (mv && mv.id) {
                window.posState.orderItemVariation[key] = {
                    id: mv.id,
                    name: mv.variation || mv.name,
                    price: parseFloat(mv.price != null ? mv.price : 0) || 0,
                };
            } else if (window.posState.orderItemVariation) {
                delete window.posState.orderItemVariation[key];
            }

            if (item.note) {
                window.posState.itemNotes[key] = item.note;
            } else if (window.posState.itemNotes) {
                delete window.posState.itemNotes[key];
            }

            const oid = item.order_item_id != null ? parseInt(item.order_item_id, 10) : null;
            if (oid && window.posState.orderItemIds) {
                window.posState.orderItemIds[key] = oid;
            }

            if (window.posState.orderItemTaxDetails) {
                let decodedBreakup = null;
                if (item.tax_breakup) {
                    if (typeof item.tax_breakup === 'string') {
                        try {
                            decodedBreakup = JSON.parse(item.tax_breakup);
                        } catch (e) {
                            decodedBreakup = null;
                        }
                    } else if (typeof item.tax_breakup === 'object') {
                        decodedBreakup = item.tax_breakup;
                    }
                }
                if (decodedBreakup && Object.keys(decodedBreakup).length) {
                    window.posState.orderItemTaxDetails[key] = {
                        qty: parseInt(item.quantity, 10) || 1,
                        tax_breakup: decodedBreakup,
                        total_tax: parseFloat(item.tax_amount != null ? item.tax_amount : 0) || 0,
                    };
                } else {
                    delete window.posState.orderItemTaxDetails[key];
                }
            }
        });
    });

    window.posState.orderDetail = Object.assign({}, window.posState.orderDetail || {}, order);
    window.posState.orderID = order.id || window.posState.orderID;
    window.posState.showOrderDetail = true;

    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }
    if (typeof window.updateOrderDetailTotalsFromResponse === 'function') {
        window.updateOrderDetailTotalsFromResponse(order, null);
    }
};

/**
 * After a successful Bill save on the Blade POS: avoid resetPosState() on /pos/kot/{id},
 * which wipes orderItemList and leaves KOT line containers empty when the order-detail drawer closes.
 * Refreshes the right column from the server (e.g. order_detail when status becomes billed) and syncs posState.
 */
window.__posAfterBillSaveSuccess = function(response, orderId, opts) {
    opts = opts || {};
    var dispatchShowOrderDetail = opts.dispatchShowOrderDetail !== false;

    var doToast = function() {
        if (response && response.message) {
            if (typeof window.showToast === 'function') {
                window.showToast('success', response.message);
            } else {
                alert(response.message);
            }
        }
    };

    var finishBillUi = function() {
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('refreshPos');
        }
        doToast();
    };

    var isPosKotOrderRoute = /\/pos\/kot\/\d+/.test(
        (window.location && window.location.pathname) ? window.location.pathname : ''
    );

    // On /pos/kot/{id} the right column refreshes to pos.order_detail — skip the global drawer to avoid duplicate UI.
    if (dispatchShowOrderDetail && orderId && typeof Livewire !== 'undefined' && !isPosKotOrderRoute) {
        Livewire.dispatch('showOrderDetail', { id: orderId, fromPos: true });
        if (typeof window.syncPosNavOffset === 'function') {
            requestAnimationFrame(window.syncPosNavOffset);
        }
    }

    if (isPosKotOrderRoute && orderId) {
        if (response && response.order && window.posState) {
            window.posState.orderID = response.order.id || orderId;
            window.posState.orderDetail = Object.assign({}, window.posState.orderDetail || {}, response.order);
        }
        if (typeof window.refreshOrderPanelsFromServer === 'function') {
            var panelUrl;
            try {
                var u = new URL(window.location.href);
                panelUrl = u.pathname + u.search + u.hash;
            } catch (e) {
                panelUrl = window.location.href;
            }
            window.refreshOrderPanelsFromServer({
                url: panelUrl,
                onSuccess: function() {
                    if (typeof window.syncPosNavOffset === 'function') {
                        requestAnimationFrame(window.syncPosNavOffset);
                    }
                    $.ajax({
                        url: '/ajax/pos/orders/' + orderId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(fetchRes) {
                            if (fetchRes && fetchRes.success && fetchRes.order && window.posState) {
                                window.posState.orderDetail = fetchRes.order;
                                window.posState.orderID = fetchRes.order.id;
                                var ost = fetchRes.order.status;
                                if (ost === 'kot' && typeof window.applyPosKotOrderSnapshotFromAjaxOrder === 'function') {
                                    window.applyPosKotOrderSnapshotFromAjaxOrder(fetchRes.order);
                                } else {
                                    if (typeof window.calculateTotal === 'function') {
                                        window.calculateTotal();
                                    }
                                    if (typeof window.updateTotalsDisplay === 'function') {
                                        window.updateTotalsDisplay();
                                    }
                                }
                            }
                        },
                        complete: function() {
                            finishBillUi();
                        }
                    });
                },
                onError: finishBillUi
            });
        } else {
            finishBillUi();
        }
    } else {
        window.resetPosState();
        finishBillUi();
    }
};

window.saveKotActionOnce = function() {
    // Strict guard: once a KOT submit starts, block subsequent KOT clicks
    // until current flow ends (success navigation, client refresh, or explicit unlock on error).
    if (window.__posKotSubmissionLocked) {
        return;
    }

    const now = Date.now();
    const cooldownMs = 900;
    const lastClickAt = window.__lastKotActionClickAt || 0;

    if ((now - lastClickAt) < cooldownMs) {
        return;
    }

    window.__lastKotActionClickAt = now;
    window.__posKotSubmissionLocked = true;
    window.saveOrder.apply(window, arguments);
};

// Save Order Function
window.saveOrder = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    // Get all action arguments
    const actions = Array.from(arguments).filter(a => a !== null && a !== undefined);
    const action = actions[0] || 'draft';
    const secondAction = actions[1] || null;
    const thirdAction = actions[2] || null;
    const fourthAction = actions[3] || null;
    const isKotAction = action === 'kot';
    let keepActionLockedAfterComplete = false;

    // Always recompute totals from current cart state immediately before building payload.
    // This prevents stale financial fields from being submitted after rapid cart edits.
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }

    // Validate that there are items in the cart
    if (!window.posState.orderItemList || Object.keys(window.posState.orderItemList).length === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: @json(__('messages.noItemsFound')),
                text: @json(__('messages.orderItemRequired')),
                confirmButtonText: @json(__('app.ok')),
                confirmButtonColor: '#3085d6'
            });
        } else {
            alert(@json(__('messages.orderItemRequired')));
        }
        if (isKotAction) {
            window.__posKotSubmissionLocked = false;
        }
        return;
    }

    // Frontend validation: for room service, require a selected stay
    const rawOrderTypeSlug = (window.posState.orderTypeSlug || window.posState.orderType || '').toString();
    const orderTypeSlug = (typeof window.posNormalizeSidebarOrderTypeKey === 'function')
        ? window.posNormalizeSidebarOrderTypeKey(rawOrderTypeSlug)
        : rawOrderTypeSlug;
    if (orderTypeSlug === 'room_service') {
        const stayId = parseInt(window.posState?.selectedStayId || 0, 10);
        if (!stayId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: @json(__('app.error')),
                    text: @json(__('hotel::modules.roomService.selectStayRequired')),
                });
            } else {
                alert(@json(__('hotel::modules.roomService.selectStayRequired')));
            }
            if (isKotAction) {
                window.__posKotSubmissionLocked = false;
            }
            return;
        }
    }

    // Frontend validation: for delivery with internal (default) app, require delivery executive
    if (orderTypeSlug === 'delivery') {
        const selectedDeliveryApp = (window.posState.selectedDeliveryApp ?? 'default') === null
            ? 'default'
            : (window.posState.selectedDeliveryApp || 'default');
        const deliveryExecId = parseInt(window.posState.selectedDeliveryExecutive || 0, 10);

        if (selectedDeliveryApp === 'default' && !deliveryExecId && action !== 'cancel') {
            const message = @json(__('validation.required', ['attribute' => __('modules.delivery.deliveryExecutive')]));
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: @json(__('app.error')),
                    text: message,
                });
            } else {
                alert(message);
            }
            if (isKotAction) {
                window.__posKotSubmissionLocked = false;
            }
            return;
        }
    }

    // Frontend validation: for pickup, only ensure a pickup date & time are chosen.
    // "Future time" and same‑day rules are enforced centrally on the server.
    if (orderTypeSlug === 'pickup' && action !== 'cancel') {
        const pickupDate = (window.posState.pickupDate || '').toString().trim();
        const pickupTime = (window.posState.pickupTime || '').toString().trim();

        if (!pickupDate || !pickupTime) {
            const message = @json(__('validation.required', ['attribute' => __('modules.order.pickUpDateTime')]));
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: @json(__('app.error')),
                    text: message,
                });
            } else {
                alert(message);
            }
            if (isKotAction) {
                window.__posKotSubmissionLocked = false;
            }
            return;
        }
    }

    // Prevent duplicate submissions from rapid double-clicks.
    if (window.__posOrderActionInProgress) {
        return;
    }

    const requiresBillAndPayment = actions.includes('bill');
    if (requiresBillAndPayment && !window.posState.canBillAndPayment) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: @json(__('app.error')),
                text: @json(__('messages.permissionDenied')),
            });
        } else {
            alert(@json(__('messages.permissionDenied')));
        }
        if (isKotAction) {
            window.__posKotSubmissionLocked = false;
        }
        return;
    }

    // Determine button IDs based on action
    let buttonId = '';
    let buttonTextId = '';
    let buttonLoadingId = '';

    const actionKey = actions.join('_');

    switch(actionKey) {
        case 'draft':
            buttonId = 'saveDraftBtn';
            buttonTextId = 'saveDraftBtnText';
            buttonLoadingId = 'saveDraftBtnLoading';
            break;
        case 'kot':
            buttonId = 'saveKotBtn';
            buttonTextId = 'saveKotBtnText';
            buttonLoadingId = 'saveKotBtnLoading';
            break;
        case 'kot_print':
            buttonId = 'saveKotPrintBtn';
            buttonTextId = 'saveKotPrintBtnText';
            buttonLoadingId = 'saveKotPrintBtnLoading';
            break;
        case 'kot_bill_payment_print':
            buttonId = 'saveKotBillPaymentBtn';
            buttonTextId = 'saveKotBillPaymentBtnText';
            buttonLoadingId = 'saveKotBillPaymentBtnLoading';
            break;
        case 'bill':
            buttonId = 'saveBillBtn';
            buttonTextId = 'saveBillBtnText';
            buttonLoadingId = 'saveBillBtnLoading';
            break;
        case 'bill_payment':
            buttonId = 'saveBillPaymentBtn';
            buttonTextId = 'saveBillPaymentBtnText';
            buttonLoadingId = 'saveBillPaymentBtnLoading';
            break;
        case 'bill_print':
            buttonId = 'saveBillPrintBtn';
            buttonTextId = 'saveBillPrintBtnText';
            buttonLoadingId = 'saveBillPrintBtnLoading';
            break;
    }

        window.__setPosOrderActionButtonsDisabled = function(disabled) {
        if (typeof window.setGlobalOrderActionLock === 'function') {
            window.setGlobalOrderActionLock(!!disabled);
            return;
        }
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') return;
        $('.pos-order-action-btn').prop('disabled', !!disabled).toggleClass('opacity-50 pointer-events-none', !!disabled);
        $('.pos-new-kot-link').toggleClass('opacity-50 pointer-events-none', !!disabled).attr('aria-disabled', disabled ? 'true' : 'false');
    };

    window.__togglePosButtonLoading = function(id, textId, loadingId, isLoading) {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined' || !id) return;
        const $btn = $('#' + id);
        if (!$btn.length) return;

        $btn.prop('disabled', !!isLoading).toggleClass('opacity-50', !!isLoading);

        if (textId) {
            const $text = $('#' + textId);
            if ($text.length) $text.toggleClass('hidden', !!isLoading);
        }
        if (loadingId) {
            const $loading = $('#' + loadingId);
            if ($loading.length) $loading.toggleClass('hidden', !isLoading);
        }
    };

    // Show loading state
    if (buttonId) {
        window.__posOrderActionInProgress = true;
        window.__setPosOrderActionButtonsDisabled(true);
        window.__togglePosButtonLoading(buttonId, buttonTextId, buttonLoadingId, true);
    }

    // Prepare order items data
    // API expects: id, variant_id, quantity, price, amount, note, modifier_ids
    const orderItems = [];
    for (const key in window.posState.orderItemList) {
        const item = window.posState.orderItemList[key];
        const qty = window.posState.orderItemQty[key] || 1;
        const variation = window.posState.orderItemVariation[key];
        const modifiers = window.posState.itemModifiersSelected[key] || [];
        const note = window.posState.itemNotes[key] || null;
        const isFreeStampItem = window.isFreeStampItemByMeta(key, item, note);

        // Existing-order KOT generation should include only fresh cart lines.
        // Keys like "kot_<kotId>_<itemId>" represent already-issued KOT items.
        if (action === 'kot' && window.posState.orderID && /^kot_\d+_\d+$/.test(key)) {
            continue;
        }

        // Ensure item and item.id exists
        if (!item) {
            console.error('Item is null or undefined for key:', key);
            continue;
        }

        if (!item.id && item.id !== 0) {
            console.error('Invalid item data - missing id:', item, 'key:', key);
            continue;
        }

        // Ensure id is a number
        const menuItemId = parseInt(item.id);
        if (isNaN(menuItemId) || menuItemId <= 0) {
            console.error('Invalid menu_item_id:', item.id, 'for item:', item);
            continue;
        }

        const basePrice = variation ? (variation.price || item.price) : item.price;
        const modifierPrice = window.posState.orderItemModifiersPrice[key] || 0;
        const itemPrice = basePrice + modifierPrice;
        // IMPORTANT: Prefer stored per-key amount (may include stamp discount / free stamp item = 0)
        const amount = (window.posState.orderItemAmount && typeof window.posState.orderItemAmount[key] !== 'undefined')
            ? window.posState.orderItemAmount[key]
            : (itemPrice * qty);

        // Get tax details for this item if available
        const itemTaxDetails = window.posState.orderItemTaxDetails && window.posState.orderItemTaxDetails[key]
            ? window.posState.orderItemTaxDetails[key]
            : null;

        const orderItem = {
            key: key,
            id: menuItemId,  // API expects 'id' not 'menu_item_id'
            variant_id: variation ? parseInt(variation.id) : 0,  // API expects 'variant_id' not 'menu_item_variation_id'
            quantity: parseInt(qty) || 1,
            price: parseFloat(itemPrice) || 0,
            amount: parseFloat(amount) || 0,
            note: note || null,
            modifier_ids: Array.isArray(modifiers) ? modifiers.map(m => parseInt(m)).filter(m => !isNaN(m)) : [],  // API expects 'modifier_ids' not 'modifier_option_ids'
            tax_amount: itemTaxDetails ? (itemTaxDetails.tax_amount || 0) : 0,
            tax_percentage: itemTaxDetails ? (itemTaxDetails.tax_percent || 0) : 0,
            tax_breakup: itemTaxDetails ? (itemTaxDetails.tax_breakup || null) : null,
            is_free_item_from_stamp: isFreeStampItem,
            stamp_rule_id: item.stamp_rule_id || null
        };

        orderItems.push(orderItem);
    }

    // Validate that we have items
    if (orderItems.length === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: @json(__('modules.order.noItemsInCart')),
                text: @json(__('messages.orderItemRequired')),
                confirmButtonText: @json(__('app.ok')),
                confirmButtonColor: '#3085d6'
            });
        } else {
            alert(@json(__('messages.orderItemRequired')));
        }
        // Hide loading state
        if (buttonId) {
            window.__posOrderActionInProgress = false;
            window.__setPosOrderActionButtonsDisabled(false);
            window.__togglePosButtonLoading(buttonId, buttonTextId, buttonLoadingId, false);
        }
        if (isKotAction) {
            window.__posKotSubmissionLocked = false;
        }
        return;
    }

    // Debug: Log order items before sending
    console.log('Sending order items:', orderItems);

    // Prepare extra charges data
    const extraCharges = [];
    if (window.posConfig && window.posConfig.extraCharges && window.posConfig.extraCharges.length > 0) {
        window.posConfig.extraCharges.forEach(function(charge) {
            extraCharges.push({
                id: charge.id,
                charge_id: charge.id,
                name: charge.name,
                amount: charge.amount,
                type: charge.type,
                is_enabled: charge.is_enabled
            });
        });
    }

    // Prepare taxes data
    const taxes = [];
    if (window.posConfig && window.posConfig.taxes && window.posConfig.taxes.length > 0) {
        window.posConfig.taxes.forEach(function(tax) {
            taxes.push({
                id: tax.id,
                tax_id: tax.id,
                tax_name: tax.tax_name,
                tax_percent: tax.tax_percent
            });
        });
    }

    // Prepare order data
    const effectiveOrderId = (typeof window.getCurrentPosOrderId === 'function')
        ? window.getCurrentPosOrderId()
        : (window.posState.orderID || window.posState.orderDetail?.id || null);
    const orderData = {
        order_id: effectiveOrderId || null,
        order_type: window.posState.orderType || window.posState.orderTypeSlug,
        order_type_id: window.posState.orderTypeId,
        order_type_display: window.posState.orderType,
        table_id: window.posState.tableId,
        customer_id: window.posState.customerId,
        customer: window.posState.customer ? {
            name: window.posState.customer.name,
            phone: window.posState.customer.phone,
            email: window.posState.customer.email
        } : null,
        items: orderItems,
        pax: window.posState.noOfPax || 1,
        waiter_id: window.posState.selectWaiter,
        delivery_executive_id: window.posState.selectedDeliveryExecutive || null,
        delivery_app_id: window.posState.selectedDeliveryApp || null,
        discount_type: window.posState.discountType,
        discount_value: window.posState.discountValue || 0,
        discount_amount: window.posState.discountAmount || 0,
        discount_apply_on: window.posState.discountApplyOn || ((window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }}) ? 'total' : 'sub_total'),
        loyalty_points_redeemed: parseInt(window.posState.loyaltyPointsRedeemed || 0),
        loyalty_discount_amount: parseFloat(window.posState.loyaltyDiscountAmount || 0),
        delivery_fee: window.posState.deliveryFee || 0,
        tip_amount: window.posState.tipAmount || 0,
        order_note: window.posState.orderNote || null,
        sub_total: window.posState.subTotal || 0,
        total: window.posState.total || 0,
        discounted_total: window.posState.discountedTotal || 0,
        total_tax_amount: window.posState.totalTaxAmount || 0,
        tax_base: window.posState.taxBase || 0,
        tax_mode: window.posConfig?.taxMode || @json($taxMode ?? 'order'),
        order_status: window.posState.orderStatus || 'confirmed',
        // For pickup, send combined "<date> <time>" string so the
        // backend can validate and normalize it using the same rules
        // as the Livewire POS component. Fallback to legacy
        // deliveryDateTime when date/time inputs are not available.
        pickup_date: (window.posState.pickupDate && window.posState.pickupTime)
            ? (window.posState.pickupDate + ' ' + window.posState.pickupTime)
            : (window.posState.deliveryDateTime || null),
        pickup_date_only: window.posState.pickupDate || null,
        pickup_time_only: window.posState.pickupTime || null,
        extra_charges: extraCharges,
        taxes: taxes,
        order_item_tax_details: window.posState.orderItemTaxDetails || {},
        actions: actions,
        order_number: window.posState.orderNumber || null,
        formatted_order_number: window.posState.formattedOrderNumber || null,
        orders_to_delete_after_merge: @json(session('pos_merged_orders_to_delete', [])),
        _token: '{{ csrf_token() }}'
    };

    // Attach Hotel room-service context (parity with Livewire Pos.php)
    const rawRoomServiceOrderTypeSlug = (window.posState.orderTypeSlug || window.posState.orderType || '').toString();
    const roomServiceOrderTypeSlug = (typeof window.posNormalizeSidebarOrderTypeKey === 'function')
        ? window.posNormalizeSidebarOrderTypeKey(rawRoomServiceOrderTypeSlug)
        : rawRoomServiceOrderTypeSlug;
    if (roomServiceOrderTypeSlug === 'room_service' && window.posState.selectedStayId) {
        orderData.context_type = 'HOTEL_ROOM';
        orderData.context_id = window.posState.selectedStayId;
        orderData.bill_to = window.posState.billTo || 'POST_TO_ROOM';
    }

    function ajaxPrintOrderWithSettings(orderId) {
        if (typeof window.ajaxPrintOrderById === 'function') {
            window.ajaxPrintOrderById(orderId);
        }
    }

    function __posExtractNumericOrderNumber(value) {
        if (value === null || value === undefined) {
            return null;
        }
        var s = String(value).trim();
        if (!s) {
            return null;
        }
        var m = s.match(/(\d+)(?!.*\d)/);
        if (!m) {
            return null;
        }
        var n = parseInt(m[1], 10);
        return Number.isFinite(n) ? n : null;
    }

    function __posOfflineOpInCurrentBusinessDay(op) {
        var cfg = window.posConfig && window.posConfig.orderNumberFormat;
        if (!cfg || !cfg.resetDaily) {
            return true;
        }
        var bd = window.posConfig && window.posConfig.posBusinessDayUtc;
        if (!bd || !bd.start || !bd.end) {
            return true;
        }
        var t = op && op.createdAt ? new Date(op.createdAt).getTime() : NaN;
        if (!Number.isFinite(t)) {
            return true;
        }
        var s = new Date(bd.start).getTime();
        var e = new Date(bd.end).getTime();
        return t >= s && t <= e;
    }

    function __posNowPartsInRestaurantTz() {
        var tz = (window.posConfig && window.posConfig.restaurantTimezone) || 'UTC';
        var d = new Date();
        var fmt = new Intl.DateTimeFormat('en-CA', {
            timeZone: tz,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
        var parts = fmt.formatToParts(d);
        var map = {};
        parts.forEach(function(p) {
            if (p.type !== 'literal') {
                map[p.type] = p.value;
            }
        });
        var year = map.year || '0000';
        var month = String(map.month || '01').replace(/\D/g, '').padStart(2, '0');
        var day = String(map.day || '01').replace(/\D/g, '').padStart(2, '0');
        var hour = String(map.hour || '00').replace(/\D/g, '').padStart(2, '0');
        var minute = String(map.minute || '00').replace(/\D/g, '').padStart(2, '0');
        return { year: year, month: month, day: day, hi: hour + minute };
    }

    /**
     * Mirror App\Models\Order::buildFormattedOrderNumber (prefix, date parts, padded seq, separator).
     */
    window.__posFormatOrderNumberForDisplay = function(orderNumber) {
        var n = parseInt(orderNumber, 10);
        if (!Number.isFinite(n) || n < 1) {
            n = 1;
        }
        var cfg = window.posConfig && window.posConfig.orderNumberFormat;
        if (!cfg || !cfg.enable) {
            var s = String(n);
            return { order_number: s, formatted_order_number: s };
        }
        var parts = [];
        if (cfg.prefix) {
            parts.push(String(cfg.prefix));
        }
        var dt = __posNowPartsInRestaurantTz();
        if (cfg.includeDate) {
            var dateParts = [];
            if (cfg.showYear) {
                dateParts.push(dt.year);
            }
            if (cfg.showMonth) {
                dateParts.push(dt.month);
            }
            if (cfg.showDay) {
                dateParts.push(dt.day);
            }
            if (dateParts.length) {
                parts.push(dateParts.join(''));
            }
            if (cfg.showTime) {
                parts.push(dt.hi);
            }
        }
        var digits = parseInt(cfg.digits, 10);
        if (!Number.isFinite(digits) || digits < 1) {
            digits = 4;
        }
        var padded = String(n);
        while (padded.length < digits) {
            padded = '0' + padded;
        }
        parts.push(padded);
        var sep = cfg.separator !== undefined && cfg.separator !== null ? String(cfg.separator) : '-';
        var formatted = parts.join(sep);
        return { order_number: String(n), formatted_order_number: formatted };
    };

    /**
     * Highest numeric order label already used in the offline queue (queued saves only).
     * Excludes the current cart display so we can compare server "next" vs queued.
     */
    function __posMaxNumericOrderLabelFromOfflineQueueOnly() {
        var maxKnown = null;
        try {
            var raw = window.localStorage.getItem('pos_blade_offline_queue');
            var q = raw ? JSON.parse(raw) : [];
            if (Array.isArray(q)) {
                q.forEach(function(op) {
                    if (!op || op.type !== 'save_order') {
                        return;
                    }
                    if (!__posOfflineOpInCurrentBusinessDay(op)) {
                        return;
                    }
                    var p = op.payload || {};
                    var n = null;
                    var rawNum = p.order_number;
                    if (rawNum !== undefined && rawNum !== null && rawNum !== '') {
                        var pn = parseInt(rawNum, 10);
                        if (Number.isFinite(pn) && pn > 0) {
                            n = pn;
                        }
                    }
                    if (n === null) {
                        var s =
                            (op.summary && op.summary.order_number_label) ||
                            p.order_number_label ||
                            p.formatted_order_number ||
                            p.order_number;
                        n = __posExtractNumericOrderNumber(s);
                    }
                    if (n !== null && (maxKnown === null || n > maxKnown)) {
                        maxKnown = n;
                    }
                });
            }
        } catch (e) {
            // ignore localStorage parse errors
        }
        return maxKnown;
    }

    function __posMaxNumericOrderLabelInOfflineQueue() {
        var maxKnown = __posMaxNumericOrderLabelFromOfflineQueueOnly();
        var cur = (window.posState.formattedOrderNumber || window.posState.orderNumber || '').toString().trim();
        var nCur = __posExtractNumericOrderNumber(cur);
        if (nCur !== null && (maxKnown === null || nCur > maxKnown)) {
            maxKnown = nCur;
        }
        return maxKnown;
    }

    function __posPlannedOrderSeqStorageKey() {
        if (!window.posConfig || !window.posConfig.branchId) {
            return 'pos_planned_order_seq_0';
        }
        return 'pos_planned_order_seq_' + String(window.posConfig.branchId);
    }

    /**
     * Last known planned order sequence from an online POS load (or after bump). Offline new orders
     * use max(queue, cached, posState) so we continue after ORD-243 instead of resetting to ORD-001.
     */
    window.__posGetCachedPlannedOrderSequence = function() {
        try {
            var raw = window.localStorage.getItem(__posPlannedOrderSeqStorageKey());
            if (!raw) {
                return null;
            }
            var n = parseInt(raw, 10);
            return Number.isFinite(n) && n > 0 ? n : null;
        } catch (e) {
            return null;
        }
    };

    window.__posCachePlannedOrderSequence = function() {
        if (!window.posState || !window.posConfig) {
            return;
        }
        var n = parseInt(window.posState.orderNumber, 10);
        if (!Number.isFinite(n) || n < 1) {
            var ex = __posExtractNumericOrderNumber(
                (window.posState.formattedOrderNumber || '').toString().trim()
            );
            n = ex !== null ? ex : NaN;
        }
        if (!Number.isFinite(n) || n < 1) {
            return;
        }
        try {
            var prev = typeof window.__posGetCachedPlannedOrderSequence === 'function'
                ? window.__posGetCachedPlannedOrderSequence()
                : null;
            if (prev !== null && n < prev) {
                return;
            }
            window.localStorage.setItem(__posPlannedOrderSeqStorageKey(), String(n));
        } catch (e2) {
            // ignore
        }
    };

    function __posApplyNumericToOrderDisplay(nextN) {
        if (!window.posState || !Number.isFinite(nextN)) {
            return;
        }
        var formatted =
            typeof window.__posFormatOrderNumberForDisplay === 'function'
                ? window.__posFormatOrderNumberForDisplay(nextN)
                : { order_number: String(nextN), formatted_order_number: String(nextN) };
        var ord = formatted.order_number != null ? String(formatted.order_number) : String(nextN);
        var disp =
            formatted.formatted_order_number != null && String(formatted.formatted_order_number).trim() !== ''
                ? String(formatted.formatted_order_number)
                : ord;
        window.posState.orderNumber = ord;
        window.posState.formattedOrderNumber = disp;
        document.querySelectorAll('.order-number-value').forEach(function(el) {
            el.textContent = ord;
        });
        document.querySelectorAll('.formatted-order-number-value').forEach(function(el) {
            el.textContent = disp;
        });
        if (typeof window.__posCachePlannedOrderSequence === 'function') {
            window.__posCachePlannedOrderSequence();
        }
    }

    /**
     * Server-generated "next" order # does not account for labels already reserved in the offline queue.
     * For a fresh cart, bump display to max(serverNext, maxQueued + 1, cached planned seq) when numeric labels apply.
     */
    window.__posSyncNewCartOrderNumberAheadOfOfflineQueue = function() {
        if (!window.posState) {
            return;
        }
        var oid = parseInt(
            window.posState.orderID || (window.posState.orderDetail && window.posState.orderDetail.id) || 0,
            10
        );
        if (Number.isFinite(oid) && oid > 0) {
            return;
        }
        if (typeof window.location !== 'undefined' && String(window.location.pathname || '').indexOf('/pos/kot/') !== -1) {
            return;
        }
        var maxQ = __posMaxNumericOrderLabelFromOfflineQueueOnly();
        var fromQueue = maxQ !== null ? maxQ + 1 : 0;
        var cachedPl =
            typeof window.__posGetCachedPlannedOrderSequence === 'function'
                ? window.__posGetCachedPlannedOrderSequence()
                : null;
        var fromCache = Number.isFinite(cachedPl) && cachedPl > 0 ? cachedPl : 0;
        var floor = Math.max(fromQueue, fromCache);
        if (floor < 1 && maxQ === null && fromCache === 0) {
            return;
        }
        var serverN = __posExtractNumericOrderNumber(
            (window.posState.formattedOrderNumber || window.posState.orderNumber || '').toString().trim()
        );
        if (serverN === null) {
            var formattedOrderNode = document.querySelector('.formatted-order-number-value');
            var plainOrderNode = document.querySelector('.order-number-value');
            if (formattedOrderNode && formattedOrderNode.textContent) {
                serverN = __posExtractNumericOrderNumber(formattedOrderNode.textContent.toString().trim());
            } else if (plainOrderNode && plainOrderNode.textContent) {
                serverN = __posExtractNumericOrderNumber(plainOrderNode.textContent.toString().trim());
            }
        }
        var serverNum = Number.isFinite(serverN) && serverN > 0 ? serverN : 0;
        var nextN = Math.max(serverNum, floor);
        if (nextN < 1) {
            return;
        }
        if (serverNum > 0 && nextN === serverNum) {
            return;
        }
        __posApplyNumericToOrderDisplay(nextN);
    };

    function __posGetOfflineQueueSession() {
        try {
            var raw = sessionStorage.getItem('pos_blade_offline_active_session');
            if (!raw) {
                return null;
            }
            var o = JSON.parse(raw);
            return o && typeof o === 'object' ? o : null;
        } catch (e) {
            return null;
        }
    }

    function __posSetOfflineQueueSession(obj) {
        try {
            if (!obj) {
                sessionStorage.removeItem('pos_blade_offline_active_session');
            } else {
                sessionStorage.setItem('pos_blade_offline_active_session', JSON.stringify(obj));
            }
        } catch (e) {
            // ignore quota / private mode
        }
    }

    window.__posClearOfflineQueueSession = function() {
        __posSetOfflineQueueSession(null);
    };

    window.__posSetOfflineQueueSessionForAppend = function(groupKey, orderNumberLabel) {
        var gk = (groupKey || '').toString().trim();
        if (!gk) {
            __posSetOfflineQueueSession(null);
            return;
        }
        var lbl = (orderNumberLabel || '').toString().trim();
        if (!lbl) {
            lbl = gk;
        }
        __posSetOfflineQueueSession({
            groupKey: gk,
            orderNumberLabel: lbl
        });
    };

    function __posActionsIncludeKot(actions) {
        if (!Array.isArray(actions)) {
            return false;
        }
        return actions.some(function(a) {
            var x = String(a || '').toLowerCase();
            return x === 'kot' || x === 'send_to_kitchen';
        });
    }

    window.__posCountQueuedKotsForCurrentOrder = function() {
        var oid = parseInt(window.posState && window.posState.orderID || 0, 10);
        var sess = typeof __posGetOfflineQueueSession === 'function' ? __posGetOfflineQueueSession() : null;
        try {
            var raw = window.localStorage.getItem('pos_blade_offline_queue');
            var q = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(q)) {
                return 0;
            }
            return q.filter(function(op) {
                if (!op || op.type !== 'save_order') {
                    return false;
                }
                var p = op.payload || {};
                var act = Array.isArray(p.actions) ? p.actions : (op.summary && op.summary.actions) || [];
                if (!__posActionsIncludeKot(act)) {
                    return false;
                }
                if (Number.isFinite(oid) && oid > 0) {
                    return parseInt(p.order_id, 10) === oid;
                }
                if (!sess || !sess.groupKey) {
                    return false;
                }
                return String(p.offline_queue_group_key || '') === String(sess.groupKey);
            }).length;
        } catch (e) {
            return 0;
        }
    };

    window.__posServerKotCountFromOrderDetail = function() {
        try {
            var od = window.posState && window.posState.orderDetail;
            if (!od || typeof od !== 'object') {
                return 0;
            }
            if (Array.isArray(od.kot)) {
                return od.kot.length;
            }
            if (Array.isArray(od.kots)) {
                return od.kots.length;
            }
            var n = parseInt(od.kot_count, 10);
            return Number.isFinite(n) && n > 0 ? n : 0;
        } catch (e) {
            return 0;
        }
    };

    window.__posUpdateRunningOrderBanner = function() {
        if (!window.posState) {
            return;
        }
        var el = document.getElementById('pos-running-order-banner');
        var textEl = document.getElementById('pos-running-order-banner-text');
        var bannerReady = !!(el && textEl);

        var oid = parseInt(
            window.posState.orderID || (window.posState.orderDetail && window.posState.orderDetail.id) || 0,
            10
        );
        // Full page load: posState can be empty briefly; URL /pos/kot/{id} always carries the order id.
        var kotPathMatch =
            typeof window.location !== 'undefined' && window.location.pathname
                ? window.location.pathname.match(/^\/pos\/kot\/(\d+)/)
                : null;
        var kotOrderIdFromUrl = kotPathMatch ? parseInt(kotPathMatch[1], 10) : 0;
        if ((!Number.isFinite(oid) || oid <= 0) && Number.isFinite(kotOrderIdFromUrl) && kotOrderIdFromUrl > 0) {
            oid = kotOrderIdFromUrl;
        }
        var offlineMode =
            window.PosOffline &&
            typeof window.PosOffline.shouldQueueNow === 'function' &&
            window.PosOffline.shouldQueueNow();
        var append = !!window.posState.__posOfflineAppendToQueuedOrder;
        var isKotRoute = typeof window.location !== 'undefined' && String(window.location.pathname || '').indexOf('/pos/kot/') !== -1;
        var showOrderDetailKotPage = false;
        try {
            showOrderDetailKotPage = new URLSearchParams(window.location.search || '').get('show-order-detail') === 'true';
        } catch (eSd) {
            showOrderDetailKotPage = false;
        }
        var isKotNewCartRoute = isKotRoute && !showOrderDetailKotPage;
        var sess = typeof __posGetOfflineQueueSession === 'function' ? __posGetOfflineQueueSession() : null;
        var orderLabel =
            (window.posState.formattedOrderNumber || window.posState.orderNumber || '').toString().trim();
        if (!orderLabel && window.posState.orderDetail) {
            orderLabel = (
                window.posState.orderDetail.formatted_order_number ||
                window.posState.orderDetail.order_number ||
                ''
            ).toString().trim();
        }
        if (!orderLabel && isKotRoute) {
            var formattedOrderNode = document.querySelector('.formatted-order-number-value');
            var plainOrderNode = document.querySelector('.order-number-value');
            if (formattedOrderNode && formattedOrderNode.textContent) {
                orderLabel = formattedOrderNode.textContent.toString().trim();
            } else if (plainOrderNode && plainOrderNode.textContent) {
                orderLabel = plainOrderNode.textContent.toString().trim();
            }
        }
        if (!orderLabel && isKotRoute && Number.isFinite(oid) && oid > 0) {
            orderLabel = String(oid);
        }
        var queuedKot =
            typeof window.__posCountQueuedKotsForCurrentOrder === 'function'
                ? window.__posCountQueuedKotsForCurrentOrder()
                : 0;
        var kitchenKot =
            typeof window.__posServerKotCountFromOrderDetail === 'function'
                ? window.__posServerKotCountFromOrderDetail()
                : 0;

        var show = false;
        var title = '';
        var sub = '';
        var orderNumberBadges = document.querySelectorAll('[data-pos-order-number-badge]');

        if (orderNumberBadges && orderNumberBadges.length) {
            orderNumberBadges.forEach(function(node) {
                if (!node) {
                    return;
                }
                if (append || isKotNewCartRoute) {
                    node.classList.add('hidden');
                } else {
                    node.classList.remove('hidden');
                }
            });
        }

        // Show running-order banner for explicit "New KOT" append flow and server KOT route.
        if ((append || isKotRoute) && Number.isFinite(oid) && oid > 0 && orderLabel) {
            show = true;
            title =
                @json(__('messages.posRunningOrderBannerTitle')) +
                ' · ' +
                orderLabel;
            if (isKotRoute && !append) {
                sub = @json(__('messages.posRunningOrderBannerExistingOrder'));
            } else {
                sub =
                    @json(__('messages.posRunningOrderBannerKotsKitchen')) +
                    ': ' +
                    kitchenKot +
                    (offlineMode
                        ? ' · ' + @json(__('messages.posRunningOrderBannerKotsQueued')) + ': ' + queuedKot
                        : '');
            }
        } else if (
            offlineMode &&
            append &&
            sess &&
            sess.orderNumberLabel
        ) {
            show = true;
            title =
                @json(__('messages.posRunningOfflineOrderBannerTitle')) +
                ' · ' +
                sess.orderNumberLabel;
            sub = @json(__('messages.posRunningOrderBannerKotsQueued')) + ': ' + queuedKot;
        }

        if (bannerReady) {
            if (show) {
                el.classList.remove('hidden');
                textEl.textContent = sub ? title + ' · ' + sub : title;
            } else {
                el.classList.add('hidden');
                textEl.textContent = '';
            }
        }

        var hideNonKotCartActions = !!append || isKotNewCartRoute;
        document.querySelectorAll('[data-pos-non-kot-cart-actions]').forEach(function(node) {
            if (!node) {
                return;
            }
            if (hideNonKotCartActions) {
                node.classList.add('hidden');
            } else {
                node.classList.remove('hidden');
            }
        });
    };

    /**
     * Stable order number + group key for multiple offline KOTs on the same order (no server order_id yet).
     * Server-side orders use the live formatted order number from posState.
     * Reuses session only when __posOfflineAppendToQueuedOrder is true (set from offline "New KOT" flow).
     */
    function __posPrepareOfflineQueueMeta(orderData) {
        orderData = orderData || {};
        var oid = parseInt(orderData.order_id || window.posState.orderID || 0, 10);
        if (Number.isFinite(oid) && oid > 0) {
            var lbl = (window.posState.formattedOrderNumber || window.posState.orderNumber || '').toString().trim();
            return {
                order_number_label: lbl || String(oid),
                offline_queue_group_key: null
            };
        }
        var append = !!window.posState.__posOfflineAppendToQueuedOrder;
        if (!append) {
            __posClearOfflineQueueSession();
        }
        var sess = __posGetOfflineQueueSession();
        if (!sess || !sess.groupKey || !sess.orderNumberLabel) {
            var fromStateNum = parseInt(window.posState.orderNumber, 10);
            if (!Number.isFinite(fromStateNum) || fromStateNum < 1) {
                var exNum = __posExtractNumericOrderNumber(
                    (window.posState.formattedOrderNumber || '').toString().trim()
                );
                fromStateNum = exNum !== null && Number.isFinite(exNum) ? exNum : 0;
            }
            var maxQ = __posMaxNumericOrderLabelFromOfflineQueueOnly();
            var fromQueue = maxQ !== null ? maxQ + 1 : 0;
            var cachedPl =
                typeof window.__posGetCachedPlannedOrderSequence === 'function'
                    ? window.__posGetCachedPlannedOrderSequence()
                    : null;
            // Fresh order must move to next number; cached sequence stores last used/planned.
            var fromCache = Number.isFinite(cachedPl) && cachedPl > 0 ? (cachedPl + 1) : 0;
            var numForLabel = Math.max(fromStateNum, fromQueue, fromCache);
            if (numForLabel < 1) {
                numForLabel = 1;
            }
            var applied =
                typeof window.__posFormatOrderNumberForDisplay === 'function'
                    ? window.__posFormatOrderNumberForDisplay(numForLabel)
                    : { order_number: String(numForLabel), formatted_order_number: String(numForLabel) };
            window.posState.orderNumber =
                applied.order_number != null ? String(applied.order_number) : String(numForLabel);
            window.posState.formattedOrderNumber =
                applied.formatted_order_number != null
                    ? String(applied.formatted_order_number)
                    : String(numForLabel);
            document.querySelectorAll('.order-number-value').forEach(function(el) {
                el.textContent = window.posState.orderNumber;
            });
            document.querySelectorAll('.formatted-order-number-value').forEach(function(el) {
                el.textContent = window.posState.formattedOrderNumber;
            });
            if (typeof window.__posCachePlannedOrderSequence === 'function') {
                window.__posCachePlannedOrderSequence();
            }
            var label = (window.posState.formattedOrderNumber || '').toString().trim();
            if (!label) {
                var fb =
                    typeof window.__posFormatOrderNumberForDisplay === 'function'
                        ? window.__posFormatOrderNumberForDisplay(numForLabel)
                        : null;
                label = fb && fb.formatted_order_number ? String(fb.formatted_order_number) : String(numForLabel);
            }
            if (!label) {
                label = 'ORD-' + Date.now().toString(36).toUpperCase();
            }
            sess = {
                groupKey: 'ogk_' + Date.now() + '_' + Math.random().toString(16).slice(2, 10),
                orderNumberLabel: label
            };
            __posSetOfflineQueueSession(sess);
        }
        orderData.offline_queue_group_key = sess.groupKey;
        return {
            order_number_label: sess.orderNumberLabel,
            offline_queue_group_key: sess.groupKey
        };
    }

    window.__posOfflineQueuedBannerHideTimer = null;

    window.__posHideOfflineQueuedBannerSmooth = function() {
        var wrap = document.getElementById('pos-offline-queued-banner-wrap');
        if (!wrap) {
            return;
        }
        wrap.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2');
        wrap.classList.remove('opacity-100', 'translate-y-0');
        wrap.setAttribute('aria-hidden', 'true');
        if (window.__posOfflineQueuedBannerHideTimer) {
            clearTimeout(window.__posOfflineQueuedBannerHideTimer);
            window.__posOfflineQueuedBannerHideTimer = null;
        }
    };

    window.__posShowOfflineQueuedBanner = function(message) {
        var wrap = document.getElementById('pos-offline-queued-banner-wrap');
        var txt = document.getElementById('pos-offline-queued-banner-text');
        if (!wrap || !txt) {
            return;
        }
        txt.textContent = message || '';
        if (window.__posOfflineQueuedBannerHideTimer) {
            clearTimeout(window.__posOfflineQueuedBannerHideTimer);
        }
        wrap.classList.remove('opacity-0', '-translate-y-2');
        wrap.classList.add('opacity-100', 'translate-y-0');
        wrap.setAttribute('aria-hidden', 'false');
        window.__posOfflineQueuedBannerHideTimer = setTimeout(function() {
            window.__posHideOfflineQueuedBannerSmooth();
        }, 7500);
    };

    function queuePosSaveOrderForLaterSync(orderData, orderItems, actions, ui) {
        ui = ui || {};
        if (!window.PosOffline || typeof window.PosOffline.queueSaveOrder !== 'function') {
            return false;
        }
        var offlineMeta = __posPrepareOfflineQueueMeta(orderData);
        if (offlineMeta.offline_queue_group_key) {
            orderData.offline_queue_group_key = offlineMeta.offline_queue_group_key;
        }
        const offlineSummary = {
            order_type: orderData.order_type_display || orderData.order_type || '',
            order_number_label: offlineMeta.order_number_label,
            offline_queue_group_key: offlineMeta.offline_queue_group_key || null,
            table_no: window.posState.tableNo || null,
            items: orderItems.map(function(row) {
                var item = window.posState.orderItemList[row.key];
                var nm = item ? (item.item_name || item.name || ('#' + row.id)) : ('#' + row.id);
                return {
                    name: nm,
                    quantity: row.quantity,
                    price: row.price,
                    amount: row.amount
                };
            }),
            customer: orderData.customer || null,
            total: orderData.total,
            sub_total: orderData.sub_total,
            discount_amount: orderData.discount_amount,
            total_tax_amount: orderData.total_tax_amount,
            delivery_fee: orderData.delivery_fee,
            tip_amount: orderData.tip_amount,
            extra_charges: Array.isArray(orderData.extra_charges) ? orderData.extra_charges : [],
            actions: actions.slice(),
            kot_print_context:
                typeof window.buildPosOfflineKotPrintContext === 'function'
                    ? window.buildPosOfflineKotPrintContext(orderData, orderItems)
                    : null,
            bill_print_context:
                typeof window.buildPosOfflineBillPrintContext === 'function'
                    ? window.buildPosOfflineBillPrintContext(orderData, orderItems)
                    : null,
        };
        window.PosOffline.queueSaveOrder(orderData, offlineSummary);
        window.posState.__posOfflineAppendToQueuedOrder = false;
        if (typeof window.__posUpdateRunningOrderBanner === 'function') {
            window.__posUpdateRunningOrderBanner();
        }
        const wantsOfflineKotPrint =
            actions[0] === 'kot' && actions.indexOf('print') !== -1;
        const wantsOfflineBillPrint =
            actions.indexOf('bill') !== -1 && actions.indexOf('print') !== -1;
        if (
            wantsOfflineKotPrint &&
            offlineSummary.kot_print_context &&
            typeof window.openPosOfflineKotPrintTab === 'function'
        ) {
            window.openPosOfflineKotPrintTab(offlineSummary.kot_print_context);
        }
        if (
            wantsOfflineBillPrint &&
            offlineSummary.bill_print_context &&
            typeof window.openPosOfflineBillPrintTab === 'function'
        ) {
            const delayMs = wantsOfflineKotPrint ? 450 : 0;
            setTimeout(function() {
                window.openPosOfflineBillPrintTab(offlineSummary.bill_print_context);
            }, delayMs);
        }
        const keysToRemove = orderItems.map(function(row) {
            return row.key;
        }).filter(Boolean);
        if (!window.posState.orderID) {
            window.resetPosState();
        } else if (typeof window.removePosCartLinesByKeys === 'function') {
            window.removePosCartLinesByKeys(keysToRemove);
        }
        const queuedMsg = ui.userMessage || @json(__('messages.posOrderQueuedForSync'));
        if (typeof window.__posShowOfflineQueuedBanner === 'function') {
            window.__posShowOfflineQueuedBanner(queuedMsg);
        } else if (typeof window.showToast === 'function') {
            window.showToast('success', queuedMsg);
        } else {
            alert(queuedMsg);
        }
        if (ui.buttonId) {
            window.__posOrderActionInProgress = false;
            window.__setPosOrderActionButtonsDisabled(false);
            window.__togglePosButtonLoading(ui.buttonId, ui.buttonTextId, ui.buttonLoadingId, false);
        }
        if (ui.isKotAction) {
            window.__posKotSubmissionLocked = false;
        }
        const wantsOfflinePayment = actions.indexOf('payment') !== -1;
        if (wantsOfflinePayment && typeof window.openPosOfflinePaymentModal === 'function') {
            setTimeout(function() {
                var oid = orderData.order_id != null ? orderData.order_id : null;
                window.openPosOfflinePaymentModal({
                    order_id: oid,
                    offline_queue_group_key: offlineMeta.offline_queue_group_key || null,
                    due_amount: parseFloat(orderData.total) || 0,
                    formatted_order_number: offlineMeta.order_number_label || orderData.formatted_order_number || ''
                });
            }, 200);
        }
        return true;
    }

    // Offline: queue save (draft, KOT, bill, etc. share the same ajax.pos.save-order endpoint)
    if (window.PosOffline && typeof window.PosOffline.shouldQueueNow === 'function' && window.PosOffline.shouldQueueNow()) {
        queuePosSaveOrderForLaterSync(orderData, orderItems, actions, {
            buttonId: buttonId,
            buttonTextId: buttonTextId,
            buttonLoadingId: buttonLoadingId,
            isKotAction: isKotAction,
            userMessage: @json(__('messages.posOrderQueuedForSync')),
        });
        return;
    }

    // Call API (fallback to jQuery.ajax when easyAjax helper is unavailable)
    const runPosAjax = function(options) {
        if (typeof $.easyAjax === 'function') {
            return $.easyAjax(options);
        }
        return $.ajax({
            url: options.url,
            type: options.type || 'GET',
            data: options.data || {},
            dataType: options.dataType || 'json',
            success: options.success,
            error: options.error
        });
    };
    runPosAjax({
        url: "{{ route('ajax.pos.save-order') }}",
        type: "POST",
        data: orderData,
        success: function(response) {
            if (response.success) {
                // For KOT actions, keep buttons locked to avoid duplicate submits
                // between response and navigation/print completion.
                if (isKotAction) {
                    keepActionLockedAfterComplete = true;
                }
                const orderId = response.order?.id || response.order_id;
                const kotIdsForPrint = (function() {
                    if (Array.isArray(response.kot_ids) && response.kot_ids.length) {
                        return response.kot_ids.map(function(id) { return parseInt(id, 10); }).filter(function(id) { return id > 0; });
                    }
                    if (response.kot && response.kot.id) {
                        var single = parseInt(response.kot.id, 10);
                        return single > 0 ? [single] : [];
                    }
                    return [];
                })();

                // Clear merged orders session data after successful save
                @if(session()->has('pos_merged_orders_to_delete'))
                $.ajax({
                    url: '/ajax/pos/clear-merge-session',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                });
                @endif

                // Handle different actions based on what was clicked
                // Check for payment action (can be secondAction or thirdAction)
                if (secondAction === 'payment' || thirdAction === 'payment') {
                    // Show payment modal using Livewire event
                    if (orderId) {
                        // Use the helper function to show payment modal
                        if (typeof window.showPaymentModalForOrder === 'function') {
                            window.showPaymentModalForOrder(orderId);
                        } else {
                            // Fallback: redirect to payment page
                            window.location.href = "{{ route('orders.show', ['order' => ':id']) }}".replace(':id', orderId) + '?payment=true';
                        }
                    }

                    // Handle print if it's also in the actions (kot,bill,payment,print)
                    if (fourthAction === 'print' || (action === 'kot' && secondAction === 'bill' && thirdAction === 'payment')) {
                        // Print KOT(s) then bill — Livewire-equivalent: printKot($order, null, $kotIds) via ajaxPrintKotForOrder
                        if (orderId) {
                            if (kotIdsForPrint.length && typeof window.ajaxPrintKotForOrder === 'function') {
                                window.ajaxPrintKotForOrder(orderId, kotIdsForPrint, {
                                    onComplete: function() { ajaxPrintOrderWithSettings(orderId); }
                                });
                            } else {
                                if (response.kot && response.kot.id && typeof window.ajaxPrintKotById === 'function') {
                                    window.ajaxPrintKotById(response.kot.id);
                                }
                                ajaxPrintOrderWithSettings(orderId);
                            }
                        }
                    }

                    // Payment flows do not pass through finishKotSaveFlow(), so unlock KOT guard here.
                    if (isKotAction) {
                        window.__posKotSubmissionLocked = false;
                    }

                    // Clear cart after showing payment modal
                    window.resetPosState();
                // KOT + print is handled in the `action === 'kot'` branch so we can finish the print
                // AJAX (and open browser tabs) before redirecting — otherwise navigation aborts the request
                // and shows "Printer Not Connected".
                } else if ((secondAction === 'print' || fourthAction === 'print') && !(action === 'kot' && secondAction === 'print')) {
                    // Print-only flows (match Livewire Pos.php):
                    // - bill + print => order bill only
                    // - kot+bill+payment+print is handled above (payment branch)
                    if (orderId) {
                        const isBillPrintOnly = action === 'bill' && secondAction === 'print';

                        if (isBillPrintOnly) {
                            ajaxPrintOrderWithSettings(orderId);
                        } else if (fourthAction === 'print') {
                            // Defensive: should normally be handled in payment branch
                            if (kotIdsForPrint.length && typeof window.ajaxPrintKotForOrder === 'function') {
                                window.ajaxPrintKotForOrder(orderId, kotIdsForPrint, {
                                    onComplete: function() { ajaxPrintOrderWithSettings(orderId); }
                                });
                            } else {
                                if (response.kot && response.kot.id && typeof window.ajaxPrintKotById === 'function') {
                                    window.ajaxPrintKotById(response.kot.id);
                                }
                                ajaxPrintOrderWithSettings(orderId);
                            }
                        }
                    }

                    if (action === 'bill') {
                        if (typeof window.__posAfterBillSaveSuccess === 'function') {
                            window.__posAfterBillSaveSuccess(response, orderId);
                        } else {
                            window.resetPosState();
                            if (response.message) {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('success', response.message);
                                } else {
                                    alert(response.message);
                                }
                            }
                            if (typeof Livewire !== 'undefined') {
                                Livewire.dispatch('showOrderDetail', { id: orderId, fromPos: true });
                                Livewire.dispatch('refreshPos');
                            } else if (orderId) {
                                window.location.href = "{{ route('orders.show', ['order' => ':id']) }}".replace(':id', orderId);
                            }
                        }
                    } else {
                        window.resetPosState();
                        if (response.message) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('success', response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    }
                } else if (action === 'kot') {
                    // Check if this is a new order creation (fresh KOT)
                    const isNewOrder = !window.posState.orderID;
                    const wantsKotPrint = secondAction === 'print' && orderId && kotIdsForPrint.length > 0;

                    const finishKotSaveFlow = function() {
                        const finalizeKotActionUi = function() {
                            keepActionLockedAfterComplete = false;
                            window.__posOrderActionInProgress = false;
                            window.__setPosOrderActionButtonsDisabled(false);
                            window.__togglePosButtonLoading(buttonId, buttonTextId, buttonLoadingId, false);
                            window.__posKotSubmissionLocked = false;
                        };

                        // For fresh KOT creation, show toast with view order button
                        if (isNewOrder && orderId) {
                            if (typeof Swal !== 'undefined') {
                                window.resetPosState();
                                const kotDetailUrl = "{{ route('pos.kot', ['id' => ':id']) }}?show-order-detail=true".replace(':id', orderId);
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    title: response.message || @json(__('modules.order.kotCreated')),
                                    showConfirmButton: false,
                                    timer: 4000,
                                    timerProgressBar: true,
                                    footer: '<button type="button" class="pos-kot-toast-view-order text-sm font-medium text-skin-base dark:text-skin-base underline underline-offset-2 bg-transparent border-0 cursor-pointer p-0">' + @json(__('modules.order.viewOrder')) + '</button>',
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer);
                                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                                        const viewBtn = toast.querySelector('.pos-kot-toast-view-order');
                                        if (viewBtn) {
                                            viewBtn.addEventListener('click', function(ev) {
                                                ev.preventDefault();
                                                Swal.close();
                                                window.setTimeout(function() {
                                                    try {
                                                        if (typeof Livewire !== 'undefined' && typeof Livewire.navigate === 'function') {
                                                            Livewire.navigate(kotDetailUrl);
                                                            return;
                                                        }
                                                    } catch (eNav) {
                                                        // fall through
                                                    }
                                                    window.location.href = kotDetailUrl;
                                                }, 50);
                                            });
                                        }
                                    }
                                }).then(function() {
                                    finalizeKotActionUi();
                                });
                            } else {
                                if (response.message) alert(response.message);
                                setTimeout(function() {
                                    finalizeKotActionUi();
                                }, 400);
                            }
                            return;
                        }

                        // Show success message for existing order updates
                        if (response.message) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('success', response.message);
                            } else {
                                alert(response.message);
                            }
                        }

                        // Existing order: if this save happened from "new KOT" URL (/pos/kot/{id} without
                        // show-order-detail), redirect to detail view so all KOTs are shown for the order.
                        const isShowOrderDetailUrl = /[?&]show-order-detail=true(?:&|$)/.test((window.location && window.location.href) ? window.location.href.toString() : '');
                        if (orderId && !isShowOrderDetailUrl) {
                            const detailUrl = "{{ route('pos.kot', ['id' => ':id']) }}?show-order-detail=true".replace(':id', orderId);
                            window.location.href = detailUrl;
                            return;
                        }

                        // Existing order: refresh KOT lines in-page (no full navigation).
                        if (orderId) {
                            const pendingKeys = orderItems.map(function(row) {
                                return row.key;
                            }).filter(Boolean);
                            try {
                                $.ajax({
                                    url: '/ajax/pos/orders/' + orderId,
                                    type: 'GET',
                                    async: false,
                                    dataType: 'json',
                                    success: function(fetchRes) {
                                        if (fetchRes && fetchRes.success && fetchRes.order && typeof window.applyPosKotOrderSnapshotFromAjaxOrder === 'function') {
                                            window.applyPosKotOrderSnapshotFromAjaxOrder(fetchRes.order);
                                        }
                                    },
                                });
                            } catch (eSync) {
                                // ignore sync fetch errors; pending lines still cleared below
                            }
                            if (pendingKeys.length && typeof window.removePosCartLinesByKeys === 'function') {
                                window.removePosCartLinesByKeys(pendingKeys);
                            }
                            if (response.kot && response.kot.id) {
                                window.currentKotId = parseInt(response.kot.id, 10);
                            } else if (kotIdsForPrint.length) {
                                window.currentKotId = kotIdsForPrint[kotIdsForPrint.length - 1];
                            }
                            if (response.order && response.order.status === 'kot' && typeof history !== 'undefined' && history.replaceState) {
                                try {
                                    const u = "{{ route('pos.kot', ['id' => ':id']) }}?show-order-detail=true".replace(':id', orderId);
                                    history.replaceState(null, '', u);
                                } catch (eHist) {
                                    // ignore
                                }
                            }
                            if (typeof Livewire !== 'undefined') {
                                try {
                                    Livewire.dispatch('showOrderDetail', { id: orderId, fromPos: true });
                                } catch (eLw) {
                                    // ignore
                                }
                            }
                            // Keep table picker state fresh right after KOT generation, so full tables
                            // appear blocked without requiring a hard reload.
                            try {
                                if (typeof loadAvailableTables === 'function') {
                                    loadAvailableTables();
                                }
                            } catch (eTbl) {
                                // ignore
                            }
                        }
                        finalizeKotActionUi();
                    };

                    // Defer finish until print AJAX + browser tabs complete (see handleAjaxPrintKotResponse)
                    if (wantsKotPrint) {
                        if (orderId && typeof window.ajaxPrintKotForOrder === 'function') {
                            window.ajaxPrintKotForOrder(orderId, kotIdsForPrint, { onComplete: finishKotSaveFlow });
                            return;
                        }
                        if (typeof window.ajaxPrintKotIdsSequential === 'function') {
                            window.ajaxPrintKotIdsSequential(kotIdsForPrint, { onComplete: finishKotSaveFlow, orderId: orderId });
                            return;
                        }
                        if (typeof window.ajaxPrintKotById === 'function' && kotIdsForPrint[0]) {
                            window.ajaxPrintKotById(kotIdsForPrint[0], { onComplete: finishKotSaveFlow });
                            return;
                        }
                    }

                    finishKotSaveFlow();
                } else if (action === 'draft') {
                    // Check if this is a new order creation (fresh draft)
                    const isNewOrder = !window.posState.orderID;

                    // Show success message
                    if (response.message) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        } else {
                            alert(response.message);
                        }
                    }

                    // For fresh draft creation, reload page to reset state
                    if (isNewOrder && orderId) {
                        window.resetPosState();
                        setTimeout(() => window.location.href = "{{ route('pos.index') }}", 500);
                        return;
                    }

                    // For existing draft updates, keep items visible but reset menu selection
                    if (typeof window.updateMenuSelection === 'function') {
                        window.updateMenuSelection(null);
                    }
                    if (window.menuFilters) {
                        window.menuFilters.menuId = null;
                        window.menuFilters.categoryId = null;
                        window.menuFilters.search = '';
                    }
                    $('#menu-filter').val('');
                    $('#category-filter').val('');
                    $('#products-search').val('');
                    if (typeof window.updateCategoryCounts === 'function') {
                        window.updateCategoryCounts();
                    }
                } else if (action === 'bill') {
                    if (typeof window.__posAfterBillSaveSuccess === 'function') {
                        window.__posAfterBillSaveSuccess(response, orderId);
                    } else {
                        if (orderId) {
                            if (typeof Livewire !== 'undefined') {
                                Livewire.dispatch('showOrderDetail', { id: orderId, fromPos: true });
                            } else {
                                window.location.href = "{{ route('orders.show', ['order' => ':id']) }}".replace(':id', orderId);
                            }
                        }
                        window.resetPosState();
                        if (typeof Livewire !== 'undefined') {
                            Livewire.dispatch('refreshPos');
                        }
                        if (response.message) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('success', response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    }
                } else {
                    // Default: show success and reset
                    if (response.message) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        } else {
                            alert(response.message);
                        }
                    }

                    window.resetPosState();
                }
            } else {
                // Show error message
                const errorMsg = response.message || 'Error saving order';
                if (typeof window.showToast === 'function') {
                    window.showToast('error', errorMsg);
                } else {
                    alert(errorMsg);
                }
                if (isKotAction) {
                    window.__posKotSubmissionLocked = false;
                }
            }

            keepActionLockedAfterComplete = false;
            window.__posOrderActionInProgress = false;
            window.__setPosOrderActionButtonsDisabled(false);
            window.__togglePosButtonLoading(buttonId, buttonTextId, buttonLoadingId, false);
        },
        error: function(xhr) {
            const xhrStatus = typeof xhr.status !== 'undefined' ? xhr.status : 0;
            if (xhrStatus === 0 && queuePosSaveOrderForLaterSync(orderData, orderItems, actions, {
                buttonId: buttonId,
                buttonTextId: buttonTextId,
                buttonLoadingId: buttonLoadingId,
                isKotAction: isKotAction,
                userMessage: @json(__('messages.posSaveQueuedNetworkError')),
            })) {
                return;
            }

            let errorMessage = 'Error saving order';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const error = JSON.parse(xhr.responseText);
                    if (error.message) {
                        errorMessage = error.message;
                    }
                } catch (e) {
                    // Use default error message
                }
            }

            // Show error using toast if available
            if (typeof window.showToast === 'function') {
                window.showToast('error', errorMessage);
            } else {
                alert(errorMessage);
            }

            if (isKotAction) {
                window.__posKotSubmissionLocked = false;
            }
        },
        complete: function() {
            // Hide loading state
            if (buttonId) {
                if (keepActionLockedAfterComplete && isKotAction) {
                    // Keep KOT locked after success; redirect/next page resets naturally.
                    window.__posOrderActionInProgress = true;
                    window.__setPosOrderActionButtonsDisabled(true);
                } else {
                    window.__posOrderActionInProgress = false;
                    window.__setPosOrderActionButtonsDisabled(false);
                    window.__togglePosButtonLoading(buttonId, buttonTextId, buttonLoadingId, false);
                }
            }
        }
    });
};

// Update Functions
window.__posIsDineInOrderType = function() {
    const s = window.posState || {};
    return s.orderType === 'dine_in' || s.orderTypeSlug === 'dine_in';
};

window.__posFormatPaxExceedsTableMessage = function(pax, tableCode, remainingSeats) {
    let msg = String(window.__posPaxExceedsTableMsgTpl || '');
    msg = msg.replace(/:pax/g, String(pax));
    msg = msg.replace(/:table/g, String(tableCode || ''));
    msg = msg.replace(/:remaining/g, String(remainingSeats));
    return msg;
};

/**
 * If dine-in with an assigned table, cap pax at seating_capacity and sync inputs.
 * @param {Object} opts Optional. Use opts.silent === true to only adjust state/inputs without toast (e.g. after table change).
 */
window.__posClampPaxToTableCapacity = function(opts) {
    opts = opts || {};
    if (!window.posState || !window.__posIsDineInOrderType()) {
        return;
    }
    const remaining = parseInt(window.posState.tableRemainingSeats, 10);
    const capacity = parseInt(window.posState.tableSeatingCapacity, 10);
    const cap = Number.isFinite(remaining) && remaining > 0
        ? remaining
        : (Number.isFinite(capacity) && capacity > 0 ? capacity : null);
    const tableCode = window.posState.tableNo || '';
    if (!window.posState.tableId || !cap || cap < 1) {
        return;
    }
    let n = parseInt(window.posState.noOfPax, 10) || 1;
    if (n <= cap) {
        return;
    }
    const prev = n;
    n = cap;
    window.posState.noOfPax = n;
    const ids = ['noOfPaxInput', 'no-of-pax'];
    ids.forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.value = n;
        }
    });
    if (!opts.silent && typeof showToast === 'function') {
        showToast('warning', window.__posFormatPaxExceedsTableMessage(prev, tableCode, cap));
    }
};

window.updateNoOfPax = function(value) {
    let n = parseInt(value, 10);
    if (!Number.isFinite(n) || n < 1) {
        n = 1;
    }
    if (window.posState && window.__posIsDineInOrderType() && window.posState.tableId) {
        const remaining = parseInt(window.posState.tableRemainingSeats, 10);
        const capacity = parseInt(window.posState.tableSeatingCapacity, 10);
        const cap = Number.isFinite(remaining) && remaining > 0
            ? remaining
            : (Number.isFinite(capacity) && capacity > 0 ? capacity : null);
        if (cap > 0 && n > cap) {
            n = cap;
            const ids = ['noOfPaxInput', 'no-of-pax'];
            ids.forEach(function(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.value = n;
                }
            });
            if (typeof showToast === 'function') {
                const raw = parseInt(value, 10) || 1;
                showToast('warning', window.__posFormatPaxExceedsTableMessage(raw, window.posState.tableNo || '', cap));
            }
        }
    }
    window.posState.noOfPax = n;
};

window.updateSelectWaiter = function(value) {
    window.posState.selectWaiter = parseInt(value) || null;
};

/**
 * Update waiter selection and persist to backend via API
 */
window.updateWaiterSelection = function(waiterId) {
    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : (window.posState.orderID || window.posState.orderDetail?.id || null);

    // Update local state
    window.posState.selectWaiter = parseInt(waiterId) || null;

    // If no order exists yet, waiter will be set on order creation
    if (!orderId) {
        console.log('No order ID found, waiter will be set on order creation');
        return;
    }

    // Check if jQuery is available
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    // Update via API
    $.easyAjax({
        url: `/ajax/pos/orders/${orderId}/update-waiter`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            waiter_id: waiterId
        },
        success: function(response) {
            if (response.success) {
                if (!window.posState.orderID && orderId) {
                    window.posState.orderID = orderId;
                }
                showToast('success', response.message || @json(__('messages.waiterUpdated')));
            }
        },
        error: function(xhr) {
            console.error('Error updating waiter:', xhr);
            showToast('error', xhr.responseJSON?.message || 'Failed to update waiter');

            // Revert select to previous value on error
            const order = window.posState.orderDetail;
            if (order && order.waiter_id) {
                $('#waiter-select').val(order.waiter_id);
                window.posState.selectWaiter = order.waiter_id;
            }
        }
    });
};

/**
 * Delivery executive: keep posState in sync; persist immediately when an order already exists (Livewire Pos::saveDeliveryExecutive parity).
 */
window.updateDeliveryExecutiveSelection = function(deliveryExecutiveId) {
    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : (window.posState.orderID || window.posState.orderDetail?.id || null);

    const parsed = deliveryExecutiveId === '' || deliveryExecutiveId === null || deliveryExecutiveId === undefined
        ? null
        : (parseInt(String(deliveryExecutiveId), 10) || null);
    window.posState.selectedDeliveryExecutive = parsed;

    if (!orderId) {
        return;
    }

    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    $.easyAjax({
        url: "{{ route('ajax.pos.update-delivery-executive', ['orderId' => '__ORDER_ID__']) }}".replace('__ORDER_ID__', orderId),
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            delivery_executive_id: parsed
        },
        success: function(response) {
            if (response.success) {
                if (!window.posState.orderID && orderId) {
                    window.posState.orderID = orderId;
                }
                if (window.posState.orderDetail) {
                    window.posState.orderDetail.delivery_executive_id = response.delivery_executive_id ?? null;
                }
                if (typeof showToast === 'function') {
                    showToast('success', response.message || @json(__('messages.deliveryExecutiveAssigned')));
                }
            }
        },
        error: function(xhr) {
            console.error('Error updating delivery executive:', xhr);
            if (typeof showToast === 'function') {
                showToast('error', xhr.responseJSON?.message || 'Failed to update delivery executive');
            }
            const order = window.posState.orderDetail;
            const prev = order && order.delivery_executive_id != null ? String(order.delivery_executive_id) : '';
            $('#delivery-executive-select, #selectDeliveryExecutiveInput').val(prev);
            window.posState.selectedDeliveryExecutive = order && order.delivery_executive_id
                ? parseInt(String(order.delivery_executive_id), 10)
                : null;
        }
    });
};

window.updateSelectDeliveryExecutive = function(value) {
    // Determine busy state from the option element (both selects render the same data-* attributes).
    let isBusy = false;
    let executiveName = '';
    try {
        const active = document.activeElement;
        const el =
            (active && (active.id === 'delivery-executive-select' || active.id === 'selectDeliveryExecutiveInput'))
                ? active
                : (document.getElementById('delivery-executive-select') || document.getElementById('selectDeliveryExecutiveInput'));
        const opt = el && el.selectedOptions && el.selectedOptions.length ? el.selectedOptions[0] : null;
        if (opt) {
            isBusy = String(opt.dataset?.busy || '0') === '1';
            executiveName = String(opt.dataset?.name || opt.text || '').trim();
        }
    } catch (e) {
        // ignore
    }

    // Store previous selection (used to revert when user cancels)
    const prev = (window.posState && window.posState.prevDeliveryExecutiveSelection !== undefined)
        ? window.posState.prevDeliveryExecutiveSelection
        : '';

    // If busy, ask confirmation before assigning
    if (value && isBusy && typeof Swal !== 'undefined' && Swal.fire) {
        Swal.fire({
            title: 'Executive is busy',
            text: (executiveName ? (executiveName + ' is already assigned to another order today.') : 'This executive is already assigned to another order today.') + ' Do you want to assign this order anyway?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, assign',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.updateDeliveryExecutiveSelection(value);
            } else {
                if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                    $('#delivery-executive-select, #selectDeliveryExecutiveInput').val(prev);
                }
            }
        });
        return;
    }

    // Fallback confirm if Swal isn't available
    if (value && isBusy) {
        const ok = confirm('This executive is busy (already assigned to another order). Assign anyway?');
        if (!ok) {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                $('#delivery-executive-select, #selectDeliveryExecutiveInput').val(prev);
            }
            return;
        }
    }

    window.updateDeliveryExecutiveSelection(value);
};

// Track previous delivery executive selection so we can revert on cancel.
// (Both selects exist in different partials; keep them in sync.)
try {
    if (!window.posState) window.posState = {};
    document.addEventListener('focusin', function(e) {
        if (!e || !e.target) return;
        if (e.target.id === 'delivery-executive-select' || e.target.id === 'selectDeliveryExecutiveInput') {
            window.posState.prevDeliveryExecutiveSelection = e.target.value || '';
        }
    });
    document.addEventListener('click', function(e) {
        if (!e || !e.target) return;
        if (e.target.id === 'delivery-executive-select' || e.target.id === 'selectDeliveryExecutiveInput') {
            window.posState.prevDeliveryExecutiveSelection = e.target.value || '';
        }
    });
} catch (e) {
    // ignore
}

window.enablePosSearchableSelect = function(selectId, placeholder) {
    const select = document.getElementById(selectId);
    if (!select || select.dataset.posSearchInit === '1') {
        return;
    }

    const options = Array.from(select.options).map(function(opt) {
        return {
            value: String(opt.value ?? ''),
            text: String(opt.textContent || '').trim(),
            disabled: !!opt.disabled
        };
    });
    const placeholderOption = options.find(function(opt) { return opt.value === ''; });
    const defaultLabel = placeholderOption ? placeholderOption.text : (placeholder || 'Select');

    const container = document.createElement('div');
    container.className = 'pos-searchable-select relative w-full min-w-0';
    if (selectId === 'waiter-select' || selectId === 'selectWaiterInput') {
        container.classList.add('sm:max-w-[11rem]');
    }

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'w-full h-8 inline-flex items-center justify-between gap-2 rounded-md border border-gray-300 bg-white px-2.5 text-xs text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200';
    button.innerHTML = '<span class="truncate text-left"></span><svg class="w-3.5 h-3.5 text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>';

    const dropdown = document.createElement('div');
    dropdown.className = 'hidden absolute z-50 mt-1 left-0 right-0 min-w-full rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg';

    const searchWrap = document.createElement('div');
    searchWrap.className = 'p-2 border-b border-gray-100 dark:border-gray-700';
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'h-8 w-full text-xs rounded-md border border-gray-300 bg-white px-2.5 text-gray-700 placeholder:text-gray-400 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:placeholder:text-gray-500 focus:border-skin-base focus:ring-1 focus:ring-skin-base';
    searchInput.placeholder = placeholder || 'Search';
    searchWrap.appendChild(searchInput);

    const list = document.createElement('div');
    list.className = 'max-h-48 overflow-auto p-1';

    const empty = document.createElement('div');
    empty.className = 'hidden px-3 py-4 text-center text-xs text-gray-500 dark:text-gray-400';
    empty.textContent = @json(__('messages.noRecordFound'));

    dropdown.appendChild(searchWrap);
    dropdown.appendChild(list);
    dropdown.appendChild(empty);

    select.parentNode.insertBefore(container, select);
    container.appendChild(button);
    container.appendChild(dropdown);
    container.appendChild(select);
    select.classList.add('hidden');

    const labelEl = button.querySelector('span');
    const updateButtonLabel = function() {
        const selected = options.find(function(opt) { return opt.value === String(select.value || ''); });
        labelEl.textContent = (selected && selected.value !== '') ? selected.text : defaultLabel;
        labelEl.classList.toggle('text-gray-400', !(selected && selected.value !== ''));
    };

    const render = function(term) {
        const q = String(term || '').toLowerCase().trim();
        const currentValue = String(select.value || '');
        list.innerHTML = '';
        let count = 0;

        options.forEach(function(opt) {
            const isPlaceholder = opt.value === '';
            if (q && opt.text.toLowerCase().indexOf(q) === -1) {
                return;
            }
            count++;
            const row = document.createElement('button');
            row.type = 'button';
            row.className = 'w-full text-left px-2.5 py-1.5 rounded-md text-xs hover:bg-gray-100 dark:hover:bg-gray-700 ' + (currentValue === opt.value ? 'bg-blue-50 dark:bg-blue-900/20 text-skin-base' : (isPlaceholder ? 'text-gray-500 dark:text-gray-400 italic' : 'text-gray-700 dark:text-gray-200'));
            row.textContent = isPlaceholder ? ('- ' + opt.text + ' -') : opt.text;
            row.addEventListener('click', function() {
                select.value = opt.value;
                updateButtonLabel();
                select.dispatchEvent(new Event('change', { bubbles: true }));
                dropdown.classList.add('hidden');
            });
            list.appendChild(row);
        });

        empty.classList.toggle('hidden', count > 0);
    };

    button.addEventListener('click', function() {
        const opening = dropdown.classList.contains('hidden');
        document.querySelectorAll('.pos-searchable-select > div.absolute').forEach(function(dd) {
            dd.classList.add('hidden');
        });
        if (opening) {
            dropdown.classList.remove('hidden');
            searchInput.value = '';
            render('');
            setTimeout(function() { searchInput.focus(); }, 20);
        }
    });

    searchInput.addEventListener('input', function() {
        render(searchInput.value);
    });

    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    updateButtonLabel();
    select.dataset.posSearchInit = '1';
};

window.initPosAssigneeSearchableSelects = function() {
    window.enablePosSearchableSelect('waiter-select', @json(__('app.search') . ' ' . __('modules.order.waiter')));
    window.enablePosSearchableSelect('selectWaiterInput', @json(__('app.search') . ' ' . __('modules.order.waiter')));
    window.enablePosSearchableSelect('delivery-executive-select', @json(__('app.search') . ' ' . __('modules.delivery.deliveryExecutive')));
    window.enablePosSearchableSelect('selectDeliveryExecutiveInput', @json(__('app.search') . ' ' . __('modules.delivery.deliveryExecutive')));
};

window.ensurePosNumberInputNoSpinnerCss = function() {
    if (document.getElementById('pos-no-spinner-style')) {
        return;
    }
    var style = document.createElement('style');
    style.id = 'pos-no-spinner-style';
    style.textContent = '.pos-number-no-spinner::-webkit-outer-spin-button,.pos-number-no-spinner::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}.pos-number-no-spinner{-moz-appearance:textfield;appearance:textfield;}';
    document.head.appendChild(style);
};

window.updateDeliveryFee = function(value) {
    window.posState.deliveryFee = parseFloat(value) || 0;
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
};

// Modal Functions
window.showOrderTypeModal = function() {
    // This should open the order type selection modal
    // For now, redirect to order type selection or show modal
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        // Check if modal exists, if not redirect
        if (typeof window.showPosOrderTypeModal === 'function') {
            window.showPosOrderTypeModal();
        } else {
            // Redirect to POS index to select order type
            window.location.href = "{{ route('pos.index') }}";
        }
    }
};

// showAddCustomerModal is defined in layouts/app.blade.php (POS opens client-side first).

// Listen for customer updates from Livewire (with resilient binding)
if (!window.__posCustomerListenersBound) {
    window.__posCustomerListenersBound = false;
}

window.bindPosCustomerListeners = function() {
    if (window.__posCustomerListenersBound) {
        return true;
    }
    const handleCustomerUpdate = (event) => {
        // Support both direct payload and wrapped payload shapes
        const payload = (event && event.customer) ? event : (event && event[0] ? event[0] : null);
        const customer = payload?.customer || null;

        if (customer && customer.id) {
            window.posState.customerId = customer.id;
            window.posState.customer = customer;
            updateCustomerDisplay(customer);
            window.refreshAutoStampPreviews?.();

            // Auto-open loyalty modal when customer is selected and cart already has items
            try {
                const cartSummary = window.getNonFreeCartSummary?.() || {};
                const hasExactlyOneNonFreeQty = (cartSummary.nonFreeQtyTotal || 0) === 1;
                if (
                    hasExactlyOneNonFreeQty &&
                    window.posState.loyaltyEnabled &&
                    (window.posState.loyaltyPointsRedeemed || 0) === 0 &&
                    (window.posState.subTotal || 0) > 0 &&
                    typeof window.openLoyaltyRedemptionModal === 'function'
                ) {
                    window.openLoyaltyRedemptionModal();
                }
            } catch (e) {
                console.warn('Auto loyalty modal on customer select failed:', e);
            }
        }
    };

    if (!window.__posAjaxCustomerListenerBound) {
        window.addEventListener('pos-customer-updated', function (event) {
            handleCustomerUpdate({ customer: event.detail?.customer || null });
        });
        window.__posAjaxCustomerListenerBound = true;
    }

    if (typeof Livewire !== 'undefined' && Livewire.on) {
        Livewire.on('customerSelected', handleCustomerUpdate);
        Livewire.on('customerAdded', handleCustomerUpdate);
    }
    window.__posCustomerListenersBound = true;
    return true;
};

(function ensurePosCustomerListeners() {
    if (window.bindPosCustomerListeners()) {
        return;
    }
    let tries = 0;
    const timer = setInterval(function() {
        tries++;
        if (window.bindPosCustomerListeners() || tries >= 20) {
            clearInterval(timer);
        }
    }, 150);
})();

document.addEventListener('livewire:navigated', function() {
    window.bindPosCustomerListeners?.();
});

window.clearSelectedCustomer = function() {
    if (!window.posState) {
        return;
    }

    window.posState.customerId = null;
    window.posState.customer = null;
    window.resetLoyaltyRedemption?.();
    window.refreshAutoStampPreviews?.();
    updateCustomerDisplay(null);

    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    window.persistPosDraftCart?.();
};

/**
 * Update customer display in all views (kot_items, order_items, order_detail)
 */
function updateCustomerDisplay(customer) {

    // Update all customer display containers
    const customerContainers = document.querySelectorAll('.customer-display-container');

    if (customerContainers.length === 0) {
        console.warn('No customer display containers found on page');
        return;
    }

    customerContainers.forEach((container, index) => {
        const customerInfoSection = container.querySelector('#customer-info-section');
        const addCustomerSection = container.querySelector('#add-customer-section');
        const customerNameElement = container.querySelector('#customer-name');

        if (!customerInfoSection || !addCustomerSection) {
            console.warn(`Customer display sections not found in container ${index}`, container);
            return;
        }

        const hasCustomerIdentity = !!(
            customer &&
            (
                customer.id ||
                (typeof customer.name === 'string' && customer.name.trim() !== '') ||
                (typeof customer.phone === 'string' && customer.phone.trim() !== '') ||
                (typeof customer.email === 'string' && customer.email.trim() !== '')
            )
        );

        if (hasCustomerIdentity) {
            // Update customer name
            if (customerNameElement) {
                const name = customer && typeof customer.name === 'string' ? customer.name.trim() : '';
                const phone = customer && typeof customer.phone === 'string' ? customer.phone.trim() : '';
                const email = customer && typeof customer.email === 'string' ? customer.email.trim() : '';
                customerNameElement.textContent = name || phone || email || '';
            }

            // Update edit button onclick if it exists (always pass order id on Livewire POS — see layouts/app showAddCustomerModal)
            const editButton = customerInfoSection.querySelector('button[onclick*="showAddCustomerModal"]');
            if (editButton) {
                var __cid = (customer && customer.id) ? JSON.stringify(customer.id) : 'null';
                var __oid = (typeof window.getCurrentPosOrderId === 'function') ? window.getCurrentPosOrderId() : null;
                var __oidLit = (__oid === null || __oid === undefined) ? 'null' : String(__oid);
                editButton.setAttribute('onclick', 'window.showAddCustomerModal(' + __cid + ', ' + __oidLit + ', true)');
            }

            // Show customer info, hide add customer section
            customerInfoSection.style.display = 'flex';
            addCustomerSection.style.display = 'none';

            console.log(`Updated customer display in container ${index}: ${customer.name}`);
        } else {
            // Hide customer info, show add customer section
            customerInfoSection.style.display = 'none';
            addCustomerSection.style.display = 'block';

            console.log(`Cleared customer display in container ${index}`);
        }
    });

    if (typeof window.updateLoyaltyRedeemButtonVisibility === 'function') {
        window.updateLoyaltyRedeemButtonVisibility();
    }
}

/**
 * Update table display in all views (kot_items, order_items, order_detail)
 */
function updateTableDisplay(table) {
    console.log('Updating table display:', table);

    // Update all table display containers
    const tableContainers = document.querySelectorAll('.table-display-container');

    if (tableContainers.length === 0) {
        console.warn('No table display containers found on page');
        return;
    }

    tableContainers.forEach((container, index) => {
        const tableInfoSection = container.querySelector('#table-info-section');
        const setTableSection = container.querySelector('#set-table-section');
        const tableCodeElement = container.querySelector('#table-code');

        if (!tableInfoSection || !setTableSection) {
            console.warn(`Table display sections not found in container ${index}`, container);
            return;
        }

        if (table && table.id && table.table_code) {
            // Update table code
            if (tableCodeElement) {
                tableCodeElement.textContent = table.table_code;
            }

            // Show table info, hide set table button
            tableInfoSection.style.display = 'flex';
            setTableSection.style.display = 'none';

            console.log(`Updated table display in container ${index}: ${table.table_code}`);
        } else {
            // Hide table info, show set table button
            tableInfoSection.style.display = 'none';
            setTableSection.style.display = 'inline-flex';

            console.log(`Cleared table display in container ${index}`);
        }
    });

}


window.showKotNoteModal = function(itemKey = null, currentNote = '') {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        if (itemKey) {
            // Item-specific note
            window.posState.pendingNoteItemKey = itemKey;
            $('#itemNoteInput').val(currentNote);
            $('#itemNoteModal').show();
        } else {
            // Order note
            $('#orderNote').val(window.posState.orderNote || '');
            $('#kotNoteModal').show();
        }
    }
};

window.showAddDiscountModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        // Block manual discount when loyalty discount is active (mirror SaaS / Livewire behavior)
        if ((window.posState.loyaltyDiscountAmount || 0) > 0) {
            window.showToast?.('error', @json(__('Loyalty discount already applied.')));
            return;
        }

        const currentType = window.posState.discountType || '';
        const currentValue = parseFloat(window.posState.discountValue || 0);
        const isTaxInclusive = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }});
        const defaultApplyOn = isTaxInclusive ? 'total' : 'sub_total';
        const currentApplyOn = (window.posState.discountApplyOn || defaultApplyOn).toString();
        const hasCurrentDiscount = currentValue > 0;
        const applyOnWrapper = document.getElementById('discountApplyOnWrapper');

        // Default to percent mode for preset discount selection.
        $('#discountType').val(hasCurrentDiscount ? (currentType || 'percent') : 'percent');
        $('#discountValue').val(hasCurrentDiscount ? currentValue : '5');
        if (!isTaxInclusive) {
            // For exclusive tax, discount is always applied on subtotal.
            window.posState.discountApplyOn = 'sub_total';
            $('#discountApplyOn').val('sub_total');
        } else {
            $('#discountApplyOn').val(['sub_total', 'total'].includes(currentApplyOn) ? currentApplyOn : defaultApplyOn);
        }
        if (applyOnWrapper) {
            applyOnWrapper.style.display = isTaxInclusive ? '' : 'none';
        }
        $('#discountValueError').hide().text('');
        if (typeof window.syncDiscountPresetSelection === 'function') {
            window.syncDiscountPresetSelection();
        }
        if (typeof window.updateDiscountModalPreview === 'function') {
            window.updateDiscountModalPreview();
        }
        $('#discountModal').show();
    }
};

window.syncDiscountPresetSelection = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    const selectedType = $('#discountType').val();
    const selectedValue = parseFloat($('#discountValue').val() || 0);
    const isPercent = selectedType === 'percent';
    const percentValue = isPercent ? selectedValue : 0;
    const presets = [5, 10, 20, 30, 40, 50];

    $('.discount-preset-btn').each(function() {
        const btnValue = parseFloat($(this).data('discountPercent'));
        const isSelected = isPercent && presets.includes(percentValue) && btnValue === percentValue;
        $(this).toggleClass('bg-skin-base text-white border-skin-base', isSelected);
    });
};

window.updateDiscountModalPreview = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined' || !window.posState) {
        return;
    }

    const formatMoney = function(v) {
        if (typeof window.formatCurrency === 'function') {
            return window.formatCurrency(v);
        }
        return '$' + Number(v || 0).toFixed(2);
    };

    const inputValue = parseFloat($('#discountValue').val() || 0);
    const discountType = ($('#discountType').val() || 'percent').toString();
    const isTaxInclusive = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }});
    const defaultApplyOn = isTaxInclusive ? 'total' : 'sub_total';
    const discountApplyOn = isTaxInclusive
        ? ($('#discountApplyOn').val() || defaultApplyOn).toString()
        : 'sub_total';

    const currentDiscountAmount = parseFloat(window.posState.discountAmount || 0);
    const currentApplyOn = ((window.posState.discountApplyOn || defaultApplyOn) + '').toLowerCase();

    // Undo current applied discount so modal preview starts from current pre-discount numbers.
    // subTotal in POS state is already the pre-discount subtotal.
    // Do not add current discount again, otherwise preview base inflates.
    const baseSubTotal = Math.max(0, parseFloat(window.posState.subTotal || 0));
    const baseTotal = Math.max(0, parseFloat(window.posState.total || 0) + currentDiscountAmount);

    const baseForDiscount = discountApplyOn === 'sub_total' ? baseSubTotal : baseTotal;
    let previewDiscount = 0;
    if (inputValue > 0) {
        if (discountType === 'percent') {
            previewDiscount = (baseForDiscount * inputValue) / 100;
        } else {
            previewDiscount = Math.min(inputValue, baseForDiscount);
        }
    }
    previewDiscount = Math.max(0, previewDiscount);

    // Keep subtotal preview as the original pre-discount subtotal.
    // Discount impact is represented separately in Discount/Total rows.
    const previewSubTotal = baseSubTotal;
    const previewTotal = Math.max(0, baseTotal - previewDiscount);

    $('#discountPreviewSubTotal').text(formatMoney(previewSubTotal));
    $('#discountPreviewAmount').text('-' + formatMoney(previewDiscount));
    $('#discountPreviewTotal').text(formatMoney(previewTotal));
};

window.removeCurrentDiscount = function() {
    window.posState.discountType = null;
    window.posState.discountValue = null;
    window.posState.discountAmount = 0;
    window.posState.discountApplyOn = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }})
        ? 'total'
        : 'sub_total';
    window.__posSkipCustomerDisplayUpdateOnce = true;
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
};

/**
 * Update order detail panel totals from discount API response (subtotal, discount, tax, total) without reload.
 */
window.updateOrderDetailTotalsFromResponse = function(order, discountInfo) {
    if (!order) return;
    var subEl = document.getElementById('order-detail-subtotal') || document.getElementById('subtotal-display');
    var discountRow = document.getElementById('discount-row');
    var discountTypeDisplay = document.getElementById('discount-type-display');
    var discountAmountEl = document.getElementById('discount-display');
    var taxableAmountEl = document.getElementById('taxable-amount-display');
    var taxEl = document.getElementById('order-detail-total-tax') || document.getElementById('total-tax-display');
    var totalEl = document.getElementById('order-detail-total') || document.getElementById('total-display');

    if (subEl && order.sub_total_formatted !== undefined) subEl.textContent = order.sub_total_formatted;

    var resolvedDiscountType = null;
    var resolvedDiscountValue = null;
    if (discountInfo && discountInfo.type) {
        resolvedDiscountType = discountInfo.type;
        resolvedDiscountValue = discountInfo.value;
    } else if (order.discount_type) {
        resolvedDiscountType = order.discount_type;
        resolvedDiscountValue = order.discount_value;
    }

    if (discountRow) {
        var hasDiscount = typeof window.posOrderDiscountIsApplied === 'function'
            ? window.posOrderDiscountIsApplied(order.discount_amount)
            : parseFloat(order.discount_amount || 0) > 0;
        discountRow.style.display = hasDiscount ? 'flex' : 'none';

        if (discountTypeDisplay && resolvedDiscountType && hasDiscount) {
            var typeLabel = '';
            if (resolvedDiscountType === 'percent') typeLabel = '(' + (resolvedDiscountValue || '') + '%)';
            discountTypeDisplay.textContent = typeLabel;
        } else if (discountTypeDisplay) {
            discountTypeDisplay.textContent = '';
        }

        if (discountAmountEl) discountAmountEl.textContent = hasDiscount ? '-' + (order.discount_amount_formatted || '0') : '';
    }

    if (taxEl && order.total_tax_amount_formatted !== undefined) taxEl.textContent = order.total_tax_amount_formatted;
    if (totalEl && order.total_formatted !== undefined) totalEl.textContent = order.total_formatted;
    if (taxableAmountEl && typeof window.formatCurrency === 'function') {
        var taxableAmount = Math.max(0, (parseFloat(order.sub_total || 0) - parseFloat(order.discount_amount || 0)));
        taxableAmountEl.textContent = window.formatCurrency(taxableAmount);
    }

    // Update per-tax rows (order-level tax mode) when tax base changes
    var taxBaseNumber = parseFloat(order.tax_base || 0);
    if (!isNaN(taxBaseNumber)) {
        document.querySelectorAll('#order-items-container [data-tax-percent]').forEach(function(taxRow) {
            var taxPercent = parseFloat(taxRow.getAttribute('data-tax-percent') || 0);
            if (isNaN(taxPercent)) return;
            var amountEl = taxRow.querySelector('.tax-amount-display');
            if (!amountEl || typeof window.formatCurrency !== 'function') return;
            var taxAmount = (taxPercent / 100) * taxBaseNumber;
            amountEl.textContent = window.formatCurrency(taxAmount);
        });
    }

    if (window.posState) {
        window.posState.subTotal = parseFloat(order.sub_total) || 0;
        window.posState.discountAmount = parseFloat(order.discount_amount) || 0;
        window.posState.totalTaxAmount = parseFloat(order.total_tax_amount) || 0;
        window.posState.total = parseFloat(order.total) || 0;
        window.posState.discountApplyOn = order.discount_apply_on || ((window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }}) ? 'total' : 'sub_total');
        if (order.tax_base !== undefined) {
            window.posState.taxBase = parseFloat(order.tax_base) || 0;
        }
        if (resolvedDiscountType) {
            window.posState.discountType = resolvedDiscountType;
            window.posState.discountValue = resolvedDiscountValue;
        } else {
            window.posState.discountType = null;
            window.posState.discountValue = null;
        }
    }
};

window.removeExtraCharge = function(chargeId, orderType) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    // Check if user has permission
    @if (!user_can('Update Order'))
        alert(@json(__('messages.permissionDenied')));
        return;
    @endif

    const doRemove = function() {
        const orderId = typeof window.getCurrentPosOrderId === 'function'
            ? window.getCurrentPosOrderId()
            : null;

        // If there's an existing order (order detail view), update via API and reload to show new totals
        if (orderId && window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true')) {
            const removeChargeUrl = "{{ route('ajax.pos.remove-extra-charge', ['orderId' => 0, 'chargeId' => 0]) }}".replace(/\/orders\/0\/remove-charge\/0/, '/orders/' + orderId + '/remove-charge/' + chargeId);
            $.easyAjax({
                url: removeChargeUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_type: orderType
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', response.message || @json(__('messages.extraChargeRemoved')));
                        }
                        // Keep billing payload in sync: removed charge must not be posted again.
                        if (window.posConfig && Array.isArray(window.posConfig.extraCharges)) {
                            window.posConfig.extraCharges = window.posConfig.extraCharges.filter(function(charge) {
                                return String(charge.id) !== String(chargeId);
                            });
                        }
                        // Remove charge row - try both selectors for compatibility
                        var row = document.querySelector('[data-order-detail-charge-id="' + chargeId + '"]');
                        if (!row) {
                            row = document.querySelector('[data-charge-id="' + chargeId + '"]');
                        }
                        if (row) row.remove();

                        if (response.order && typeof window.updateOrderDetailTotalsFromResponse === 'function') {
                            window.updateOrderDetailTotalsFromResponse(response.order, null);
                        }
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : @json(__('messages.somethingWentWrong'));
                    if (typeof showToast === 'function') {
                        showToast('error', error);
                    } else {
                        alert(error);
                    }
                }
            });
            return;
        }

        // For new orders, remove from posConfig.extraCharges array
        if (window.posConfig && window.posConfig.extraCharges) {
            const chargeIndex = window.posConfig.extraCharges.findIndex(c => c.id == chargeId);

            if (chargeIndex > -1) {
                const removedCharge = window.posConfig.extraCharges[chargeIndex];

                // Remove from array
                window.posConfig.extraCharges.splice(chargeIndex, 1);

                console.log('Removed extra charge:', removedCharge.charge_name);

                // Remove the DOM element immediately for better UX
                $(`#extra-charges-container [data-charge-id="${chargeId}"]`).fadeOut(200, function() {
                    $(this).remove();

                    // Hide container if no more charges
                    if (window.posConfig.extraCharges.length === 0) {
                        $('#extra-charges-container').hide();
                    }
                });

                // Recalculate totals
                if (typeof window.calculateTotal === 'function') {
                    window.calculateTotal();
                }

                // Show success message
                if (typeof window.showToast === 'function') {
                    window.showToast('success', @json(__('messages.extraChargeRemoved')));
                }
            } else {
                console.warn('Extra charge not found:', chargeId);
            }
        }
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: @json(__('messages.removeExtraCharge') . '?'),
            text: @json(__('messages.removeExtraChargeMessage')),
            showCancelButton: true,
            confirmButtonText: @json(__('modules.order.remove')),
        }).then((result) => {
            if (result.isConfirmed) {
                doRemove();
            }
        });
    } else {
        // Fallback to native confirm
        if (confirm(@json(__('messages.removeExtraCharge')))) {
            doRemove();
        }
    }
};

window.__clearPosCartStateNow = function() {
    window.posState.orderItemList = {};
    window.posState.orderItemQty = {};
    window.posState.orderItemAmount = {};
    window.posState.orderItemVariation = {};
    window.posState.itemModifiersSelected = {};
    window.posState.orderItemModifiersPrice = {};
    window.posState.itemNotes = {};
    window.posState.orderItemTaxDetails = {};
    window.posState.subTotal = 0;
    window.posState.total = 0;
    window.posState.discountAmount = 0;
    window.posState.discountType = null;
    window.posState.discountValue = null;
    window.posState.discountApplyOn = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }})
        ? 'total'
        : 'sub_total';
    window.posState.discountedTotal = 0;
    window.posState.taxBase = 0;
    window.posState.deliveryFee = 0;
    window.posState.tipAmount = 0;
    window.posState.totalTaxAmount = 0;
    window.posState.orderNote = null;
    window.posState.loyaltyPointsRedeemed = 0;
    window.posState.loyaltyDiscountAmount = 0;
    window.posState.stampDiscountAmount = 0;
    window.posState.hasFreeStampItems = false;
    window.currentKotId = null;
    window.clearPersistedPosDraftCart?.();

    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }

    if (typeof window.flushCustomerDisplayUpdate === 'function') {
        window.flushCustomerDisplayUpdate();
    }
};

// Clear cart lines/totals while keeping selected order context (table/customer/order type).
window.clearPosCartOnly = function(anchorEl) {
    const proceed = function() {
        window.__clearPosCartStateNow?.();
    };

    if (typeof window.openPosSimpleConfirm === 'function') {
        window.openPosSimpleConfirm(@json(__('messages.posClearCartConfirm')), proceed, { anchorEl: anchorEl });
    } else if (window.confirm(@json(__('messages.posClearCartConfirm')))) {
        proceed();
    }
};

function __posShouldShowFreshNewOrderEmptyHint() {
    if (!window.posState) {
        return false;
    }
    if (window.posState.__posOfflineAppendToQueuedOrder) {
        return false;
    }
    var path = typeof window.location !== 'undefined' ? String(window.location.pathname || '') : '';
    if (path.indexOf('/pos/kot/') !== -1) {
        return false;
    }
    var oid = parseInt(
        window.posState.orderID || (window.posState.orderDetail && window.posState.orderDetail.id) || 0,
        10
    );
    return !(Number.isFinite(oid) && oid > 0);
}

window.__posBuildEmptyCartPlaceholderHtml = function() {
    var showHint = __posShouldShowFreshNewOrderEmptyHint();
    var title = @json(__('messages.posEmptyCartNoItems'));
    var hint = @json(__('messages.posEmptyCartNewOrderHint'));
    var hintHtml = showHint
        ? '<p class="mt-2 max-w-sm mx-auto text-sm leading-snug text-gray-500 dark:text-gray-400 px-2">' + hint + '</p>'
        : '';
    return (
        '<div class="h-full min-h-[18rem] flex items-center justify-center text-center text-gray-500 dark:text-gray-400">' +
            '<div class="flex flex-col items-center justify-center">' +
                '<svg class="w-12 h-12 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />' +
                '</svg>' +
                '<div class="text-gray-500 dark:text-gray-400 text-base">' + title + '</div>' +
                hintHtml +
            '</div>' +
        '</div>'
    );
};

window.__posBuildOrderPanelLoaderHtml = function() {
    var loadingText = @json(__('app.loading'));
    return (
        '<div class="h-full min-h-[18rem] flex items-center justify-center">' +
            '<div class="inline-flex items-center gap-3 text-gray-600 dark:text-gray-300">' +
                '<svg class="animate-spin h-5 w-5 text-skin-base" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>' +
                '</svg>' +
                '<span class="text-sm font-medium">' + loadingText + '...</span>' +
            '</div>' +
        '</div>'
    );
};

// Reset POS State Function
// options.skipMenuReload: when true (e.g. "New order"), do not refetch the product grid / category UI on the
// left — only cart + order panel state; the right panel is refreshed via refreshOrderPanelsFromServer in startNewOrder.
window.resetPosState = function(options) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    var opts = options && typeof options === 'object' ? options : {};
    var skipMenuReload = opts.skipMenuReload === true;

    // Clear cart
    window.posState.orderItemList = {};
    window.posState.orderItemQty = {};
    window.posState.orderItemAmount = {};
    window.posState.orderItemVariation = {};
    window.posState.itemModifiersSelected = {};
    window.posState.orderItemModifiersPrice = {};
    window.posState.itemNotes = {};
    window.posState.subTotal = 0;
    window.posState.total = 0;
    window.posState.discountAmount = 0;
    window.posState.discountType = null;
    window.posState.discountValue = null;
    window.posState.discountApplyOn = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }})
        ? 'total'
        : 'sub_total';
    window.posState.discountedTotal = 0;
    window.posState.taxBase = 0;
    window.posState.deliveryFee = 0;
    window.posState.tipAmount = 0;
    window.posState.totalTaxAmount = 0;
    window.posState.orderItemTaxDetails = {};
    window.posState.customerId = null;
    window.posState.customer = null;
    window.clearPersistedPosDraftCart?.();
    window.posState.orderNote = null;
    window.posState.orderID = null;
    window.posState.orderDetail = null;
    window.posState.showOrderDetail = false;
    window.posState.orderNumber = '';
    window.posState.formattedOrderNumber = '';
    window.posState.orderStatus = 'confirmed';
    window.posState.__posOfflineAppendToQueuedOrder = false;
    // Keep currently selected order type context on "New Order" (do not force modal/default type).
    window.posState.orderTypeId = window.posState.orderTypeId || null;
    window.posState.orderTypeSlug = window.posState.orderTypeSlug || '';
    window.posState.orderType = window.posState.orderType || '';
    window.posState.selectedDeliveryApp = window.posState.selectedDeliveryApp ?? null;
    window.posState.selectedDeliveryExecutive = window.posState.selectedDeliveryExecutive ?? null;
    window.posState.deliveryDate = window.posState.deliveryDate || '';
    window.posState.deliveryTime = window.posState.deliveryTime || '';
    window.posState.deliveryDateTime = window.posState.deliveryDateTime || '';
    // Reset loyalty points and stamps context for new KOT / fresh cart
    window.posState.loyaltyPointsRedeemed = 0;
    window.posState.loyaltyDiscountAmount = 0;
    window.posState.stampDiscountAmount = 0;
    window.posState.hasFreeStampItems = false;
    window.currentKotId = null;

    if (!skipMenuReload) {
        // Reset menu filters (left column)
        if (window.menuFilters) {
            window.menuFilters.menuId = null;
            window.menuFilters.categoryId = null;
            window.menuFilters.search = '';
            // Keep the original limit value
            if (!window.menuFilters.originalLimit) {
                window.menuFilters.originalLimit = window.menuFilters.limit || 75;
            }
            window.menuFilters.limit = window.menuFilters.originalLimit;
        } else {
            // Initialize menuFilters if it doesn't exist
            window.menuFilters = {
                menuId: null,
                categoryId: null,
                search: '',
                limit: 75
            };
        }

        // Clear menu selection UI
        if (typeof window.updateMenuSelection === 'function') {
            window.updateMenuSelection(null);
        }

        // Clear category selection
        $('#category-filter').val('');

        // Clear search input
        $('#products-search').val('');
    }

    if (typeof window.updateCustomerDisplay === 'function') {
        window.updateCustomerDisplay(null);
    } else if (typeof updateCustomerDisplay === 'function') {
        updateCustomerDisplay(null);
    }
    if (typeof window.flushCustomerDisplayUpdate === 'function') {
        window.flushCustomerDisplayUpdate();
    }
    if (typeof window.updatePosOrderTypeUiLabels === 'function') {
        window.updatePosOrderTypeUiLabels();
    }

    // Force clear the order items container immediately - try multiple selectors
    let $orderItemsContainer = $('#order-items-container .flex.flex-col.rounded.gap-1');

    if ($orderItemsContainer.length === 0) {
        // Try alternative selectors - look inside the scrollable area
        const $scrollArea = $('#order-items-container .flex-1.overflow-y-auto, #order-items-container .overflow-y-auto');
        if ($scrollArea.length > 0) {
            $orderItemsContainer = $scrollArea.find('div.flex.flex-col.rounded.gap-1');
        }
    }

    if ($orderItemsContainer.length === 0) {
        // Try finding directly in order-items-container
        $orderItemsContainer = $('#order-items-container').find('div.flex.flex-col.rounded.gap-1');
    }

    // Clear the container if found
    if ($orderItemsContainer.length > 0) {
        var emptyCartHtml =
            typeof window.__posBuildEmptyCartPlaceholderHtml === 'function'
                ? window.__posBuildEmptyCartPlaceholderHtml()
                : '';
        $orderItemsContainer.html(emptyCartHtml);
    } else {
        // If container not found, try to clear all items with data-item-key attribute
        $('#order-items-container [data-item-key]').remove();
    }

    // Update UI - use setTimeout to ensure state is cleared first
    setTimeout(function() {
        if (typeof window.updateOrderItemsContainer === 'function') {
            window.updateOrderItemsContainer();
        }
        if (typeof window.calculateTotal === 'function') {
            window.calculateTotal();
        }
        if (typeof window.updateTotalsDisplay === 'function') {
            window.updateTotalsDisplay();
        }
    }, 50);

    if (!skipMenuReload) {
        // Reload menu items via AJAX to show all items
        if (typeof window.loadMenuItems === 'function') {
            window.loadMenuItems();
        }

        // Update category counts
        if (typeof window.updateCategoryCounts === 'function') {
            window.updateCategoryCounts();
        }
    }

    // Dispatch reset event if Livewire is available (full reset only; "New order" keeps left column unchanged)
    if (!skipMenuReload && typeof Livewire !== 'undefined') {
        Livewire.dispatch('resetPos');
    }
};

// Public action for starting a fresh order from the POS UI.
window.startNewOrder = function() {
    window.__posForceFreshOrder = true;
    window.__posInitialServerOrderId = null;
    const targetUrl = @json(route('pos.index'));
    const preservedOrderType = {
        orderTypeId: window.posState ? window.posState.orderTypeId : null,
        orderTypeSlug: window.posState ? window.posState.orderTypeSlug : '',
        selectedDeliveryApp: window.posState ? (window.posState.selectedDeliveryApp ?? null) : null,
        selectedDeliveryExecutive: window.posState ? (window.posState.selectedDeliveryExecutive ?? null) : null,
        deliveryDate: window.posState ? (window.posState.deliveryDate || '') : '',
        deliveryTime: window.posState ? (window.posState.deliveryTime || '') : '',
        deliveryDateTime: window.posState ? (window.posState.deliveryDateTime || '') : ''
    };

    if (typeof window.__posClearOfflineQueueSession === 'function') {
        window.__posClearOfflineQueueSession();
    }

    // Keep SPA behavior (no full app reload), but move URL to /pos first so
    // empty-cart hint logic treats this as a fresh order page immediately.
    if (window.history && typeof window.history.replaceState === 'function') {
        try {
            const parsedTargetUrl = new URL(targetUrl, window.location.origin);
            const nextPath = parsedTargetUrl.pathname + parsedTargetUrl.search + parsedTargetUrl.hash;
            window.history.replaceState(window.history.state || {}, document.title, nextPath);
        } catch (e) {
            // ignore URL parsing errors and continue with state reset
        }
    }

    if (typeof window.resetPosState === 'function') {
        window.resetPosState({ skipMenuReload: true });
    }

    // Immediately hide stale order-detail controls/content while fresh panel loads.
    const orderPanel = document.getElementById('order-items-container');
    if (orderPanel) {
        orderPanel.innerHTML = window.__posBuildOrderPanelLoaderHtml
            ? window.__posBuildOrderPanelLoaderHtml()
            : '<div class="h-full min-h-[18rem] flex items-center justify-center text-gray-500">@lang("app.loading")...</div>';
    }

    // Render totals/cart state instantly in current DOM (if relevant nodes still exist).
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }

    // Pull full fresh-order panel from /pos (no hard reload).
    if (typeof window.refreshOrderPanelsFromServer === 'function') {
        window.refreshOrderPanelsFromServer({
            url: targetUrl,
            onSuccess: function() {
                if (!window.posState) {
                    return;
                }
                // Re-apply preserved order type after panel refresh so UI and validation
                // stay in sync (prevents showing Dine In while logic still uses Delivery).
                if (preservedOrderType.orderTypeId && preservedOrderType.orderTypeSlug) {
                    if (typeof window.applyPosOrderTypeSelection === 'function') {
                        window.applyPosOrderTypeSelection(
                            preservedOrderType.orderTypeId,
                            preservedOrderType.orderTypeSlug,
                            preservedOrderType.selectedDeliveryApp
                        );
                    } else {
                        window.posState.orderTypeId = preservedOrderType.orderTypeId;
                        window.posState.orderTypeSlug = preservedOrderType.orderTypeSlug;
                    }
                }

                window.posState.selectedDeliveryExecutive = preservedOrderType.selectedDeliveryExecutive;
                window.posState.deliveryDate = preservedOrderType.deliveryDate;
                window.posState.deliveryTime = preservedOrderType.deliveryTime;
                window.posState.deliveryDateTime = preservedOrderType.deliveryDateTime;

                if (typeof window.updatePosOrderTypeUiLabels === 'function') {
                    window.updatePosOrderTypeUiLabels();
                }
                if (typeof window.calculateTotal === 'function') {
                    window.calculateTotal();
                }
            }
        });
    } else if (preservedOrderType.orderTypeId && preservedOrderType.orderTypeSlug) {
        if (typeof window.applyPosOrderTypeSelection === 'function') {
            window.applyPosOrderTypeSelection(
                preservedOrderType.orderTypeId,
                preservedOrderType.orderTypeSlug,
                preservedOrderType.selectedDeliveryApp
            );
        }
    }
};

/**
 * Remove specific cart line keys (used after offline KOT/draft queue on an existing order).
 */
window.removePosCartLinesByKeys = function(keys) {
    if (!keys || !keys.length || !window.posState) {
        return;
    }
    keys.forEach(function(key) {
        if (!key) {
            return;
        }
        if (window.posState.orderItemList) {
            delete window.posState.orderItemList[key];
        }
        if (window.posState.orderItemQty) {
            delete window.posState.orderItemQty[key];
        }
        if (window.posState.orderItemVariation) {
            delete window.posState.orderItemVariation[key];
        }
        if (window.posState.orderItemAmount) {
            delete window.posState.orderItemAmount[key];
        }
        if (window.posState.itemModifiersSelected) {
            delete window.posState.itemModifiersSelected[key];
        }
        if (window.posState.orderItemModifiersPrice) {
            delete window.posState.orderItemModifiersPrice[key];
        }
        if (window.posState.itemNotes) {
            delete window.posState.itemNotes[key];
        }
        if (window.posState.orderItemTaxDetails) {
            delete window.posState.orderItemTaxDetails[key];
        }
        if (window.posState.orderItemIds) {
            delete window.posState.orderItemIds[key];
        }
    });
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }
};

// Toast notification helper
window.showToast = function(type, message) {
    // Use SweetAlert if available
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type === 'error' ? 'error' : 'success',
            title: type === 'error' ? 'Error' : 'Success',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    } else {
        // Fallback to alert
        alert(message);
    }
};

// Save Functions
window.saveKotNote = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        const note = $('#orderNote').val();
        window.posState.orderNote = note;

        const orderId = typeof window.getCurrentPosOrderId === 'function'
            ? window.getCurrentPosOrderId()
            : null;
        const showOrderDetail = !!(window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true'));

        // If we are editing an existing server-backed order (order detail/KOT view), persist note immediately.
        if (orderId && showOrderDetail) {
            const updateNoteUrl = "{{ route('ajax.pos.update-order-note', ['orderId' => 0]) }}".replace(/\/orders\/0\/update-note/, '/orders/' + orderId + '/update-note');
            const runPosAjax = function(options) {
                if (typeof $.easyAjax === 'function') {
                    return $.easyAjax(options);
                }
                return $.ajax({
                    url: options.url,
                    type: options.type || 'GET',
                    data: options.data || {},
                    dataType: options.dataType || 'json',
                    success: options.success,
                    error: options.error
                });
            };

            runPosAjax({
                url: updateNoteUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_note: note
                },
                success: function(response) {
                    if (response && response.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message || @json(__('messages.updatedSuccessfully')));
                        }
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast('error', (response && response.message) ? response.message : 'Failed to save note');
                        }
                    }
                },
                error: function() {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', 'Failed to save note');
                    }
                }
            });
        }

        if (typeof window.closeKotNoteModal === 'function') {
            window.closeKotNoteModal();
        }
    }
};

window.saveItemNote = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    const note = ($('#itemNoteInput').val() || '').toString();

    // If opened by a Livewire/Alpine component (e.g. x-pos.item-note), use callback and let Livewire persist it
    if (typeof window.__posItemNoteSave === 'function') {
        try {
            window.__posItemNoteSave(note);
        } finally {
            if (typeof window.__posItemNoteAfterSave === 'function') {
                window.__posItemNoteAfterSave(note);
            }
            window.__posItemNoteSave = null;
            window.__posItemNoteAfterSave = null;
        }

        if (typeof window.closeItemNoteModal === 'function') {
            window.closeItemNoteModal();
        } else {
            $('#itemNoteModal').hide();
        }
        return;
    }

    // Otherwise, fallback to client-side POS state (used by JS cart mode)
    const itemKey = window.posState.pendingNoteItemKey;
    if (!itemKey) {
        $('#itemNoteError').text('Item not found').show();
        return;
    }

    window.posState.itemNotes = window.posState.itemNotes || {};
    window.posState.itemNotes[itemKey] = note;

    if (typeof window.closeItemNoteModal === 'function') {
        window.closeItemNoteModal();
    } else {
        $('#itemNoteModal').hide();
    }

    // Update UI
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
};

/**
 * Open the POS item note modal with custom save handler (used by Livewire/Alpine item note component)
 */
window.openPosItemNoteModal = function(currentNote, onSave, onAfterSave) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    window.__posItemNoteSave = typeof onSave === 'function' ? onSave : null;
    window.__posItemNoteAfterSave = typeof onAfterSave === 'function' ? onAfterSave : null;
    $('#itemNoteInput').val((currentNote || '').toString());
    $('#itemNoteError').hide();
    $('#itemNoteModal').show();
};

window.saveDiscount = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    const discountValue = parseFloat($('#discountValue').val()) || 0;
    const discountType = $('#discountType').val();
    const isTaxInclusive = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }});
    const defaultApplyOn = isTaxInclusive ? 'total' : 'sub_total';
    const discountApplyOn = isTaxInclusive
        ? ($('#discountApplyOn').val() || defaultApplyOn).toString()
        : 'sub_total';

    if (discountValue <= 0) {
        $('#discountValueError').text('Discount value must be positive').show();
        return;
    }

    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : null;
    const showOrderDetail = !!(window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true'));

    // For server-backed order detail/KOT pages, persist discount via API and only patch totals.
    if (orderId && showOrderDetail) {
        const updateDiscountUrl = "{{ route('ajax.pos.update-order-discount', ['orderId' => 0]) }}".replace(/\/orders\/0\/update-discount/, '/orders/' + orderId + '/update-discount');
        const runPosAjax = function(options) {
            if (typeof $.easyAjax === 'function') {
                return $.easyAjax(options);
            }
            return $.ajax({
                url: options.url,
                type: options.type || 'GET',
                data: options.data || {},
                dataType: options.dataType || 'json',
                success: options.success,
                error: options.error
            });
        };
        runPosAjax({
            url: updateDiscountUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                discount_type: discountType,
                discount_value: discountValue
            },
            success: function(response) {
                if (response.success) {
                    if (response.order && typeof window.updateOrderDetailTotalsFromResponse === 'function') {
                        window.updateOrderDetailTotalsFromResponse(response.order, {
                            type: discountType,
                            value: discountValue
                        });
                    }
                    if (typeof window.closeDiscountModal === 'function') {
                        window.closeDiscountModal();
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', response.message || @json(__('messages.discountApplied')));
                    }
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : @json(__('messages.somethingWentWrong'));
                $('#discountValueError').text(msg).show();
            }
        });
        return;
    }

    window.posState.discountValue = discountValue;
    window.posState.discountType = discountType;
    window.posState.discountApplyOn = ['sub_total', 'total'].includes(discountApplyOn) ? discountApplyOn : defaultApplyOn;
    // Recomputed by calculateTotal using selected discount base.
    window.posState.discountAmount = 0;

    window.__posSkipCustomerDisplayUpdateOnce = true;
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.closeDiscountModal === 'function') {
        window.closeDiscountModal();
    }
    // Keep discount/tax UI fully local; avoid server refresh until action buttons save.
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
};

if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
    $(document).on('click', '.discount-preset-btn', function() {
        const percent = parseFloat($(this).data('discountPercent') || 0);
        if (percent <= 0) {
            return;
        }

        $('#discountType').val('percent');
        $('#discountValue').val(percent);
        $('#discountValueError').hide().text('');
        if (typeof window.syncDiscountPresetSelection === 'function') {
            window.syncDiscountPresetSelection();
        }
        if (typeof window.updateDiscountModalPreview === 'function') {
            window.updateDiscountModalPreview();
        }
    });

    $(document).on('change keyup input', '#discountType, #discountValue, #discountApplyOn', function() {
        if (typeof window.syncDiscountPresetSelection === 'function') {
            window.syncDiscountPresetSelection();
        }
        if (typeof window.updateDiscountModalPreview === 'function') {
            window.updateDiscountModalPreview();
        }
    });
}

// Load Functions
window.loadExtraCharges = function(orderTypeSlug) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    $.easyAjax({
        url: "{{ route('ajax.pos.extra-charges', ['orderType' => ':orderType']) }}".replace(':orderType', orderTypeSlug),
        type: "GET",
        success: function(response) {
            // Store in posConfig to match updateTotalsDisplay expectations
            if (!window.posConfig) {
                window.posConfig = {};
            }
            window.posConfig.extraCharges = response;

            // Recalculate totals which will update the display
            if (typeof window.calculateTotal === 'function') {
                window.calculateTotal();
            }
        }
    });
};

window.loadOrderItems = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    $.easyAjax({
        url: "{{ route('pos.index') }}",
        type: "GET",
        data: {
            orderTypeId: window.posState.orderTypeId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (typeof window.cleanupPosPortaledTooltips === 'function') {
                window.cleanupPosPortaledTooltips();
            }
            // Update order items container using jQuery
            $('#order-items-container').html($(response).find('#order-items-container').html());
            if (typeof window.initPosAssigneeSearchableSelects === 'function') {
                window.initPosAssigneeSearchableSelects();
            }
            if (typeof window.resetPosOrderPanelScroll === 'function') {
                window.resetPosOrderPanelScroll();
            }
            if (typeof window.setGlobalOrderActionLock === 'function' && (window.__posOrderActionInProgress || window.__posPrintOrderInProgress)) {
                window.setGlobalOrderActionLock(true);
            }
            if (typeof window.__posSyncNewCartOrderNumberAheadOfOfflineQueue === 'function') {
                window.__posSyncNewCartOrderNumberAheadOfOfflineQueue();
            }
            if (typeof window.__posUpdateRunningOrderBanner === 'function') {
                window.__posUpdateRunningOrderBanner();
            }
        }
    });
};

window.refreshOrderPanelsFromServer = function(options = {}) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    if (window.__refreshOrderPanelsInProgress) {
        return;
    }

    const requestUrl = options.url || window.location.href;
    window.__refreshOrderPanelsInProgress = true;

    $.ajax({
        url: requestUrl,
        method: 'GET',
        cache: false,
        dataType: 'html',
        success: function(html) {
            const $newPanel = $(html).find('#order-items-container');
            if ($newPanel.length) {
                if (typeof window.cleanupPosPortaledTooltips === 'function') {
                    window.cleanupPosPortaledTooltips();
                }
                $('#order-items-container').html($newPanel.html());
                if (typeof window.initPosAssigneeSearchableSelects === 'function') {
                    window.initPosAssigneeSearchableSelects();
                }
                if (typeof window.resetPosOrderPanelScroll === 'function') {
                    window.resetPosOrderPanelScroll();
                }
                if (typeof window.syncPosNavOffset === 'function') {
                    window.syncPosNavOffset();
                }
                if (typeof window.setGlobalOrderActionLock === 'function' && (window.__posOrderActionInProgress || window.__posPrintOrderInProgress)) {
                    window.setGlobalOrderActionLock(true);
                }
                // Keep order_items totals in sync after partial refresh.
                if (document.getElementById('subtotal-display') && typeof window.calculateTotal === 'function') {
                    window.calculateTotal();
                }
                if (typeof window.__posSyncNewCartOrderNumberAheadOfOfflineQueue === 'function') {
                    window.__posSyncNewCartOrderNumberAheadOfOfflineQueue();
                }
                if (typeof window.__posUpdateRunningOrderBanner === 'function') {
                    window.__posUpdateRunningOrderBanner();
                }
                if (typeof window.updateOrderItemsContainer === 'function') {
                    window.updateOrderItemsContainer();
                }
                if (typeof options.onSuccess === 'function') {
                    options.onSuccess();
                }
                return;
            }

            if (typeof options.onError === 'function') {
                options.onError();
            }
        },
        error: function(xhr) {
            console.error('POS panel refresh failed', xhr?.status, xhr?.statusText);
            if (typeof options.onError === 'function') {
                options.onError();
            }
        },
        complete: function() {
            window.__refreshOrderPanelsInProgress = false;
        }
    });
};

window.resetPosOrderPanelScroll = function() {
    const panel = document.getElementById('order-items-container');
    if (!panel) {
        return;
    }

    panel.scrollTop = 0;
    const scrollAreas = panel.querySelectorAll('.overflow-y-auto');
    scrollAreas.forEach(function(area) {
        area.scrollTop = 0;
    });
};

window.removeItemKeyFromPosState = function(itemKey) {
    if (!itemKey || !window.posState) {
        return;
    }

    const cleanKey = String(itemKey).replace(/"/g, '');
    const props = [
        'orderItemList',
        'orderItemQty',
        'orderItemAmount',
        'orderItemVariation',
        'itemModifiersSelected',
        'orderItemModifiersPrice',
        'itemNotes',
        'orderItemTaxDetails'
    ];

    props.forEach(function(prop) {
        if (window.posState[prop] && Object.prototype.hasOwnProperty.call(window.posState[prop], cleanKey)) {
            delete window.posState[prop][cleanKey];
        }
    });
};

window.removeOrderItemFromPosStateById = function(itemId) {
    if (!window.posState || !window.posState.orderItemList) {
        return;
    }

    const targetId = String(itemId);
    Object.keys(window.posState.orderItemList).forEach(function(key) {
        if (key === `order_item_${targetId}` || key.endsWith(`_${targetId}`)) {
            window.removeItemKeyFromPosState(key);
        }
    });
};

// Calculate Total - mirrors PHP calculateTotal() logic
window.calculateTotal = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.warn('jQuery not available yet for calculateTotal');
        return;
    }

    const normalizeTaxesList = function(rawTaxes) {
        if (!rawTaxes) {
            return [];
        }
        if (Array.isArray(rawTaxes)) {
            return rawTaxes;
        }
        if (typeof rawTaxes === 'object') {
            return Object.values(rawTaxes).filter(function(tax) {
                return tax && typeof tax === 'object';
            });
        }
        return [];
    };

    const normalizeTaxBreakup = function(rawBreakup) {
        const normalized = {};
        if (!rawBreakup) {
            return normalized;
        }

        // Format A: { "GST": {percent/rate, amount}, ... }
        if (!Array.isArray(rawBreakup) && typeof rawBreakup === 'object') {
            Object.keys(rawBreakup).forEach(function(key) {
                const taxInfo = rawBreakup[key] || {};
                const taxName = taxInfo.tax_name || taxInfo.name || key;
                const taxPercent = Number(taxInfo.percent ?? taxInfo.rate ?? taxInfo.tax_percent ?? 0);
                const taxAmount = Number(taxInfo.amount ?? 0);
                if (taxName) {
                    normalized[taxName] = {
                        percent: taxPercent,
                        amount: taxAmount
                    };
                }
            });
            return normalized;
        }

        // Format B: [{name/tax_name, percent/rate, amount}, ...]
        if (Array.isArray(rawBreakup)) {
            rawBreakup.forEach(function(taxInfo) {
                if (!taxInfo || typeof taxInfo !== 'object') {
                    return;
                }
                const taxName = taxInfo.tax_name || taxInfo.name;
                if (!taxName) {
                    return;
                }
                normalized[taxName] = {
                    percent: Number(taxInfo.percent ?? taxInfo.rate ?? taxInfo.tax_percent ?? 0),
                    amount: Number(taxInfo.amount ?? 0)
                };
            });
        }

        return normalized;
    };

    let total = 0;
    let subTotal = 0;
    let totalTaxAmount = 0;
    let orderItemTaxDetails = {};

    // If cart is empty, reset status (including tax base so customer display clears GST lines)
    if (!window.posState.orderItemList || Object.keys(window.posState.orderItemList).length === 0) {
        window.posState.total = 0;
        window.posState.subTotal = 0;
        window.posState.discountedTotal = 0;
        window.posState.taxBase = 0;
        window.posState.totalTaxAmount = 0;
        window.posState.orderItemTaxDetails = {};
        window.posState.totalsPreCalculated = false;
        if (typeof window.updateTotalsDisplay === 'function') {
            window.updateTotalsDisplay();
        }
        if (window.__posSkipCustomerDisplayUpdateOnce) {
            window.__posSkipCustomerDisplayUpdateOnce = false;
        } else if (typeof window.flushCustomerDisplayUpdate === 'function') {
            window.flushCustomerDisplayUpdate();
        } else if (typeof updateCustomerDisplayCache === 'function') {
            updateCustomerDisplayCache();
        }
        window.persistPosDraftCart?.();
        return;
    }

    // Get configuration
    const taxMode = window.posConfig?.taxMode || @json($taxMode ?? 'order');
    const isInclusive = window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }};
    const taxes = window.posConfig?.taxes || [];

    if (taxMode === 'item' && window.posState.orderItemAmount) {
        // Calculate tax details for each item
        // For client-side added items, apply all taxes from config
        for (const key in window.posState.orderItemAmount) {
            const item = window.posState.orderItemList[key];
            if (!item) continue;

            // Skip free stamp items completely from tax calculations (tt Livewire parity)
            const itemNote = window.posState.itemNotes?.[key] || '';
            if (window.isFreeStampItemByMeta && window.isFreeStampItemByMeta(key, item, itemNote)) {
                continue;
            }

            const qty = window.posState.orderItemQty[key] || 1;
            const basePrice = window.posState.orderItemVariation[key]?.price || item.price || 0;
            const modifierPrice = window.posState.orderItemModifiersPrice[key] || 0;
            const itemPriceWithModifiers = basePrice + modifierPrice;
            const lineAmount = parseFloat(window.posState.orderItemAmount[key] || 0);
            // tt parity: compute taxes on effective discounted per-unit amount.
            const discountedPerUnit = qty > 0 ? (lineAmount / qty) : itemPriceWithModifiers;

            // Item tax mode must use item-specific taxes only.
            // If item has no tax assignment, do not apply any tax.
            const normalizedItemTaxes = normalizeTaxesList(item.taxes);
            const itemTaxes = (taxMode === 'item')
                ? (normalizedItemTaxes.length > 0 ? normalizedItemTaxes : [])
                : taxes;
            let itemTaxAmount = 0;
            let itemTaxPercent = 0;
            let taxBreakup = {};

            if (itemTaxes.length > 0) {
                // Calculate total tax percent first (needed for inclusive tax calculation)
                const totalTaxPercent = itemTaxes.reduce((sum, tax) => sum + parseFloat(tax.tax_percent || 0), 0);

                for (const tax of itemTaxes) {
                    const taxPercent = parseFloat(tax.tax_percent || 0);

                    let taxAmount = 0;
                    if (isInclusive) {
                        // Tax is included in price: tax = (price * rate) / (100 + total_rate)
                        taxAmount = (discountedPerUnit * taxPercent) / (100 + totalTaxPercent);
                    } else {
                        // Tax is added to price: tax = price * rate / 100
                        taxAmount = (discountedPerUnit * taxPercent) / 100;
                    }

                    itemTaxAmount += taxAmount;
                    itemTaxPercent += taxPercent;
                    taxBreakup[tax.tax_name] = {
                        percent: taxPercent,
                        amount: taxAmount
                    };
                }
            }

            // Keep persisted tax details for old/existing orders when item tax relation is unavailable on client.
            if (itemTaxes.length === 0 && window.posState.orderItemTaxDetails && window.posState.orderItemTaxDetails[key]) {
                const persistedTax = window.posState.orderItemTaxDetails[key];
                const persistedBreakup = normalizeTaxBreakup(persistedTax.tax_breakup);

                if (Object.keys(persistedBreakup).length > 0) {
                    let persistedTaxAmount = Number(persistedTax.tax_amount ?? persistedTax.total_tax ?? 0);
                    if (!(persistedTaxAmount > 0)) {
                        Object.keys(persistedBreakup).forEach(function(taxName) {
                            persistedTaxAmount += Number(persistedBreakup[taxName].amount || 0) * qty;
                        });
                    }

                    orderItemTaxDetails[key] = {
                        tax_amount: persistedTaxAmount,
                        tax_percent: Number(persistedTax.tax_percent || 0),
                        tax_breakup: persistedBreakup,
                        base_price: itemPriceWithModifiers,
                        discounted_price: discountedPerUnit,
                        qty: qty
                    };
                    continue;
                }
            }

            orderItemTaxDetails[key] = {
                tax_amount: itemTaxAmount * qty,
                tax_percent: itemTaxPercent,
                tax_breakup: taxBreakup,
                base_price: itemPriceWithModifiers,
                discounted_price: discountedPerUnit,
                qty: qty
            };
        }
    }

    // Calculate totals from item amounts
    for (const key in window.posState.orderItemAmount) {
        const item = window.posState.orderItemList?.[key] || {};
        const itemNote = window.posState.itemNotes?.[key] || '';

        // Skip free stamp items from subtotal/total/tax base (tt Livewire parity)
        if (window.isFreeStampItemByMeta && window.isFreeStampItemByMeta(key, item, itemNote)) {
            continue;
        }

        const value = parseFloat(window.posState.orderItemAmount[key] || 0);
        total += value;

        // For inclusive taxes, subtract tax from subtotal
        if (taxMode === 'item' && orderItemTaxDetails[key]) {
            const taxDetail = orderItemTaxDetails[key];
            if (isInclusive) {
                // For inclusive tax: subtotal = item amount - tax amount
                subTotal += (value - (taxDetail.tax_amount || 0));
            } else {
                // For exclusive tax: subtotal = item amount
                subTotal += value;
            }
        } else {
            // No item taxes or order-level taxes
            subTotal += value;
        }
    }

    window.posState.subTotal = subTotal;
    window.posState.orderItemTaxDetails = orderItemTaxDetails;

    // Re-cap loyalty when subtotal changes (e.g. stamp free item lowers subtotal after points were redeemed).
    if (typeof window.clampLoyaltyRedemptionToSubtotal === 'function') {
        window.clampLoyaltyRedemptionToSubtotal(subTotal);
    }

    // Use subtotal as the discount/total base so final math follows:
    // taxable = subtotal - discount.
    total = subTotal;
    let discountedTotal = total;

    // Apply discounts
    const discountValue = parseFloat(window.posState.discountValue || 0);
    const discountType = window.posState.discountType;

    // Loyalty redemption (when applied, regular discount is disabled)
    const loyaltyPointsRedeemed = parseInt(window.posState.loyaltyPointsRedeemed || 0);
    const loyaltyDiscountAmount = parseFloat(window.posState.loyaltyDiscountAmount || 0);

    const discountApplyOn = ((window.posState.discountApplyOn || ((window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }}) ? 'total' : 'sub_total')) + '').toLowerCase() === 'total'
        ? 'total'
        : 'sub_total';
    const discountBase = subTotal;
    if (discountValue > 0 && discountType) {
        if (discountApplyOn === 'sub_total') {
            if (discountType === 'percent') {
                window.posState.discountAmount = Math.round((discountBase * discountValue) / 100 * 100) / 100;
            } else if (discountType === 'fixed') {
                window.posState.discountAmount = Math.min(discountValue, discountBase);
            }
            total -= window.posState.discountAmount;
        } else {
            // For total-level discount, apply after full total is computed (tax/charges/tip/delivery included).
            window.posState.discountAmount = 0;
        }
    } else {
        window.posState.discountAmount = 0;
    }

    // Apply loyalty discount AFTER regular discount calculation.
    // This mirrors SaaS behavior: if loyalty is redeemed, discount_type/value/amount are cleared.
    if (loyaltyPointsRedeemed > 0 && loyaltyDiscountAmount > 0) {
        window.posState.discountType = '';
        window.posState.discountValue = 0;
        window.posState.discountAmount = 0;
        window.posState.discountApplyOn = (window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }})
            ? 'total'
            : 'sub_total';
        total -= loyaltyDiscountAmount;
    }

    // Ensure total does not go negative
    total = Math.max(0, total);

    discountedTotal = total;
    window.posState.discountedTotal = discountedTotal;

    // Step 2: Calculate service charges on discountedTotal
    let serviceTotal = 0;
    const extraCharges = window.posConfig?.extraCharges || [];

    // Detect "fresh KOT" context on JS POS:
    // Only skip charges when:
    // - URL path looks like /pos/kot/{id} AND
    // - show-order-detail=true is NOT present in URL AND
    // - showOrderDetail flag in state is NOT true
    const path = (window.location && window.location.pathname) ? window.location.pathname.toString() : '';
    const href = (window.location && window.location.href) ? window.location.href.toString() : '';
    const urlShowOrderDetail = /[?&]show-order-detail=true(?:&|$)/.test(href);
    const showOrderDetailFlag = !!(window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true'));
    const effectiveShowOrderDetail = showOrderDetailFlag || urlShowOrderDetail;
    const isFreshKotContext =
        !effectiveShowOrderDetail &&
        /\/pos\/kot\/\d+/.test(path);

    // IMPORTANT:
    // For fresh/new KOT creation screen we should not apply UI-only extra charges again,
    // otherwise charges are effectively added twice (once on original bill, once on new KOT view).
    const shouldApplyCharges =
        !isFreshKotContext &&
        extraCharges &&
        extraCharges.length > 0 &&
        Object.keys(window.posState.orderItemAmount).length > 0;

    const currentOrderTypeSlug = String(window.posState.orderType || '')
        .trim()
        .toLowerCase()
        .replace(/\s+/g, '_');

    const normalizeOrderTypes = function(rawTypes) {
        let list = [];
        if (Array.isArray(rawTypes)) {
            list = rawTypes;
        } else if (typeof rawTypes === 'string') {
            const s = rawTypes.trim();
            if (!s) {
                return [];
            }
            try {
                const parsed = JSON.parse(s);
                if (Array.isArray(parsed)) {
                    list = parsed;
                } else {
                    list = s.split(',');
                }
            } catch (e) {
                list = s.split(',');
            }
        } else if (rawTypes && typeof rawTypes === 'object') {
            list = Object.values(rawTypes);
        }

        return list
            .map(function(v) {
                return String(v || '').trim().toLowerCase().replace(/\s+/g, '_');
            })
            .filter(Boolean);
    };

    const isChargeApplicableForOrderType = function(charge, orderTypeSlug) {
        const allowedTypes = normalizeOrderTypes(charge?.order_types);
        if (!allowedTypes.length) {
            return true;
        }
        return !!orderTypeSlug && allowedTypes.includes(orderTypeSlug);
    };

    if (shouldApplyCharges) {
        for (const charge of extraCharges) {
            if (!isChargeApplicableForOrderType(charge, currentOrderTypeSlug)) {
                continue;
            }

            let chargeAmount = 0;
            if (charge.charge_type === 'percent') {
                chargeAmount = (parseFloat(charge.charge_value || 0) / 100) * discountedTotal;
            } else {
                chargeAmount = parseFloat(charge.charge_value || 0);
            }
            total += chargeAmount;
            serviceTotal += chargeAmount;
        }
    }

    // Step 3: Tax base equals taxable amount (subtotal - discount), excluding charges.
    let taxBase = discountedTotal;
    window.posState.taxBase = taxBase;

    // Step 4: Calculate taxes on tax_base
    totalTaxAmount = 0;

    if (taxMode === 'order') {
        // Order-level taxation
        if (taxes && taxes.length > 0) {
            const totalTaxPercent = taxes.reduce(function(sum, tax) {
                return sum + (parseFloat(tax.tax_percent || 0) || 0);
            }, 0);

            for (const tax of taxes) {
                const taxPercent = parseFloat(tax.tax_percent || 0);
                let taxAmount = 0;
                if (isInclusive) {
                    taxAmount = totalTaxPercent > 0 ? ((taxBase * taxPercent) / (100 + totalTaxPercent)) : 0;
                } else {
                    taxAmount = (taxPercent / 100) * taxBase;
                }
                totalTaxAmount += taxAmount;
            }
            total += totalTaxAmount;
        }
    } else if (taxMode === 'item') {
        // Item-level taxation
        let totalInclusiveTax = 0;
        let totalExclusiveTax = 0;

        for (const key in orderItemTaxDetails) {
            const taxDetail = orderItemTaxDetails[key];
            const taxAmount = taxDetail.tax_amount || 0;

            if (isInclusive) {
                totalInclusiveTax += taxAmount;
            } else {
                totalExclusiveTax += taxAmount;
            }
        }

        // When order-level discount exists in item-tax mode, reduce item taxes proportionally
        // so effective tax is computed on discounted balance.
        const discountedTaxableBase = Math.max(
            0,
            (subTotal || 0) - (window.posState.discountAmount || 0) - (loyaltyPointsRedeemed > 0 ? loyaltyDiscountAmount : 0)
        );
        const itemTaxDiscountFactor = (subTotal > 0)
            ? Math.max(0, Math.min(1, discountedTaxableBase / subTotal))
            : 1;

        if (itemTaxDiscountFactor < 1) {
            totalInclusiveTax *= itemTaxDiscountFactor;
            totalExclusiveTax *= itemTaxDiscountFactor;

            // Keep per-item breakup in sync with the discounted tax totals.
            for (const key in orderItemTaxDetails) {
                const taxDetail = orderItemTaxDetails[key];
                if (!taxDetail || typeof taxDetail !== 'object') continue;

                taxDetail.tax_amount = (Number(taxDetail.tax_amount || 0) * itemTaxDiscountFactor);
                if (taxDetail.tax_breakup && typeof taxDetail.tax_breakup === 'object') {
                    Object.keys(taxDetail.tax_breakup).forEach(function(taxName) {
                        const entry = taxDetail.tax_breakup[taxName];
                        if (!entry || typeof entry !== 'object') return;
                        entry.amount = Number(entry.amount || 0) * itemTaxDiscountFactor;
                    });
                }
            }
        }

        totalTaxAmount = totalInclusiveTax + totalExclusiveTax;

        // Add tax to total (POS expectation: final total = taxable + tax).
        if (totalTaxAmount > 0) {
            total += totalTaxAmount;
        }
    }

    window.posState.totalTaxAmount = totalTaxAmount;

    // Add tip and delivery fees
    const showOrderDetail = !!(window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true'));
    const tipAmount = showOrderDetail ? (parseFloat(window.posState.tipAmount || 0) || 0) : 0;
    const deliveryFee = parseFloat(window.posState.deliveryFee || 0);
    const currentOrderType = String(window.posState.orderType || '').toLowerCase().replace(/\s+/g, '_');
    const isDeliveryOrder = currentOrderType === 'delivery';

    if (tipAmount > 0) {
        total += tipAmount;
    }

    if (isDeliveryOrder && deliveryFee > 0) {
        total += deliveryFee;
    }

    if (discountApplyOn === 'total' && discountValue > 0 && discountType) {
        const preDiscountGrandTotal = Math.max(0, total);
        if (discountType === 'percent') {
            window.posState.discountAmount = Math.round((preDiscountGrandTotal * discountValue) / 100 * 100) / 100;
        } else if (discountType === 'fixed') {
            window.posState.discountAmount = Math.min(discountValue, preDiscountGrandTotal);
        }

        total = Math.max(0, preDiscountGrandTotal - window.posState.discountAmount);

        // Reverse taxable base from discounted grand total.
        if (taxMode === 'order' || taxMode === 'item') {
            const applicableCharges = (extraCharges || []).filter(function(charge) {
                return isChargeApplicableForOrderType(charge, currentOrderTypeSlug);
            });
            const percentChargeFactor = applicableCharges.reduce(function(sum, charge) {
                if (charge.charge_type === 'percent') {
                    return sum + (parseFloat(charge.charge_value || 0) / 100);
                }
                return sum;
            }, 0);
            const fixedChargeTotal = applicableCharges.reduce(function(sum, charge) {
                if (charge.charge_type !== 'percent') {
                    return sum + parseFloat(charge.charge_value || 0);
                }
                return sum;
            }, 0);
            let totalTaxPercent = (taxes || []).reduce(function(sum, tax) {
                return sum + (parseFloat(tax.tax_percent || 0) || 0);
            }, 0);
            if (!(totalTaxPercent > 0) && taxMode === 'item') {
                const percentSet = new Set();
                Object.keys(orderItemTaxDetails || {}).forEach(function(key) {
                    const detail = orderItemTaxDetails[key] || {};
                    const p = Number(detail.tax_percent || 0);
                    if (p > 0) {
                        percentSet.add(p);
                    }
                });
                totalTaxPercent = Array.from(percentSet).reduce(function(sum, p) {
                    return sum + p;
                }, 0);
            }
            const fixedTail = (tipAmount > 0 ? tipAmount : 0)
                + ((isDeliveryOrder && deliveryFee > 0) ? deliveryFee : 0)
                + fixedChargeTotal;
            const baseAfterFixed = Math.max(0, total - fixedTail);
            // Inclusive tax means discounted total is already tax-inclusive.
            // So reverse taxable base from total WITHOUT adding tax factor to divisor.
            const taxFactor = isInclusive
                ? 0
                : (totalTaxPercent / 100);
            const divisor = Math.max(0.000001, 1 + percentChargeFactor + taxFactor);
            const discountedTaxInclusiveAmount = baseAfterFixed / divisor;
            totalTaxAmount = isInclusive
                ? (totalTaxPercent > 0 ? ((discountedTaxInclusiveAmount * totalTaxPercent) / (100 + totalTaxPercent)) : 0)
                : ((totalTaxPercent / 100) * discountedTaxInclusiveAmount);
            taxBase = isInclusive
                ? Math.max(0, discountedTaxInclusiveAmount - totalTaxAmount)
                : discountedTaxInclusiveAmount;
            discountedTotal = discountedTaxInclusiveAmount;
            serviceTotal = fixedChargeTotal + (percentChargeFactor * discountedTaxInclusiveAmount);
            subTotal = Math.max(0, discountedTaxInclusiveAmount + window.posState.discountAmount);
            window.posState.subTotal = Math.round(subTotal * 100) / 100;
            window.posState.discountedTotal = Math.round(discountedTotal * 100) / 100;
            window.posState.taxBase = Math.round(taxBase * 100) / 100;
            window.posState.totalTaxAmount = Math.round(totalTaxAmount * 100) / 100;

            // Keep item tax rows in sync when item mode is active.
            if (taxMode === 'item' && subTotal > 0) {
                const ratio = Math.max(0, Math.min(1, discountedTaxInclusiveAmount / subTotal));
                for (const key in orderItemTaxDetails) {
                    const taxDetail = orderItemTaxDetails[key];
                    if (!taxDetail || typeof taxDetail !== 'object') continue;
                    taxDetail.tax_amount = Number(taxDetail.tax_amount || 0) * ratio;
                    if (taxDetail.tax_breakup && typeof taxDetail.tax_breakup === 'object') {
                        Object.keys(taxDetail.tax_breakup).forEach(function(taxName) {
                            const entry = taxDetail.tax_breakup[taxName];
                            if (!entry || typeof entry !== 'object') return;
                            entry.amount = Number(entry.amount || 0) * ratio;
                        });
                    }
                }
                window.posState.orderItemTaxDetails = orderItemTaxDetails;
            }
        }
    }


    // Update state
    window.posState.total = Math.round(total * 100) / 100;

    // Mark that totals are now calculated (no longer using pre-calculated PHP values)
    window.posState.totalsPreCalculated = false;

    // Update display
    if (typeof window.updateTotalsDisplay === 'function') {
        window.updateTotalsDisplay();
    }

    if (window.__posSkipCustomerDisplayUpdateOnce) {
        window.__posSkipCustomerDisplayUpdateOnce = false;
    } else {
        updateCustomerDisplayCache();
    }
    window.persistPosDraftCart?.();
};

// Debounce timer for customer display updates (SPA-safe across Livewire.navigate swaps)
window.customerDisplayUpdateTimer = window.customerDisplayUpdateTimer || null;

// Update customer display cache - mirrors Livewire Pos.php calculateTotal() pattern
// Debounced to reduce server calls while maintaining real-time feel
function updateCustomerDisplayCache() {
    // Clear existing timer
    if (window.customerDisplayUpdateTimer) {
        clearTimeout(window.customerDisplayUpdateTimer);
    }

    // Debounce: wait 300ms after last change before updating
    window.customerDisplayUpdateTimer = setTimeout(function() {
        sendCustomerDisplayUpdate();
    }, 300);
}

window.flushCustomerDisplayUpdate = function() {
    if (window.customerDisplayUpdateTimer) {
        clearTimeout(window.customerDisplayUpdateTimer);
        window.customerDisplayUpdateTimer = null;
    }
    sendCustomerDisplayUpdate();
};

function sendCustomerDisplayUpdate() {
    // Prepare items for customer display (matching Livewire getCustomerDisplayItems())
    const displayItems = [];
    for (const key in window.posState.orderItemList) {
        const item = window.posState.orderItemList[key];
        const qty = window.posState.orderItemQty[key] || 1;
        const variation = window.posState.orderItemVariation[key];
        const modifiersSelected = window.posState.itemModifiersSelected[key] || [];
        const modifierPrice = window.posState.orderItemModifiersPrice[key] || 0;
        const basePrice = variation ? (variation.price || item.price) : item.price;
        const totalUnitPrice = basePrice + modifierPrice;

        // Build modifiers array matching Livewire structure (name must be string - translatable can be object)
        const modifiers = [];
        if (modifiersSelected.length > 0 && window.posConfig.modifierOptions) {
            modifiersSelected.forEach(function(modifierId) {
                const modifier = window.posConfig.modifierOptions[modifierId];
                if (modifier) {
                    var mName = modifier.name;
                    if (typeof mName !== 'string' && mName !== null && typeof mName === 'object') {
                        mName = mName.en || mName[Object.keys(mName)[0]] || '';
                    }
                    modifiers.push({
                        name: (typeof mName === 'string' ? mName : '') || 'Modifier',
                        price: parseFloat(modifier.price) || 0
                    });
                }
            });
        }

        displayItems.push({
            name: item.item_name || item.name,
            qty: qty,
            price: basePrice,
            total_unit_price: totalUnitPrice,
            variation: variation ? {
                name: variation.variation || variation.name,
                price: variation.price
            } : null,
            modifiers: modifiers,
            notes: window.posState.itemNotes?.[key] || null,
            eu_allergen_keys: (function() {
                var raw = Array.isArray(item.eu_allergen_keys) ? item.eu_allergen_keys : [];
                var order = (window.posConfig && window.posConfig.posEuAllergenKeyOrder) || [];
                if (!order.length) {
                    return [];
                }
                var allowed = {};
                order.forEach(function(k) {
                    allowed[k] = true;
                });
                return raw.filter(function(k) {
                    return typeof k === 'string' && allowed[k];
                });
            })(),
            dietary_labels: (function() {
                var raw = Array.isArray(item.dietary_labels) ? item.dietary_labels : [];
                var order = (window.posConfig && window.posConfig.posDietaryLabelOrder) || [];
                if (!order.length) {
                    return [];
                }
                var allowed = {};
                order.forEach(function(k) {
                    allowed[k] = true;
                });
                return raw.filter(function(k) {
                    return typeof k === 'string' && allowed[k];
                });
            })()
        });
    }

    // Prepare taxes for display (matching Livewire pattern)
    const taxes = window.posConfig?.taxes || [];
    const cartIsEmpty = displayItems.length === 0;
    const totalDisplayTaxPercent = taxes.reduce(function(sum, tax) {
        return sum + (parseFloat(tax.tax_percent || 0) || 0);
    }, 0);
    const displayTaxBase = cartIsEmpty
        ? 0
        : (parseFloat(window.posState.taxBase ?? window.posState.discountedTotal ?? 0) || 0);
    const taxesDisplay = cartIsEmpty
        ? []
        : taxes.map(function(tax) {
            const taxPercent = parseFloat(tax.tax_percent || 0);
            const taxAmount = (window.posConfig?.taxInclusive ?? false)
                ? (totalDisplayTaxPercent > 0 ? ((displayTaxBase * taxPercent) / (100 + totalDisplayTaxPercent)) : 0)
                : ((taxPercent / 100) * displayTaxBase);
            return {
                name: tax.tax_name,
                percent: taxPercent,
                amount: taxAmount
            };
        });

    // Prepare extra charges for display (matching Livewire pattern)
    const extraCharges = window.posConfig?.extraCharges || [];
    const chargesDisplay = cartIsEmpty
        ? []
        : extraCharges.map(function(charge) {
            let amount = 0;
            if (charge.charge_type === 'percent') {
                amount = (parseFloat(charge.charge_value || 0) / 100) * (window.posState.discountedTotal || 0);
            } else {
                amount = parseFloat(charge.charge_value || 0);
            }
            return {
                name: charge.charge_name || charge.name,
                amount: amount
            };
        });

    // Call API to update customer display cache (matching Livewire pattern)
    // Uses server Cache + Pusher broadcast for multi-device support
    $.ajax({
        url: '{{ route("ajax.pos.update-customer-display") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            items: displayItems,
            order_number: window.posState.orderNumber,
            formatted_order_number: window.posState.formattedOrderNumber,
            sub_total: window.posState.subTotal,
            discount: window.posState.discountAmount || 0,
            total: window.posState.total,
            taxes: taxesDisplay,
            extra_charges: chargesDisplay,
            tip: window.posState.tipAmount || 0,
            delivery_fee: window.posState.deliveryFee || 0,
            order_type: window.posState.orderType,
            status: 'idle'
        },
        success: function(response) {
            // Cache updated successfully - silent
        },
        error: function(xhr, status, error) {
            // Silent fail - don't interrupt POS flow
            console.warn('Failed to update customer display:', error);
        }
    });
}

window.sendCustomerDisplayUpdate = sendCustomerDisplayUpdate;

window.updateTotalsDisplay = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    // Use posConfig for configuration
    const currencySymbol = window.posConfig?.currencySymbol || @json($restaurant->currency->currency_symbol ?? '$');
    const currencyCode = window.posConfig?.currencyCode || @json($restaurant->currency->currency_code ?? 'USD');
    const taxMode = window.posConfig?.taxMode || @json($taxMode ?? 'order');
    const isInclusive = window.posConfig?.taxInclusive ?? {{ $restaurant->tax_inclusive ?? 0 ? 'true' : 'false' }};
    const extraCharges = window.posConfig?.extraCharges || [];
    const hasItems = Object.keys(window.posState.orderItemList || {}).length > 0;

    // Update total items count
    const itemCount = Object.keys(window.posState.orderItemList || {}).length;
    $('#total-items-display').text(itemCount);

    const $discountBtn = $('#discount-button-container');
    $discountBtn.toggle(itemCount > 0);

    if (typeof window.updateLoyaltyRedeemButtonVisibility === 'function') {
        window.updateLoyaltyRedeemButtonVisibility();
    }

    // Update subtotal
    $('#subtotal-display').text(window.formatCurrency(window.posState.subTotal || 0));

    // Update subtotal stamp badge (tt parity style)
    const stampLabel = @json(__('app.stampDiscount'));
    const freeItemLabel = @json(__('app.freeItem'));
    const $stampBadge = $('#stamp-discount-badge');
    const $stampBadgeText = $('#stamp-discount-badge-text');
    if ($stampBadge.length && $stampBadgeText.length) {
        let hasFreeStampItems = false;
        let calculatedStampDiscount = 0;
        Object.keys(window.posState.orderItemList || {}).forEach(function(key) {
            const item = window.posState.orderItemList?.[key] || {};
            const note = window.posState.itemNotes?.[key] || '';
            const qty = parseInt(window.posState.orderItemQty?.[key] || 1, 10);
            const amount = parseFloat(window.posState.orderItemAmount?.[key] || 0);
            const variation = window.posState.orderItemVariation?.[key];
            const modifierPrice = parseFloat(window.posState.orderItemModifiersPrice?.[key] || 0);
            const basePrice = variation ? (parseFloat(variation.price || item.price || 0)) : parseFloat(item.price || 0);
            const expectedAmount = (basePrice + modifierPrice) * qty;
            const isFreeStampItem = window.isFreeStampItemByMeta(key, item, note);

            if (isFreeStampItem) {
                hasFreeStampItems = true;
                return;
            }

            if (expectedAmount > amount) {
                calculatedStampDiscount += (expectedAmount - amount);
            }
        });

        const orderLevelStampDiscount = parseFloat(window.posState.stampDiscountAmount || 0);
        const stampDiscountAmount = Math.max(orderLevelStampDiscount, calculatedStampDiscount);

        const hasServerFreeStampItems = window.posState.hasFreeStampItems === true || window.posState.hasFreeStampItems === 1 || window.posState.hasFreeStampItems === '1';
        const shouldShowStampBadge = hasFreeStampItems || hasServerFreeStampItems || stampDiscountAmount > 0.01;
        window.posState.hasFreeStampItems = hasFreeStampItems || hasServerFreeStampItems;

        if (shouldShowStampBadge) {
            const badgeText = stampDiscountAmount > 0.01
                ? `${stampLabel} (-${window.formatCurrency(stampDiscountAmount)})`
                : `${stampLabel} (${freeItemLabel})`;
            $stampBadgeText.text(badgeText);
            $stampBadge.removeClass('hidden').show();
        } else {
            $stampBadge.addClass('hidden').hide();
        }
    }

    // Update/show discount row (hide if loyalty discount is active or rounded discount is 0)
    const loyaltyActive = (window.posState.loyaltyDiscountAmount || 0) > 0;
    const discApplied = typeof window.posOrderDiscountIsApplied === 'function'
        ? window.posOrderDiscountIsApplied(window.posState.discountAmount)
        : Number(window.posState.discountAmount || 0) > 0;
    if (!loyaltyActive && discApplied) {
        let discountTypeText = '';
        if (window.posState.discountType === 'percent') {
            discountTypeText = ` (${window.posState.discountValue}%)`;
        }
        $('#discount-type-display').text(discountTypeText);
        $('#discount-display').text('-' + window.formatCurrency(window.posState.discountAmount));
        $('#discount-row').css('display', 'flex');
    } else {
        $('#discount-row').hide();
    }

    const taxableAmount = (window.posState.discountApplyOn === 'total')
        ? Math.max(0, parseFloat(window.posState.taxBase || 0))
        : Math.max(0, (parseFloat(window.posState.subTotal || 0) - parseFloat(window.posState.discountAmount || 0)));
    $('#taxable-amount-display').text(window.formatCurrency(taxableAmount));
    $('#taxable-amount-row').toggle(hasItems);

    // Loyalty discount row (tt parity): show and update when loyalty applied in session
    const $loyaltyRowJs = $('#loyalty-discount-row-js');
    const $loyaltyRowBlade = $('#loyalty-discount-row-blade');
    if ($loyaltyRowJs.length) {
        if (loyaltyActive) {
            const pts = parseInt(window.posState.loyaltyPointsRedeemed || 0, 10);
            const amt = parseFloat(window.posState.loyaltyDiscountAmount || 0);
            const loyaltyDiscountLabel = @json(__('loyalty::app.loyaltyDiscount'));
            const pointsLabel = @json(__('loyalty::app.points'));
            const $loyaltyLabel = $loyaltyRowJs.find('#loyalty-js-label');
            if ($loyaltyLabel.length) {
                $loyaltyLabel.text(`${loyaltyDiscountLabel} (${pts.toLocaleString()} ${pointsLabel})`);
            }
            $loyaltyRowJs.find('#loyalty-js-points').text(pts.toLocaleString());
            $loyaltyRowJs.find('#loyalty-js-amount').text('-' + window.formatCurrency(amt));
            $loyaltyRowJs.show();
            if ($loyaltyRowBlade.length) $loyaltyRowBlade.hide();
        } else {
            $loyaltyRowJs.hide();
            if ($loyaltyRowBlade.length) $loyaltyRowBlade.show();
        }
    }

    // Update delivery fee note
    if (window.posState.deliveryFee == 0) {
        $('#delivery-fee-note').text('(' + @json(__('modules.delivery.freeDelivery')) + ')').show();
    } else {
        $('#delivery-fee-note').hide();
    }

    // Detect fresh KOT context (same rule as calculateTotal)
    const path = (window.location && window.location.pathname) ? window.location.pathname.toString() : '';
    const href = (window.location && window.location.href) ? window.location.href.toString() : '';
    const urlShowOrderDetail = /[?&]show-order-detail=true(?:&|$)/.test(href);
    const showOrderDetailFlag = !!(window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true'));
    const effectiveShowOrderDetail = showOrderDetailFlag || urlShowOrderDetail;
    const isFreshKotContext =
        !effectiveShowOrderDetail &&
        /\/pos\/kot\/\d+/.test(path);

    // Update extra charges (handle both Blade-rendered and dynamically loaded)
    // Skip rendering charges for fresh new KOT screen to avoid double-charging in UI.
    const currentOrderTypeSlugForDisplay = String(window.posState.orderType || '')
        .trim()
        .toLowerCase()
        .replace(/\s+/g, '_');
    const applicableExtraCharges = (extraCharges || []).filter(function(charge) {
        const rawTypes = charge?.order_types;
        let allowedTypes = [];
        if (Array.isArray(rawTypes)) {
            allowedTypes = rawTypes;
        } else if (typeof rawTypes === 'string') {
            const s = rawTypes.trim();
            if (s) {
                try {
                    const parsed = JSON.parse(s);
                    allowedTypes = Array.isArray(parsed) ? parsed : s.split(',');
                } catch (e) {
                    allowedTypes = s.split(',');
                }
            }
        } else if (rawTypes && typeof rawTypes === 'object') {
            allowedTypes = Object.values(rawTypes);
        }

        const normalized = allowedTypes
            .map(function(v) {
                return String(v || '').trim().toLowerCase().replace(/\s+/g, '_');
            })
            .filter(Boolean);

        return normalized.length === 0
            ? true
            : (currentOrderTypeSlugForDisplay && normalized.includes(currentOrderTypeSlugForDisplay));
    });

    if (!isFreshKotContext && applicableExtraCharges.length > 0 && Object.keys(window.posState.orderItemAmount).length > 0) {
        // Check if charges exist in DOM (Blade rendered) or need to be created (dynamically loaded)
        const existingCharges = $('#extra-charges-container [data-charge-id]').length;

        if (existingCharges === 0) {
            // Charges were loaded dynamically - need to build HTML
            let chargesHtml = '';
            applicableExtraCharges.forEach(charge => {
                let chargeAmount = 0;
                const chargeValue = parseFloat(charge.charge_value || 0);

                if (charge.charge_type === 'percent') {
                    chargeAmount = (chargeValue / 100) * (window.posState.discountedTotal || 0);
                } else {
                    chargeAmount = chargeValue;
                }

                const percentText = charge.charge_type === 'percent' ? ` (${chargeValue}%)` : '';
                const deleteButton = @json(user_can('Update Order'))
                    ? `<span class="text-red-500 hover:scale-110 active:scale-100 cursor-pointer"
                            onclick="removeExtraCharge(${charge.id}, {{ \Illuminate\Support\Js::from($orderType ?? '') }})">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/>
                            </svg>
                        </span>`
                    : '';

                chargesHtml += `
                    <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400"
                         data-charge-id="${charge.id}"
                         data-charge-name="${charge.charge_name}"
                         data-charge-type="${charge.charge_type}"
                         data-charge-value="${chargeValue}">
                        <div class="inline-flex items-center gap-x-1">
                            ${charge.charge_name}${percentText}
                            ${deleteButton}
                        </div>
                        <div class="charge-amount-display">${window.formatCurrency(chargeAmount)}</div>
                    </div>
                `;
            });

            $('#extra-charges-container').html(chargesHtml).show();
        } else {
            // Charges exist - just update amounts
            applicableExtraCharges.forEach(charge => {
                let chargeAmount = 0;
                const chargeValue = parseFloat(charge.charge_value || 0);

                if (charge.charge_type === 'percent') {
                    chargeAmount = (chargeValue / 100) * (window.posState.discountedTotal || 0);
                } else {
                    chargeAmount = chargeValue;
                }

                const $chargeRow = $(`#extra-charges-container [data-charge-id="${charge.id}"]`);
                if ($chargeRow.length > 0) {
                    $chargeRow.find('.charge-amount-display').text(window.formatCurrency(chargeAmount));
                }
            });

            $('#extra-charges-container').show();
        }
    } else {
        $('#extra-charges-container').hide();
    }

    const normalizeTaxBreakup = function(rawBreakup) {
        const normalized = {};
        if (!rawBreakup) {
            return normalized;
        }

        if (!Array.isArray(rawBreakup) && typeof rawBreakup === 'object') {
            Object.keys(rawBreakup).forEach(function(key) {
                const taxInfo = rawBreakup[key] || {};
                const taxName = taxInfo.tax_name || taxInfo.name || key;
                if (!taxName) {
                    return;
                }
                normalized[taxName] = {
                    percent: Number(taxInfo.percent ?? taxInfo.rate ?? taxInfo.tax_percent ?? 0),
                    amount: Number(taxInfo.amount ?? 0)
                };
            });
            return normalized;
        }

        if (Array.isArray(rawBreakup)) {
            rawBreakup.forEach(function(taxInfo) {
                if (!taxInfo || typeof taxInfo !== 'object') {
                    return;
                }
                const taxName = taxInfo.tax_name || taxInfo.name;
                if (!taxName) {
                    return;
                }
                normalized[taxName] = {
                    percent: Number(taxInfo.percent ?? taxInfo.rate ?? taxInfo.tax_percent ?? 0),
                    amount: Number(taxInfo.amount ?? 0)
                };
            });
        }

        return normalized;
    };

    // Update taxes based on tax mode
    if (taxMode === 'order') {
        // Order-level taxes - rebuild from config; fallback to DOM rows when config list is empty.
        let taxes = window.posConfig?.taxes || [];
        if ((!Array.isArray(taxes) || taxes.length === 0)) {
            taxes = [];
            $('#order-taxes-container [data-tax-percent]').each(function() {
                const $row = $(this);
                const name = String($row.attr('data-tax-name') || '').trim();
                const percent = parseFloat($row.attr('data-tax-percent') || 0);
                if (name && !Number.isNaN(percent)) {
                    taxes.push({ tax_name: name, tax_percent: percent });
                }
            });
        }
        let orderTaxesHtml = '';

        if (taxes && taxes.length > 0) {
            const totalTaxPercent = taxes.reduce(function(sum, tax) {
                return sum + (parseFloat(tax.tax_percent || 0) || 0);
            }, 0);
            for (const tax of taxes) {
                const taxPercent = parseFloat(tax.tax_percent || 0);
                const taxBase = (window.posState.taxBase || window.posState.discountedTotal || 0);
                const taxAmount = hasItems
                    ? (isInclusive
                        ? (totalTaxPercent > 0 ? ((taxBase * taxPercent) / (100 + totalTaxPercent)) : 0)
                        : ((taxPercent / 100) * taxBase))
                    : 0;

                orderTaxesHtml += `
                    <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400"
                         data-tax-name="${tax.tax_name}"
                         data-tax-percent="${taxPercent}">
                        <div>${tax.tax_name} (${taxPercent}%)</div>
                        <div class="tax-amount-display">${window.formatCurrency(taxAmount)}</div>
                    </div>
                `;
            }

            $('#order-taxes-container').html(orderTaxesHtml).show();
        } else {
            $('#order-taxes-container').hide();
        }
    } else {
        // Item-level taxes - rebuild tax breakdown
        const taxTotals = {};
        const taxes = window.posConfig?.taxes || [];

        // Calculate tax totals from orderItemTaxDetails
        for (const key in window.posState.orderItemTaxDetails) {
            const taxDetail = window.posState.orderItemTaxDetails[key];
            if (taxDetail && taxDetail.tax_breakup) {
                const qty = Number(taxDetail.qty || 1);
                const taxBreakup = normalizeTaxBreakup(taxDetail.tax_breakup);

                for (const taxName in taxBreakup) {
                    const taxInfo = taxBreakup[taxName];
                    const perUnitAmount = Number(taxInfo?.amount || 0);

                    if (!taxTotals[taxName]) {
                        taxTotals[taxName] = {
                            percent: taxInfo.percent,
                            amount: 0
                        };
                    }

                    // tax_breakup amounts are per-unit; scale by item quantity
                    taxTotals[taxName].amount += perUnitAmount * qty;
                }
            }
        }

        // Rebuild item taxes display
        let itemTaxesHtml = '';
        for (const taxName in taxTotals) {
            const taxInfo = taxTotals[taxName];
            itemTaxesHtml += `
                <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400">
                    <div>${taxName} (${taxInfo.percent}%)</div>
                    <div>${window.formatCurrency(taxInfo.amount)}</div>
                </div>
            `;
        }

        const shouldShowTotalTax = Number(window.posState.totalTaxAmount || 0) > 0
            || (hasItems && Object.keys(taxTotals).length > 0)
            || (!hasItems && taxes && taxes.length > 0);

        if (shouldShowTotalTax) {
            const taxInclusiveText = isInclusive
                ? @json(__('modules.settings.taxInclusive'))
                : @json(__('modules.settings.taxExclusive'));

            itemTaxesHtml += `
                <div class="flex justify-between text-gray-500 text-xs dark:text-neutral-400">
                    <div>` + @json(__('modules.order.totalTax')) + `
                        <span class="text-xs text-gray-400">(${taxInclusiveText})</span>
                    </div>
                    <div id="total-tax-display">${window.formatCurrency(window.posState.totalTaxAmount || 0)}</div>
                </div>
            `;
        }

        $('#item-taxes-container').html(itemTaxesHtml);
    }

    // Keep aggregate tax display in sync for templates that render this row.
    $('#total-tax-display').text(window.formatCurrency(window.posState.totalTaxAmount || 0));

    // Update total
    $('#total-display').text(window.formatCurrency(window.posState.total || 0));

    (function syncPosMobileCartSummary() {
        if (typeof jQuery === 'undefined') {
            return;
        }
        var $badge = $('#pos-menu-fab-cart-badge');
        var n = itemCount;
        var totalStr = window.formatCurrency(window.posState.total || 0);
        try {
            window.dispatchEvent(new CustomEvent('pos-cart-summary-sync', { detail: { count: n } }));
        } catch (e) { /* ignore */ }
        if ($('#pos-mobile-cart-summary-text').length) {
            $('#pos-mobile-cart-summary-text').text(n + ' \u00b7 ' + totalStr);
        }
        if ($badge.length) {
            if (n > 0) {
                $badge.text(n > 99 ? '99+' : String(n)).removeClass('hidden').addClass('inline-flex');
            } else {
                $badge.addClass('hidden').removeClass('inline-flex').text('0');
            }
        }
    })();

};

(function initPosMobileCartSummaryClick() {
    if (window.__posMobileCartSummaryClickBound) {
        return;
    }
    window.__posMobileCartSummaryClickBound = true;
    if (typeof jQuery === 'undefined') {
        return;
    }
    $(document).on('click', '#pos-mobile-cart-summary', function () {
        try {
            window.dispatchEvent(new CustomEvent('pos-focus-cart'));
        } catch (e) { /* ignore */ }
        var el = document.getElementById('order-items-container');
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
})();

@php
    // Use the same formatter settings as `currency_format()` in `app/Helper/start.php`.
    $formats = currency_format_setting();

    $currency_position = $formats->currency_position ?? 'left';
    $no_of_decimal = !is_null($formats->no_of_decimal) ? $formats->no_of_decimal : '0';
    $thousand_separator = !is_null($formats->thousand_separator) ? $formats->thousand_separator : '';
    $decimal_separator = !is_null($formats->decimal_separator) ? $formats->decimal_separator : '0';

    // For current restaurant (currencyId = null in PHP helper), symbol comes from restaurant currency.
    $currency_symbol = $restaurant->currency->currency_symbol ?? '';
@endphp

window.formatCurrency = function(amount) {
    const noOfDecimal = parseInt(@json($no_of_decimal), 10) || 0;
    const thousandSeparator = @json($thousand_separator);
    const decimalSeparator = @json($decimal_separator);
    const currencySymbol = @json($currency_symbol);
    const currencyPosition = @json($currency_position);

    const num = Number(amount || 0);
    const sign = num < 0 ? '-' : '';
    const absNum = Math.abs(num);

    // `toFixed` always uses '.' as decimal separator; we replace it below.
    const fixed = absNum.toFixed(noOfDecimal);
    let parts = fixed.split('.');
    let intPart = parts[0] || '0';
    const decPart = parts.length > 1 ? parts[1] : '';

    if (thousandSeparator) {
        // Insert thousand separators from right, like PHP's `number_format()`.
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, function() {
            return thousandSeparator;
        });
    }

    const formattedAmount = noOfDecimal > 0
        ? (intPart + decimalSeparator + decPart)
        : intPart;

    const amountWithSign = sign + formattedAmount;

    switch (currencyPosition) {
        case 'right':
            return amountWithSign + currencySymbol;
        case 'left_with_space':
            return currencySymbol + ' ' + amountWithSign;
        case 'right_with_space':
            return amountWithSign + ' ' + currencySymbol;
        default:
            return currencySymbol + amountWithSign;
    }
};

// Match PHP `round($amount, no_of_decimal) > 0` so tiny float noise does not keep the discount row visible.
window.posOrderDiscountIsApplied = function(amount) {
    const noOfDecimal = parseInt(@json($no_of_decimal), 10) || 0;
    const n = Number(amount || 0);
    const rounded = parseFloat(n.toFixed(noOfDecimal));
    return rounded > 0;
};

window.updateOrderItems = function() {
    // Recalculate total and update display
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    // Also update the order items container if needed
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
};

// Refresh taxes from server (online) so new tax settings appear without manual cache clear.
window.refreshPosTaxesFromServer = function(force = false) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    if (!window.__posIsEffectiveOnline()) {
        return;
    }
    if (window.__posTaxRefreshInProgress) {
        return;
    }

    const now = Date.now();
    const refreshGapMs = 60 * 1000; // avoid repeated calls within 1 minute
    if (!force && window.__posTaxLastRefreshAt && (now - window.__posTaxLastRefreshAt) < refreshGapMs) {
        return;
    }

    window.__posTaxRefreshInProgress = true;
    $.ajax({
        url: "{{ url('/ajax/pos/taxes') }}",
        type: "GET",
        success: function(response) {
            const incomingTaxes = Array.isArray(response) ? response : [];
            const normalized = incomingTaxes
                .map(function(tax) {
                    return {
                        id: tax?.id ?? null,
                        tax_name: tax?.tax_name || tax?.name || '',
                        tax_percent: parseFloat(tax?.tax_percent ?? tax?.percent ?? 0) || 0
                    };
                })
                .filter(function(tax) {
                    return !!tax.tax_name;
                });

            if (!window.posConfig) {
                window.posConfig = {};
            }
            window.posConfig.taxes = normalized;
            window.__posTaxLastRefreshAt = Date.now();

            if (typeof window.calculateTotal === 'function') {
                window.calculateTotal();
            } else if (typeof window.updateTotalsDisplay === 'function') {
                window.updateTotalsDisplay();
            }
        },
        complete: function() {
            window.__posTaxRefreshInProgress = false;
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.refreshPosTaxesFromServer?.();
    });
} else {
    window.refreshPosTaxesFromServer?.();
}

if (typeof window !== 'undefined') {
    window.addEventListener('online', function() {
        window.refreshPosTaxesFromServer?.(true);
    });
}

window.getMenuItemTaxesFromInput = function($input) {
    if (!$input || !$input.length) {
        return [];
    }

    const menuItemId = parseInt($input.attr('data-item-id') || $input.data('item-id') || 0, 10);
    const fallbackTaxes = (!isNaN(menuItemId) && menuItemId > 0 && window.menuItemTaxesIndex && Array.isArray(window.menuItemTaxesIndex[menuItemId]))
        ? window.menuItemTaxesIndex[menuItemId]
        : [];

    const rawTaxes = $input.attr('data-item-taxes');
    if (!rawTaxes) {
        return fallbackTaxes.map(function(tax) {
            return {
                id: parseInt(tax.id) || null,
                tax_name: tax.tax_name || '',
                tax_percent: parseFloat(tax.tax_percent || 0)
            };
        });
    }

    const decodeHtmlEntities = function(value) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = value;
        return textarea.value;
    };

    try {
        let parsed = null;
        try {
            parsed = JSON.parse(rawTaxes);
        } catch (e) {
            parsed = JSON.parse(decodeHtmlEntities(rawTaxes));
        }

        if (!Array.isArray(parsed)) {
            return fallbackTaxes;
        }

        const normalized = parsed
            .filter(function(tax) {
                return tax && typeof tax === 'object';
            })
            .map(function(tax) {
                return {
                    id: parseInt(tax.id) || null,
                    tax_name: tax.tax_name || '',
                    tax_percent: parseFloat(tax.tax_percent || 0)
                };
            });
        return normalized.length > 0 ? normalized : fallbackTaxes;
    } catch (e) {
        return fallbackTaxes;
    }
};

// Client-side add item to cart (no server request)
window.addCartItemClientSide = function(menuItemId) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    if (window.__multiposBlocksPosInteraction) {
        return;
    }

    const orderID = window.posState.orderID;

    if ((orderID && !window.posState.canUpdateOrder) || (!orderID && !window.posState.canCreateOrder)) {
        window.showToast('error', @json(__('messages.permissionDenied')));
        return;
    }


    // Check order limit
    @php
        $orderStats = getRestaurantOrderStats(branch()->id);
    @endphp
    @if (!$orderStats['unlimited'] && $orderStats['current_count'] >= $orderStats['order_limit'])
        window.showToast('error', @json(__('messages.orderLimitReached')));
        return;
    @endif

    // Prevent adding items to finalized or KOT orders (only when URL is in order-detail mode — avoids stale posState after SPA nav)
    if (orderID && window.posState.orderDetail) {
        const status = window.posState.orderDetail.status;
        if (['billed', 'paid', 'payment_due'].includes(status)) {
            return;
        }
        const urlParams = new URLSearchParams(window.location.search || '');
        const urlShowOrderDetail = urlParams.get('show-order-detail') === 'true';
        const showOd = window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true';
        if (showOd && urlShowOrderDetail && status === 'kot') {
            window.showNewKotRequiredModal();
            return;
        }
    }

    // Get item data from the clicked element
    const $input = $(`input[data-item-id="${menuItemId}"]`);
    if ($input.length === 0) {
        console.error('Menu item not found');
        return;
    }

    const rawInStock = $input.attr('data-item-in-stock');
    const isInStock = !(
        rawInStock === '0' ||
        rawInStock === 'false' ||
        rawInStock === false ||
        rawInStock === 0
    );
    if (!isInStock) {
        window.showToast('error', @json(__('messages.outOfStock')));
        return;
    }

    const variationsCountRaw = $input.attr('data-variations-count');
    const modifiersCountRaw = $input.attr('data-modifiers-count');
    const parsedVariationsCount = Number.parseInt(
        variationsCountRaw ?? $input.data('variationsCount') ?? $input.data('variations-count'),
        10
    );
    const parsedModifiersCount = Number.parseInt(
        modifiersCountRaw ?? $input.data('modifiersCount') ?? $input.data('modifiers-count'),
        10
    );

    const itemData = {
        id: parseInt($input.data('item-id')),
        name: $input.data('item-name'),
        item_name: $input.data('item-name'),
        price: parseFloat($input.data('item-price')),
        image: $input.data('item-image'),
        taxes: window.getMenuItemTaxesFromInput($input),
        variationsCount: Number.isFinite(parsedVariationsCount) ? parsedVariationsCount : 0,
        modifiersCount: Number.isFinite(parsedModifiersCount) ? parsedModifiersCount : 0
    };

    const euFromInput = typeof window.__posGetEuAllergenKeysFromMenuInput === 'function'
        ? window.__posGetEuAllergenKeysFromMenuInput($input)
        : [];
    if (euFromInput.length) {
        itemData.eu_allergen_keys = euFromInput;
    }

    const dietaryFromInput = typeof window.__posGetDietaryLabelsFromMenuInput === 'function'
        ? window.__posGetDietaryLabelsFromMenuInput($input)
        : [];
    if (dietaryFromInput.length) {
        itemData.dietary_labels = dietaryFromInput;
    }

    // Hard fallback for item-wise tax mode: always pick backend-indexed taxes by menu id.
    if (
        (window.posConfig?.taxMode || 'order') === 'item'
        && (!Array.isArray(itemData.taxes) || itemData.taxes.length === 0)
        && window.menuItemTaxesIndex
        && Array.isArray(window.menuItemTaxesIndex[itemData.id])
    ) {
        itemData.taxes = window.menuItemTaxesIndex[itemData.id];
    }

    // Check if item has variations or modifiers - show modals if needed
    if (itemData.variationsCount > 0) {
        // Show variation modal
        if (typeof window.showVariationModal === 'function') {
            // Store the item data for later use
            window.posState.pendingMenuItem = itemData;
            window.showVariationModal(menuItemId);
        }
        return;
    }

    if (itemData.modifiersCount > 0) {
        // Show modifiers modal
        if (typeof window.showModifiersModal === 'function') {
            // Store the item data for later use
            window.posState.pendingMenuItem = itemData;
            window.showModifiersModal(menuItemId);
        }
        return;
    }

    // Initialize state if needed
    if (!window.posState.orderItemList) {
        window.posState.orderItemList = {};
    }
    if (!window.posState.orderItemQty) {
        window.posState.orderItemQty = {};
    }
    if (!window.posState.orderItemAmount) {
        window.posState.orderItemAmount = {};
    }
    if (!window.posState.orderItemVariation) {
        window.posState.orderItemVariation = {};
    }
    if (!window.posState.itemModifiersSelected) {
        window.posState.itemModifiersSelected = {};
    }
    if (!window.posState.orderItemModifiersPrice) {
        window.posState.orderItemModifiersPrice = {};
    }
    if (!window.posState.itemNotes) {
        window.posState.itemNotes = {};
    }

    // Check if item already exists in cart (same item ID, no variation, no modifiers)
    // IMPORTANT: Skip free-stamp items so paid items never merge into a free row.
    let existingKey = null;
    if (window.posState.orderItemList) {
        Object.keys(window.posState.orderItemList).forEach(key => {
            const item = window.posState.orderItemList[key];
            const note = window.posState.itemNotes && window.posState.itemNotes[key] ? window.posState.itemNotes[key] : '';

            // If this row is a free stamp item, never merge into it.
            if (window.isFreeStampItemByMeta && window.isFreeStampItemByMeta(key, item || {}, note)) {
                return;
            }

            const hasVariation = window.posState.orderItemVariation && window.posState.orderItemVariation[key];
            const hasModifiers = window.posState.itemModifiersSelected && window.posState.itemModifiersSelected[key] && window.posState.itemModifiersSelected[key].length > 0;

            // Same item, no variation, no modifiers = same item, increase quantity
            if (item && item.id === itemData.id && !hasVariation && !hasModifiers) {
                existingKey = key;
            }
        });
    }

    let affectedItemKey = null;
    if (existingKey) {
        // Item already exists, increase quantity
        window.posState.orderItemQty[existingKey] = (window.posState.orderItemQty[existingKey] || 0) + 1;
        const basePrice = itemData.price;
        const modifierPrice = (window.posState.orderItemModifiersPrice && window.posState.orderItemModifiersPrice[existingKey]) || 0;
        window.posState.orderItemAmount[existingKey] = window.posState.orderItemQty[existingKey] * (basePrice + modifierPrice);
        window.autoApplyStampPreviewForItem?.(existingKey);
        affectedItemKey = existingKey;
    } else {
        // Add new item
        const itemKey = 'item_' + itemData.id + '_' + Date.now();
        window.posState.orderItemList[itemKey] = itemData;
        window.posState.orderItemQty[itemKey] = 1;
        window.posState.orderItemAmount[itemKey] = itemData.price;
        window.autoApplyStampPreviewForItem?.(itemKey);
        affectedItemKey = itemKey;
    }

    // Update the UI
    window.updateOrderItemsContainer();

    // Auto-scroll cart to show latest added item.
    if (typeof window.scrollPosCartToLatest === 'function') {
        window.scrollPosCartToLatest();
    }

    // Recalculate totals
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }

    // Play beep sound
    if (typeof window.playBeep === 'function') {
        window.playBeep();
    }

    // Auto-open loyalty modal when first non-free item added (tt parity)
    try {
        if (
            window.posState?.loyaltyEnabled &&
            window.posState.customerId &&
            (window.posState.loyaltyPointsRedeemed || 0) === 0 &&
            (window.posState.subTotal || 0) > 0 &&
            typeof window.openLoyaltyRedemptionModal === 'function'
        ) {
            const cartSummary = window.getNonFreeCartSummary?.() || {};
            if ((cartSummary.nonFreeQtyTotal || 0) === 1) {
                window.openLoyaltyRedemptionModal();
            }
        }
    } catch (e) {
        console.warn('Auto loyalty modal check failed:', e);
    }
};

// Update order items container HTML directly - matching original design
window.updateOrderItemsContainer = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    if (typeof window.initPosIconTooltips === 'function') {
        window.initPosIconTooltips(document);
    }
    if (typeof window.initOrderStatusStepTooltips === 'function') {
        window.initOrderStatusStepTooltips();
    }

    // Find ALL item containers that should be populated by JS
    // This includes both kot_items.blade.php and order_items.blade.php containers
    let itemsContainers = $('#order-items-container').find('div.flex.flex-col.rounded.gap-1, div[data-js-populated="1"]');

    // If not found, try the kot_items structure
    if (itemsContainers.length === 0) {
        itemsContainers = $('#order-items-container .flex.flex-col.rounded.gap-1');
    }

    // If still not found, create it
    if (itemsContainers.length === 0) {
        const flexContainer = $('#order-items-container .flex-1.overflow-y-auto');
        if (flexContainer.length > 0) {
            flexContainer.append('<div class="flex flex-col rounded gap-1"></div>');
            itemsContainers = $('#order-items-container .flex.flex-col.rounded.gap-1');
        } else {
            // Fallback: just update the order-items-container directly
            itemsContainers = $('#order-items-container');
        }
    }

        if (!window.posState.orderItemList || Object.keys(window.posState.orderItemList).length === 0) {
        // Empty cart - clear all containers
        itemsContainers.each(function() {
            const $container = $(this);
            // Only show empty state in the main kot_items container (without data-kot-id)
            if (!$container.attr('data-kot-id')) {
                $container.html(
                    typeof window.__posBuildEmptyCartPlaceholderHtml === 'function'
                        ? window.__posBuildEmptyCartPlaceholderHtml()
                        : ''
                );
            } else {
                // For KOT-specific containers, just clear them
                $container.empty();
            }
        });
        if (typeof window.__posUpdateRunningOrderBanner === 'function') {
            window.__posUpdateRunningOrderBanner();
        }
        return;
    }

    const freeItemLabel = @json(__('app.freeItem'));
    const stampDiscountLabel = @json(__('app.stampDiscount'));

    let detectedFreeStampItems = false;

    // Group items by KOT if we're in order_items view
    const itemsByKot = {};
    Object.keys(window.posState.orderItemList).forEach(key => {
        const cleanKey = typeof window.__posNormalizeCartItemKey === 'function'
            ? window.__posNormalizeCartItemKey(key)
            : String(key).replace(/"/g, '');
        // Check if this is a KOT item (format: kot_123_456)
        const kotMatch = cleanKey.match(/^kot_(\d+)_/);
        if (kotMatch) {
            const kotId = kotMatch[1];
            if (!itemsByKot[kotId]) {
                itemsByKot[kotId] = [];
            }
            itemsByKot[kotId].push(key);
        } else {
            // Non-KOT items go in the main list
            if (!itemsByKot['main']) {
                itemsByKot['main'] = [];
            }
            itemsByKot['main'].push(key);
        }
    });

    // Render items in their respective containers
    itemsContainers.each(function() {
        const $container = $(this);
        const kotId = $container.attr('data-kot-id');

        // Determine which items to show in this container
        let itemKeys;
        if (kotId) {
            // This is a KOT-specific container in order_items.blade.php
            itemKeys = itemsByKot[kotId] || [];
        } else {
            // This is the main container in kot_items.blade.php - show all items
            itemKeys = Object.keys(window.posState.orderItemList);
        }

        if (itemKeys.length === 0) {
            // No items for this container
            if (!kotId) {
                // Show empty state only in main container
                $container.html(
                    typeof window.__posBuildEmptyCartPlaceholderHtml === 'function'
                        ? window.__posBuildEmptyCartPlaceholderHtml()
                        : ''
                );
            } else {
                $container.empty();
            }
            return;
        }

        // Reorder so free-stamp items appear once, directly after the item that triggered them
        try {
            const sourceMap = window.posState.freeStampSourceByKey || {};
            const keySet = new Set(itemKeys);
            const orderedKeys = [];
            const mappedFreeKeys = Object.keys(sourceMap).filter(fk => keySet.has(fk));
            const usedFree = new Set();

            itemKeys.forEach(function(key) {
                const isFreeStampKey = key.indexOf('free_stamp_') === 0;
                if (!isFreeStampKey) {
                    orderedKeys.push(key);
                }
                mappedFreeKeys.forEach(function(freeKey) {
                    if (!usedFree.has(freeKey) && sourceMap[freeKey] === key) {
                        orderedKeys.push(freeKey);
                        usedFree.add(freeKey);
                    }
                });
            });

            itemKeys.forEach(function(key) {
                if (!orderedKeys.includes(key)) {
                    orderedKeys.push(key);
                }
            });

            itemKeys = orderedKeys;
        } catch (e) {
            // Fallback safely if reordering fails
        }

        // Build HTML for items
        let html = '';
        itemKeys.forEach((key, idx) => {
            const cleanKey = typeof window.__posNormalizeCartItemKey === 'function'
                ? window.__posNormalizeCartItemKey(key)
                : String(key).replace(/"/g, '');
            const serialNumber = idx + 1;
            const item = window.posState.orderItemList[key];
            const qty = window.posState.orderItemQty[key] || 1;
            const amount = window.posState.orderItemAmount[key] || 0;
            const variation = window.posState.orderItemVariation && window.posState.orderItemVariation[key];
            const modifiers = window.posState.itemModifiersSelected && window.posState.itemModifiersSelected[key];
            const modifierPrice = (window.posState.orderItemModifiersPrice && window.posState.orderItemModifiersPrice[key]) || 0;
            const note = (window.posState.itemNotes && window.posState.itemNotes[key]) || '';
            // Calculate display price (base price + modifier price)
            const basePrice = variation ? (variation.price || item.price) : item.price;
            const displayPrice = basePrice + modifierPrice;
            const expectedAmount = displayPrice * qty;
            // Treat as free-stamp item only when meta says so AND the line amount is actually zero (or nearly zero)
            const isFreeStampItem = window.isFreeStampItemByMeta(key, item, note) && (amount <= 0.0001);
            if (isFreeStampItem) {
                detectedFreeStampItems = true;
            }
            const stampDiscountAmount = Math.max(0, expectedAmount - amount);
            const hasStampDiscount = !isFreeStampItem && stampDiscountAmount > 0.01;
            const cardClasses = isFreeStampItem
                ? 'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20'
                : (hasStampDiscount ? 'border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900 dark:bg-blue-900/20' : 'border-gray-100 dark:border-gray-700');

            html += `
            <div class="${cardClasses} flex flex-col gap-1 border-b pb-1 border-gray-200 dark:border-gray-700" data-item-key="${key}">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-gray-800 dark:text-white">${serialNumber}.</span>
                                <span class="text-gray-900 dark:text-white text-xs">${item.item_name || item.name || 'Item'}</span>
                                ${isFreeStampItem ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">${freeItemLabel}</span>` : ''}
                                ${hasStampDiscount ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">${stampDiscountLabel} (-${window.formatCurrency(stampDiscountAmount)})</span>` : ''}
                            </div>
                            ${variation ? `<span class="text-gray-500 dark:text-gray-400 text-xs">&bull; ${variation.variation || ''}</span>` : ''}
                            ${modifiers && modifiers.length > 0 ? `
                                <div class="inline-flex flex-wrap gap-2 text-xs text-gray-600 dark:text-white">
                                    ${modifiers.map(function(modIdOrObj) {
                                        var modId = (typeof modIdOrObj === 'object' && modIdOrObj !== null && modIdOrObj.id != null) ? modIdOrObj.id : modIdOrObj;
                                        var modOption = (window.posState.modifierOptions && window.posState.modifierOptions[modId]) || (typeof modIdOrObj === 'object' && modIdOrObj !== null ? modIdOrObj : null);
                                        if (!modOption) return '';
                                        var modName = modOption.name;
                                        if (typeof modName !== 'string' && modName !== null && typeof modName === 'object') {
                                            modName = modName.en || modName[Object.keys(modName)[0]] || 'Modifier';
                                        }
                                        modName = (typeof modName === 'string' ? modName : '') || 'Modifier';
                                        var modPrice = parseFloat(modOption.price) || 0;
                                        return '<div class="inline-flex items-center justify-between text-xs mb-1 py-0.5 px-1 border-l-2 border-blue-500 bg-gray-200 dark:bg-gray-900 rounded-md">' +
                                            '<span class="text-gray-900 dark:text-white">' + modName + '</span>' +
                                            (modPrice > 0 ? '<span class="text-gray-600 dark:text-gray-300 ml-1">' + window.formatCurrency(modPrice) + '</span>' : '') +
                                            '</div>';
                                    }).filter(m => m).join('')}
                                </div>
                            ` : ''}
                        </div>
                        <div class="flex items-center gap-2">
                            ${
                                isFreeStampItem
                                    ? `<div class="flex flex-col items-end">
                                            <div class="text-green-600 dark:text-green-400 text-xs font-bold">${window.formatCurrency(0)}</div>
                                            <div class="text-[10px] text-gray-400 line-through">${window.formatCurrency(expectedAmount)}</div>
                                       </div>`
                                    : hasStampDiscount
                                        ? `<div class="flex flex-col items-end">
                                                <div class="text-blue-600 dark:text-blue-400 text-xs font-bold">${window.formatCurrency(amount)}</div>
                                                <div class="text-[10px] text-gray-400 line-through">${window.formatCurrency(expectedAmount)}</div>
                                           </div>`
                                        : `<div class="text-gray-500 dark:text-gray-400 text-xs">${window.formatCurrency(displayPrice)}</div>
                                           <div class="text-gray-500 dark:text-gray-400 text-xs font-bold">${window.formatCurrency(amount)}</div>`
                            }
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full min-h-[2.25rem]">
                    ${
                        isFreeStampItem
                            ? `<div class="text-xs text-gray-500 dark:text-gray-400 shrink-0">${@json(__('app.qty'))} ${qty}</div>`
                            : `<div class="relative inline-flex items-center max-w-[7rem] shrink-0">
                                    <button type="button" onclick="decreaseQty('${key}')"
                                        class="bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-s-md p-2 h-6 relative">
                                        <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                    </button>
                                    <input type="text" value="${qty}"
                                        onchange="updateQtyInput('${key}', this.value)"
                                        class="min-w-10 bg-white border-x-0 border-gray-300 h-6 text-center text-gray-900 text-sm block w-full py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                        min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                    <button type="button" onclick="increaseQty('${key}')"
                                        class="bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 dark:border-gray-600 hover:bg-gray-200 border border-gray-300 rounded-e-md p-2 h-6 relative">
                                        <svg class="w-2 h-2 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                    </button>
                               </div>`
                    }
                    <div class="flex flex-1 min-w-0 justify-center items-center gap-2 flex-wrap px-1">
                    <div class="flex-shrink-0">
                        ${note ? `
                            <button type="button" onclick="showItemNote('${key}')"
                                class="group relative inline-flex items-center gap-1.5 px-2 py-1.5 text-xs text-skin-base hover:text-skin-base/80 bg-skin-base/10 dark:bg-skin-base/20 dark:text-white hover:bg-skin-base/20 dark:hover:bg-skin-base/30 rounded-md transition-all duration-200"
                                title="${note}">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                <span class="truncate max-w-[60px] md:max-w-[100px] lg:max-w-[80px] font-medium">${note}</span>
                                <svg class="w-2.5 h-2.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                            </button>
                        ` : `
                            <button type="button" onclick="showItemNote('${key}')"
                                class="inline-flex items-center gap-1 px-2 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:text-skin-base dark:hover:text-blue-400 hover:bg-skin-base/10 dark:hover:bg-blue-900/20 rounded-md transition-all duration-200"
                                title="@lang('modules.order.addNote')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="font-medium">@lang('modules.order.addNote')</span>
                            </button>
                        `}
                    </div>
                    ${typeof window.__posBuildAllergenIconsHtml === 'function' ? window.__posBuildAllergenIconsHtml(item.eu_allergen_keys, { inActionRow: true }) : ''}
                    ${typeof window.__posBuildDietaryLabelsHtml === 'function' ? window.__posBuildDietaryLabelsHtml(item.dietary_labels, { inActionRow: true }) : ''}
                    </div>
                    ${typeof window.__posCanDeleteCartItem === 'function' && window.__posCanDeleteCartItem(key) ? `
                    <div class="shrink-0">
                        <button type="button" onclick="${(cleanKey.indexOf('kot_') === 0 || cleanKey.indexOf('order_item_') === 0) ? `deleteCartItemHandler('${cleanKey}')` : `removeCartItem('${cleanKey}')`}"
                            class="rounded text-gray-800 dark:text-gray-400 border dark:border-gray-500 hover:bg-gray-200 dark:hover:bg-gray-900/20 p-1 relative">
                            <svg class="w-4 h-4 text-gray-700 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    ` : ''}
                </div>
            </div>

        `;
        });

        // Update this container's HTML
        $container.html(html);
    });

    // Keep runtime free-item truth in sync for subtotal badge logic.
    window.posState.hasFreeStampItems = detectedFreeStampItems;

    if (typeof window.__posUpdateRunningOrderBanner === 'function') {
        window.__posUpdateRunningOrderBanner();
    }
};

// Show item note (placeholder - can be implemented later)
window.showItemNote = function(itemKey) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    const currentNote = (window.posState.itemNotes && window.posState.itemNotes[itemKey]) || '';

    // Use the POS item note modal (same UI used elsewhere)
    window.posState.pendingNoteItemKey = itemKey;
    $('#itemNoteInput').val(currentNote);
    $('#itemNoteError').hide();
    $('#itemNoteModal').show();

    // Auto focus on textarea with a small delay to ensure modal is visible
    setTimeout(() => {
        $('#itemNoteInput').focus();
    }, 200);
};

// Quantity management functions (using jQuery for DOM updates)
window.increaseQty = function(itemKey) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    const itemMeta = window.posState.orderItemList?.[itemKey] || {};
    const itemNote = window.posState.itemNotes?.[itemKey] || '';
    const isFreeStampItem = window.isFreeStampItemByMeta(itemKey, itemMeta, itemNote) && ((window.posState.orderItemAmount?.[itemKey] || 0) <= 0.0001);

    if (isFreeStampItem) {
        window.showToast?.('info', @json(__('app.freeItem')));
        return;
    }

    if (!window.posState.orderItemQty[itemKey]) {
        window.posState.orderItemQty[itemKey] = 0;
    }
    const currentQty = window.posState.orderItemQty[itemKey] || 0;
    const currentAmount = parseFloat(window.posState.orderItemAmount[itemKey] || 0);
    window.posState.orderItemQty[itemKey]++;
    const item = window.posState.orderItemList[itemKey];
    const variation = window.posState.orderItemVariation && window.posState.orderItemVariation[itemKey];
    const modifierPrice = (window.posState.orderItemModifiersPrice && window.posState.orderItemModifiersPrice[itemKey]) || 0;
    const basePrice = variation ? (variation.price || item.price) : item.price;
    const unitPrice = currentQty > 0 ? (currentAmount / currentQty) : (basePrice + modifierPrice);
    window.posState.orderItemAmount[itemKey] = window.posState.orderItemQty[itemKey] * unitPrice;

    // Update the input field to show the new quantity
    $(`#qty-${itemKey}`).val(window.posState.orderItemQty[itemKey]);

    // Update UI using jQuery
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    // IMPORTANT: Only auto-preview stamps for NON-free items; free items themselves should not re-trigger stamp preview
    window.autoApplyStampPreviewForItem?.(itemKey);
};

window.decreaseQty = function(itemKey) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    const itemMeta = window.posState.orderItemList?.[itemKey] || {};
    const itemNote = window.posState.itemNotes?.[itemKey] || '';
    const isFreeStampItem = window.isFreeStampItemByMeta(itemKey, itemMeta, itemNote) && ((window.posState.orderItemAmount?.[itemKey] || 0) <= 0.0001);

    if (isFreeStampItem) {
        window.showToast?.('info', @json(__('app.freeItem')));
        return;
    }

    if (!window.posState.orderItemQty[itemKey] || window.posState.orderItemQty[itemKey] <= 1) {
        window.deleteCartItemHandler(itemKey);
        return;
    }
    const currentQty = window.posState.orderItemQty[itemKey] || 0;
    const currentAmount = parseFloat(window.posState.orderItemAmount[itemKey] || 0);
    window.posState.orderItemQty[itemKey]--;
    const item = window.posState.orderItemList[itemKey];
    const variation = window.posState.orderItemVariation && window.posState.orderItemVariation[itemKey];
    const modifierPrice = (window.posState.orderItemModifiersPrice && window.posState.orderItemModifiersPrice[itemKey]) || 0;
    const basePrice = variation ? (variation.price || item.price) : item.price;
    const unitPrice = currentQty > 0 ? (currentAmount / currentQty) : (basePrice + modifierPrice);
    window.posState.orderItemAmount[itemKey] = window.posState.orderItemQty[itemKey] * unitPrice;

    // Update UI using jQuery
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    window.autoApplyStampPreviewForItem?.(itemKey);
};

window.updateQtyInput = function(itemKey, newQty) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    const itemMeta = window.posState.orderItemList?.[itemKey] || {};
    const itemNote = window.posState.itemNotes?.[itemKey] || '';
    const isFreeStampItem = window.isFreeStampItemByMeta(itemKey, itemMeta, itemNote);

    if (isFreeStampItem) {
        window.showToast?.('info', @json(__('app.freeItem')));
        return;
    }

    const qty = parseInt(newQty) || 1;
    if (qty < 1) {
        window.deleteCartItemHandler(itemKey);
        return;
    }
    const previousQty = parseInt(window.posState.orderItemQty[itemKey] || 0);
    const previousAmount = parseFloat(window.posState.orderItemAmount[itemKey] || 0);
    window.posState.orderItemQty[itemKey] = qty;
    const item = window.posState.orderItemList[itemKey];
    const variation = window.posState.orderItemVariation && window.posState.orderItemVariation[itemKey];
    const modifierPrice = (window.posState.orderItemModifiersPrice && window.posState.orderItemModifiersPrice[itemKey]) || 0;
    const basePrice = variation ? (variation.price || item.price) : item.price;
    const unitPrice = previousQty > 0 ? (previousAmount / previousQty) : (basePrice + modifierPrice);
    window.posState.orderItemAmount[itemKey] = qty * unitPrice;

    // Update the input field to show the correct value
    $(`#qty-${itemKey}`).val(qty);

    // Update UI using jQuery
    if (typeof window.updateOrderItemsContainer === 'function') {
        window.updateOrderItemsContainer();
    }
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    window.autoApplyStampPreviewForItem?.(itemKey);
};

/**
 * Handle delete cart item - routes to appropriate delete function based on item type
 */
window.__posGetApproxVisibleItemCount = function() {
    var domRows = document.querySelectorAll('tr[data-order-item-id]');
    if (domRows && domRows.length) {
        return domRows.length;
    }
    try {
        return Object.keys((window.posState && window.posState.orderItemList) || {}).length;
    } catch (e) {
        return 0;
    }
};

window.__posConfirmDeleteItem = function(isLastItem, onConfirm) {
    var defaultTitle = @json(__('modules.order.deleteOrderItem') . '?');
    var defaultText = @json(__('modules.order.deleteOrderItemMessage'));
    var lastItemTitle = @json(__('modules.order.deleteOrder') . '?');
    var lastItemText = @json('This is the last item. Deleting it will delete this order and you will need to create a new order.');
    var title = isLastItem ? lastItemTitle : defaultTitle;
    var text = isLastItem ? lastItemText : defaultText;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: isLastItem ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: @json(__('app.delete')),
        }).then(function(result) {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
        return;
    }

    var fallbackText = isLastItem
        ? @json('This is the last item. Deleting it will delete this order. Continue?')
        : @json(__('messages.confirmDeleteItem'));
    if (confirm(fallbackText) && typeof onConfirm === 'function') {
        onConfirm();
    }
};

window.deleteCartItemHandler = function(itemKey) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    itemKey = typeof window.__posResolveCartItemKey === 'function'
        ? window.__posResolveCartItemKey(itemKey)
        : String(itemKey).replace(/"/g, '');
    const normalizedItemKey = window.__posNormalizeCartItemKey(itemKey);

    if (typeof window.__posCanDeleteCartItem === 'function' && !window.__posCanDeleteCartItem(normalizedItemKey)) {
        const deniedMsg = @json(__('messages.permissionDenied'));
        if (typeof window.showToast === 'function') {
            window.showToast('error', deniedMsg);
        } else {
            alert(deniedMsg);
        }
        return;
    }

    // Parse the item key
    const parts = normalizedItemKey.split('_');

    // Get order ID from posState
    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : null;

    // Check if it's a draft order item (format: order_item_123)
    if (parts.length >= 3 && parts[0] === 'order' && parts[1] === 'item') {
        const orderItemId = parts[2];
        if (orderId) {
            window.deleteOrderItem(orderItemId, itemKey);
        } else {
            console.error('Order ID not found for order item');
        }
        return;
    }

    // Check if it's a KOT item (format: kot_123_456)
    if (parts.length >= 3 && parts[0] === 'kot') {
        const kotId = parts[1];
        const kotItemId = parts[2];

        if (!orderId) {
            console.error('Order ID not found for KOT item');
            return;
        }

        const doDelete = function() {
            window.__posRunAjax({
                url: '/ajax/pos/delete-cart-item',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    item_key: normalizedItemKey,
                    order_id: orderId
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        }

                        // If order was deleted, same reset as "New order" (no full reload).
                        if (response.order_deleted && response.redirect) {
                            setTimeout(function() {
                                if (typeof window.startNewOrder === 'function') {
                                    window.startNewOrder();
                                } else {
                                    window.location.href = response.redirect;
                                }
                            }, 500);
                        } else {
                            window.removeItemKeyFromPosState(itemKey);
                            var showOrderDetail = window.posState && (window.posState.showOrderDetail === true || window.posState.showOrderDetail === 'true');
                            if (showOrderDetail && response.order && typeof window.updateOrderDetailTotalsFromResponse === 'function') {
                                window.updateOrderDetailTotalsFromResponse(response.order, null);
                            }
                            if (typeof window.calculateTotal === 'function') {
                                window.calculateTotal();
                            }
                            if (typeof window.updateOrderItemsContainer === 'function') {
                                window.updateOrderItemsContainer();
                            }
                        }
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || @json(__('messages.somethingWentWrong'));
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            });
        };

        var isLastItem = window.__posGetApproxVisibleItemCount() <= 1;
        window.__posConfirmDeleteItem(isLastItem, doDelete);
        return;
    }

    // For new items not yet saved (no prefix), just remove from client-side state
    window.removeCartItem(itemKey);
};

// /**
//  * Delete KOT item from order via API
//  */
// window.deleteKotItem = function(kotItemId, orderId) {
//     if (!orderId) {
//         orderId = window.posState?.orderID || window.posState?.orderDetail?.id || {{ optional($orderDetail)->id ?? 'null' }};
//     }

//     if (!orderId) {
//         showToast('error', 'Order ID not found');
//         return;
//     }

//     $.easyAjax({
//         url: `/ajax/pos/orders/${orderId}/items/${kotItemId}`,
//         type: 'DELETE',
//         success: function(response) {
//             if (response.success) {
//                 showToast('success', response.message || 'Item deleted successfully');

//                 // If redirect URL provided (last item deleted), redirect to POS index
//                 if (response.redirect) {
//                     setTimeout(() => window.location.href = response.redirect, 500);
//                 } else {
//                     // Otherwise just reload to show updated order
//                     setTimeout(() => location.reload(), 500);
//                 }
//             }
//         },
//         error: function(xhr) {
//             const error = xhr.responseJSON?.message || 'Failed to delete item';
//             showToast('error', error);
//         }
//     });
// };

window.removeCartItem = function(itemKey) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    itemKey = typeof window.__posResolveCartItemKey === 'function'
        ? window.__posResolveCartItemKey(itemKey)
        : String(itemKey).replace(/"/g, '');
    const normalizedKey = window.__posNormalizeCartItemKey(itemKey);
    const keyParts = normalizedKey.split('_');
    if ((keyParts.length >= 3 && keyParts[0] === 'kot')
        || (keyParts.length >= 3 && keyParts[0] === 'order' && keyParts[1] === 'item')) {
        window.deleteCartItemHandler(normalizedKey);
        return;
    }

    if (window.posState.orderItemList?.[itemKey]) {
        // Define properties to delete for cleaner code
        const propertiesToDelete = [
            'orderItemList',
            'orderItemQty',
            'orderItemAmount',
            'orderItemVariation',
            'itemModifiersSelected',
            'orderItemModifiersPrice',
            'itemNotes',
            'orderItemTaxDetails'
        ];

        // Delete all related properties for the primary item
        propertiesToDelete.forEach(prop => {
            if (window.posState[prop]?.[itemKey]) {
                delete window.posState[prop][itemKey];
            }
        });

        // Maintain stamp-source mapping if present
        if (window.posState.freeStampSourceByKey) {
            delete window.posState.freeStampSourceByKey[itemKey];
        }

        // When deleting a main (paid/discounted) item, only remove the free stamp items
        // that were created from this specific line, instead of wiping all free stamp items.
        if (itemKey.indexOf('free_stamp_') !== 0) {
            const list = window.posState.orderItemList || {};
            const sourceMap = window.posState.freeStampSourceByKey || {};

            Object.keys(list).forEach(function(key) {
                // Only consider free-stamp rows whose recorded source is this itemKey
                if (key.indexOf('free_stamp_') === 0 && sourceMap[key] === itemKey) {
                    propertiesToDelete.forEach(prop => {
                        if (window.posState[prop] && window.posState[prop][key] !== undefined) {
                            delete window.posState[prop][key];
                        }
                    });

                    if (window.posState.freeStampSourceByKey) {
                        delete window.posState.freeStampSourceByKey[key];
                    }
                }
            });
        } else if (window.posState.freeStampSourceByKey) {
            // If deleting a free-stamp-only line, ensure its mapping entry is cleared
            delete window.posState.freeStampSourceByKey[itemKey];
        }

        // Update UI using jQuery
        window.updateOrderItemsContainer?.();
        window.calculateTotal?.();
    }
    window.persistPosDraftCart?.();
};

// Add item to cart function (kept for backward compatibility)
window.addItemToCart = function(menuItem) {
    if (!menuItem) {
        console.error('Menu item is required');
        return;
    }


    if (!menuItem.item_name && menuItem.name) {
        menuItem.item_name = menuItem.name;
    }
    if (!menuItem.name && menuItem.item_name) {
        menuItem.name = menuItem.item_name;
    }

    // Generate a unique key for this cart item
    const itemKey = 'item_' + menuItem.id + '_' + Date.now();

    // Add to cart state
    if (!window.posState.orderItemList) {
        window.posState.orderItemList = {};
    }
    if (!window.posState.orderItemQty) {
        window.posState.orderItemQty = {};
    }
    if (!window.posState.orderItemAmount) {
        window.posState.orderItemAmount = {};
    }

    window.posState.orderItemList[itemKey] = menuItem;
    window.posState.orderItemQty[itemKey] = 1;

    // Calculate item amount (base price + modifiers if any)
    let itemPrice = menuItem.price || 0;
    if (menuItem.modifiers && menuItem.modifiers.length > 0) {
        menuItem.modifiers.forEach(modifier => {
            if (modifier.selected) {
                itemPrice += modifier.price || 0;
            }
        });
    }

    window.posState.orderItemAmount[itemKey] = itemPrice;

    // Update the UI
    window.updateOrderItemsContainer();

    // Auto-scroll cart to show latest added item.
    if (typeof window.scrollPosCartToLatest === 'function') {
        window.scrollPosCartToLatest();
    }

    // Recalculate totals
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }
    if (typeof window.playBeep === 'function') {
        window.playBeep();
    }
    window.autoApplyStampPreviewForItem?.(itemKey);

    // Auto-open loyalty redemption modal when first non-free item is added
    try {
        if (
            window.posState?.loyaltyEnabled &&
            window.posState.customerId &&
            (window.posState.loyaltyPointsRedeemed || 0) === 0 &&
            nonFreeQtyBefore === 0 &&
            nonFreeQtyAfter > 0 &&
            typeof window.openLoyaltyRedemptionModal === 'function'
        ) {
            window.openLoyaltyRedemptionModal();
        }
    } catch (e) {
        console.warn('Auto loyalty modal check failed:', e);
    }
};

// Add cart item to POS (called from menu.blade.php)
window.addCartItemToPos = function(menuItemId, variationsCount, modifierGroupsCount) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    if (window.__multiposBlocksPosInteraction) {
        return;
    }

    $.easyAjax({
        url: "{{ route('ajax.pos.add-cart-item') }}",
        type: "POST",
        data: {
            menu_item_id: menuItemId,
            order_type_id: window.posState.orderTypeId,
            delivery_app_id: window.posState.selectedDeliveryApp,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                        if (response.has_variations) {
                            // Show variation modal
                            if (typeof window.showVariationModal === 'function') {
                                window.showVariationModal(menuItemId);
                            }
                        } else if (response.has_modifiers) {
                            // Show modifiers modal
                            if (typeof window.showModifiersModal === 'function') {
                                window.showModifiersModal(menuItemId);
                            }
                        } else {
                            // Add directly to cart
                            if (typeof window.addItemToCart === 'function') {
                                window.addItemToCart(response.menu_item);
                            }
                        }
            }
        },
        error: function() {
            alert('Error adding item to cart');
        }
    });
};

/**
 * Close the new KOT required modal (defined before showNewKotRequiredModal so inline onclick always works even if script halts later).
 */
window.closeNewKotRequiredModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#newKotRequiredModal').remove();
    }
};

/**
 * Navigate to new KOT page (remove show-order-detail parameter)
 */
window.navigateToNewKot = function(url) {
    window.location.href = url;
};

/**
 * Show modal when user tries to add item in order detail view
 * Requires creating a new KOT first
 * Includes permission and order limit validation
 */
window.showNewKotRequiredModal = function() {

    if (window.__multiposBlocksPosInteraction) {
        return;
    }

    const hasOrderId = window.posState.orderID && window.posState.orderID !== null;
    const orderStatus = (window.posState.orderDetail && window.posState.orderDetail.status) || '';
    let urlShowDetail = false;
    try {
        urlShowDetail = new URLSearchParams(window.location.search || '').get('show-order-detail') === 'true';
    } catch (e) {
        urlShowDetail = false;
    }

    // Only when URL is in order-detail mode (avoids false positives from stale posState)
    if (hasOrderId && orderStatus === 'kot' && urlShowDetail) {
        const newKotUrl = window.location.href.replace(/[?&]show-order-detail=true/g, '');

        const modalHtml = `
            <div id="newKotRequiredModal" class="fixed inset-0 z-[9999]" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="window.closeNewKotRequiredModal()"></div>
                    <div class="relative bg-white dark:bg-slate-800 rounded-lg shadow-2xl max-w-md w-full border border-gray-200 dark:border-slate-700 transform transition-all duration-300 scale-100">
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-2">@lang('app.error')</h3>
                                    <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed">@lang('messages.errorWantToCreateNewKot')</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 flex gap-3 justify-end border-t border-gray-200 dark:border-slate-700">
                            <button type="button" onclick="window.closeNewKotRequiredModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 rounded-md transition-colors duration-200 border border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                @lang('app.cancel')
                            </button>
                            <button type="button" onclick="window.navigateToNewKot('${newKotUrl}')" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-md transition-colors duration-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                @lang('modules.order.newKot')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present and append new one
        $('#newKotRequiredModal').remove();
        $('body').append(modalHtml);
    }
};

// Show variation modal
window.showVariationModal = function(menuItemId) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    // Store menu item ID for later use
    window.posState.pendingMenuItemId = menuItemId;

    // Load variations and show modal
    if (typeof $.easyAjax === 'function') {
        $.easyAjax({
            url: "{{ route('ajax.pos.menu-item-variations', ['id' => ':id']) }}".replace(':id', menuItemId),
            type: "GET",
            data: {
                order_type_id: window.posState.orderTypeId,
                delivery_app_id: window.posState.selectedDeliveryApp
            },
            success: function(response) {
                if (response.success) {
                    window.posState.pendingVariations = Array.isArray(response.variations)
                        ? response.variations
                        : [];
                    // Populate and show variation modal
                    $('#variationModalContent').html(response.html);
                    $('#variationModal').show();
                }
            }
        });
    }
};

// Handle variation selection
window.selectVariation = function(variationId, menuItemId) {
    const selectedVariationId = Number.parseInt(variationId, 10);
    const resolveSelectedVariation = function(variationsList) {
        if (!Array.isArray(variationsList) || !variationsList.length) {
            return null;
        }
        const idAsString = String(variationId);
        return variationsList.find((v) => {
            const vidNum = Number.parseInt(v && v.id, 10);
            if (Number.isFinite(selectedVariationId) && Number.isFinite(vidNum)) {
                return vidNum === selectedVariationId;
            }
            return String(v && v.id) === idAsString;
        }) || null;
    };

    const applySelectedVariation = function(variation) {
        if (!variation) {
            return;
        }
        // Check if item has modifiers
        const menuItem = window.posState.pendingMenuItem || {};
        const modifiersCount = menuItem.modifiersCount || 0;

        if (modifiersCount > 0) {
            // Show modifiers modal with variation
            window.posState.pendingVariation = {
                id: Number.parseInt(variation.id, 10) || selectedVariationId || variationId,
                variation: variation.variation,
                price: parseFloat(variation.price) || 0
            };
            window.closeVariationModal();
            window.showModifiersModal(menuItemId, Number.parseInt(variation.id, 10) || selectedVariationId || variationId);
        } else {
            // Add to cart directly with variation
            window.addItemWithVariation(menuItemId, variation);
            window.closeVariationModal();
        }
    };

    const cachedVariation = resolveSelectedVariation(window.posState.pendingVariations);
    if (cachedVariation) {
        applySelectedVariation(cachedVariation);
        return;
    }

    // Get variation data from API
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    $.easyAjax({
        url: "{{ route('ajax.pos.menu-item-variations', ['id' => ':id']) }}".replace(':id', menuItemId),
        type: "GET",
        data: {
            order_type_id: window.posState.orderTypeId,
            delivery_app_id: window.posState.selectedDeliveryApp
        },
        success: function(response) {
            if (response.success && response.variations) {
                window.posState.pendingVariations = Array.isArray(response.variations)
                    ? response.variations
                    : [];
                const variation = resolveSelectedVariation(response.variations);
                applySelectedVariation(variation);
            }
        }
    });
};

window.posEscapeHtmlModifiers = function(s) {
    if (s === null || s === undefined) {
        return '';
    }
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
};

window.resolvePosModifierGroupsFromCatalog = function(modifierCatalog, variationId) {
    if (!modifierCatalog || typeof modifierCatalog !== 'object') {
        return [];
    }
    const base = Array.isArray(modifierCatalog.base) ? modifierCatalog.base : [];
    if (!variationId) {
        return base.slice();
    }
    const vid = String(variationId);
    const extra = modifierCatalog.by_variation && modifierCatalog.by_variation[vid]
        ? modifierCatalog.by_variation[vid]
        : (modifierCatalog.by_variation && modifierCatalog.by_variation[variationId]
            ? modifierCatalog.by_variation[variationId]
            : []);
    const extraArr = Array.isArray(extra) ? extra : [];
    return base.concat(extraArr);
};

window.mergePosModifierOptionsFromGroups = function(groups) {
    window.posState.modifierOptions = window.posState.modifierOptions || {};
    (groups || []).forEach(function(g) {
        (g.options || []).forEach(function(opt) {
            window.posState.modifierOptions[String(opt.id)] = {
                id: opt.id,
                name: opt.name,
                price: parseFloat(opt.price) || 0,
                groupId: g.id
            };
        });
    });
};

window.buildPosModifiersModalHtml = function(catalogItem, groups, menuItemId, variationId) {
    const i18n = window.__posModifiersModalI18n || {};
    const escAttr = typeof window.posEscapeHtmlModifiers === 'function'
        ? window.posEscapeHtmlModifiers
        : function(s) {
            if (s === null || s === undefined) {
                return '';
            }
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        };
    const fmt = typeof window.formatCurrency === 'function'
        ? window.formatCurrency
        : function(n) { return String(n); };
    const hideImg = window.posConfig && window.posConfig.hideMenuItemImageOnPos;
    const iconBase = (window.__posMenuItemIconBase || '').replace(/\/$/, '');
    const typeIcon = catalogItem.type && iconBase
        ? (iconBase + '/' + catalogItem.type + '.svg')
        : '';
    let variationLabel = '';
    if (variationId && window.posState && window.posState.pendingVariation) {
        const pv = window.posState.pendingVariation;
        if (String(pv.id) === String(variationId) && pv.variation) {
            variationLabel = ' <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ms-1">(' + escAttr(pv.variation) + ')</span>';
        }
    }
    const photoUrl = catalogItem.item_photo_url || '';
    const headerImg = (!hideImg && photoUrl)
        ? '<img class="w-14 h-14 rounded-lg object-cover shrink-0" src="' + escAttr(photoUrl) + '" alt="">'
        : '';
    const typeImg = typeIcon
        ? '<img src="' + escAttr(typeIcon) + '" class="h-4 mr-2" alt="">'
        : '';

    let groupsHtml = '';
    (groups || []).forEach(function(mod) {
        const gid = mod.id;
        const req = mod.is_required ? '<span class="text-red-500 text-xs">*</span>' : '';
        const allowMulti = !!mod.allow_multiple_selection;
        let rows = '';
        (mod.options || []).forEach(function(opt) {
            if (opt.is_available) {
                const inputClick = allowMulti
                    ? ' onclick="event.stopPropagation()"'
                    : ' onclick="event.stopPropagation(); handleSingleSelection(' + gid + ', ' + opt.id + ')"';
                const priceStr = opt.price ? fmt(parseFloat(opt.price) || 0) : '—';
                rows +=
                    '<div role="button" tabindex="0" class="flex items-center gap-2 sm:gap-3 px-2.5 py-1.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/60 border-b border-gray-100 dark:border-gray-600/80 last:border-b-0"' +
                    ' onclick="if (event.target.tagName !== \'INPUT\') { var el = this.querySelector(\'input[value=\\\'" + opt.id + "\\\']\'); if (el) el.click(); }"' +
                    ' onkeydown="if (event.key===\' \') { event.preventDefault(); var el = this.querySelector(\'input[value=\\\'" + opt.id + "\\\']\'); if (el) el.click(); }">' +
                    '<span class="min-w-0 flex-1 text-sm text-gray-900 dark:text-white leading-snug">' + escAttr(opt.name) + '</span>' +
                    '<span class="shrink-0 text-xs sm:text-sm tabular-nums text-gray-600 dark:text-gray-300 w-[4.25rem] sm:w-24 text-right">' + priceStr + '</span>' +
                    '<span class="shrink-0 w-9 flex justify-end">' +
                    '<input type="checkbox" name="modifier_group_' + gid + '" value="' + opt.id + '"' +
                    ' data-modifier-group-id="' + gid + '"' +
                    ' data-modifier-option-id="' + opt.id + '"' +
                    ' data-modifier-price="' + (parseFloat(opt.price) || 0) + '"' +
                    ' data-modifier-name="' + escAttr(opt.name) + '"' +
                    ' class="modifier-option-checkbox w-4 h-4 rounded border-gray-300 focus:ring-skin-base text-skin-base"' +
                    inputClick + ' />' +
                    '</span></div>';
            } else {
                rows +=
                    '<div class="flex items-center gap-2 px-2.5 py-1.5 border-b border-gray-100 dark:border-gray-600/80 last:border-b-0 opacity-75">' +
                    '<span class="min-w-0 flex-1 text-sm text-gray-700 dark:text-gray-300">' + escAttr(opt.name) + '</span>' +
                    '<span class="shrink-0 text-xs tabular-nums text-gray-500 w-[4.25rem] sm:w-24 text-right">—</span>' +
                    '<span class="shrink-0 w-9 flex justify-end"><span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900/80 dark:text-red-200">' + escAttr(i18n.notAvailable || '') + '</span></span>' +
                    '</div>';
            }
        });
        const reqErr = (i18n.requiredGroupTpl || '').split(':name').join(escAttr(mod.name));
        groupsHtml += '<details open class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/40 overflow-hidden" data-modifier-group-id="' + gid + '" data-is-required="' + (mod.is_required ? '1' : '0') + '">' +
            '<summary class="flex items-start gap-2 cursor-pointer select-none list-none px-3 py-2 bg-gray-100/90 dark:bg-gray-700/80 hover:bg-gray-200/90 dark:hover:bg-gray-600/80 [&::-webkit-details-marker]:hidden">' +
            '<div class="min-w-0 flex-1 text-left">' +
            '<div class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">' + escAttr(mod.name) + req + '</div>' +
            (mod.description ? '<div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">' + escAttr(mod.description) + '</div>' : '') +
            '</div>' +
            '<svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5 details-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>' +
            '</summary>' +
            '<div class="bg-white dark:bg-gray-800">' +
            '<div class="hidden sm:flex items-center gap-2 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">' +
            '<span class="min-w-0 flex-1">' + escAttr(i18n.optionName || '') + '</span>' +
            '<span class="shrink-0 w-[4.25rem] sm:w-24 text-right">' + escAttr(i18n.setPrice || '') + '</span>' +
            '<span class="shrink-0 w-9 text-right">' + escAttr(i18n.select || '') + '</span>' +
            '</div>' +
            rows +
            '</div>' +
            '<div id="required-error-' + gid + '" class="px-2.5 py-1.5 text-red-600 text-xs hidden bg-red-50/50 dark:bg-red-950/20">' + reqErr +             '</div></details>';
    });

    const catalogAllergenIcons =
        typeof window.__posBuildAllergenIconsHtml === 'function'
            ? window.__posBuildAllergenIconsHtml(catalogItem.eu_allergen_keys || [], {})
            : '';
    const catalogDietaryIcons =
        typeof window.__posBuildDietaryLabelsHtml === 'function'
            ? window.__posBuildDietaryLabelsHtml(catalogItem.dietary_labels || [], {})
            : '';
    const catalogMetaIcons =
        catalogAllergenIcons || catalogDietaryIcons
            ? '<div class="flex flex-wrap items-center gap-2 mt-1">' +
                catalogAllergenIcons +
                catalogDietaryIcons +
                '</div>'
            : '';

    const itemHeader =
        '<div class="shrink-0 flex gap-3 pb-3 mb-1 border-b border-gray-200 dark:border-gray-700">' +
        headerImg +
        '<div class="min-w-0 flex-1 flex flex-col justify-center gap-0.5">' +
        '<div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">' +
        '<span class="inline-flex items-center min-w-0 text-sm font-semibold text-gray-900 dark:text-white">' + typeImg + '<span class="truncate">' + escAttr(catalogItem.item_name || '') + '</span>' + variationLabel + '</span>' +
        '<span class="text-xs font-semibold tabular-nums text-skin-base dark:text-skin-base shrink-0 sm:ml-auto">' + fmt(parseFloat(catalogItem.price) || 0) + '</span>' +
        '</div>' +
        (catalogItem.description ? '<p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-snug">' + escAttr(catalogItem.description) + '</p>' : '') +
        catalogMetaIcons +
        '</div></div>';

    return '<div class="flex flex-col h-full min-h-0">' +
        '<div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden px-4 pt-3 pb-2 space-y-2.5">' +
        itemHeader +
        groupsHtml +
        '</div>' +
        '<div class="shrink-0 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">' +
        '<button type="button" onclick="closeModifiersModal()" class="inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">' + escAttr(i18n.cancel || 'Cancel') + '</button>' +
        '<button type="button" onclick="saveModifiers(' + menuItemId + ', ' + (variationId ? variationId : 'null') + ')" class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">' + escAttr(i18n.save || 'Save') + '</button>' +
        '</div></div>';
};

// Show modifiers modal (client catalog: no extra AJAX; fallback to server HTML for legacy slices)
window.showModifiersModal = function(menuItemId, variationId) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    window.posState.pendingMenuItemId = menuItemId;
    window.posState.pendingVariationId = variationId || null;

    const useCatalog = window.__posMenuClientSideCatalog && Array.isArray(window.posMenuClientCatalog);
    let catalogItem = null;
    if (useCatalog) {
        catalogItem = window.posMenuClientCatalog.find(function(it) {
            return String(it.id) === String(menuItemId);
        });
    }

    if (useCatalog && catalogItem) {
        const mc = catalogItem.modifier_catalog && typeof catalogItem.modifier_catalog === 'object'
            ? catalogItem.modifier_catalog
            : { base: [], by_variation: {} };
        const groups = window.resolvePosModifierGroupsFromCatalog(mc, variationId);
        if (groups && groups.length > 0) {
            window.mergePosModifierOptionsFromGroups(groups);
            const html = window.buildPosModifiersModalHtml(catalogItem, groups, menuItemId, variationId || null);
            $('#modifiersModalContent').html(html);
            $('#modifiersModal').show();
            return;
        }
    }

    if (typeof $.easyAjax === 'function') {
        $.easyAjax({
            url: "{{ route('ajax.pos.menu-item-modifiers', ['id' => ':id']) }}".replace(':id', menuItemId),
            type: "GET",
            data: {
                order_type_id: window.posState.orderTypeId,
                delivery_app_id: window.posState.selectedDeliveryApp,
                variation_id: variationId || null
            },
            success: function(response) {
                if (response.success) {
                    if (response.modifier_options) {
                        window.posState.modifierOptions = window.posState.modifierOptions || {};
                        Object.keys(response.modifier_options).forEach(function(optionId) {
                            window.posState.modifierOptions[optionId] = response.modifier_options[optionId];
                        });
                    }
                    $('#modifiersModalContent').html(response.html);
                    $('#modifiersModal').show();
                }
            }
        });
    }
};

// Handle single selection for radio buttons
window.handleSingleSelection = function(groupId, optionId) {
    // Uncheck other options in the same group
    $(`input[name="modifier_group_${groupId}"]`).not(`[value="${optionId}"]`).prop('checked', false);
};

// Save modifiers and add to cart
window.saveModifiers = function(menuItemId, variationId) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    // Get all selected modifier options
    const selectedModifiers = [];
    let modifierPrice = 0;
    const modifierOptionsData = {};

    $('.modifier-option-checkbox:checked').each(function() {
        const optionId = parseInt($(this).val());
        const groupId = parseInt($(this).data('modifier-group-id'));
        const price = parseFloat($(this).data('modifier-price')) || 0;
        const name = $(this).data('modifier-name') || '';

        selectedModifiers.push(optionId);
        modifierPrice += price;

        // Store modifier option details
        modifierOptionsData[optionId] = {
            id: optionId,
            name: name,
            price: price,
            groupId: groupId
        };
    });

    // Validate required modifiers (check for required groups)
    let hasError = false;
    $('#modifiersModalContent [data-modifier-group-id]').each(function() {
        const groupId = parseInt($(this).data('modifier-group-id'));
        const isRequired = $(this).data('is-required') === '1' || $(this).data('is-required') === 1;

        if (isRequired) {
            const hasSelection = $(`.modifier-option-checkbox[data-modifier-group-id="${groupId}"]:checked`).length > 0;
            if (!hasSelection) {
                $(`#required-error-${groupId}`).removeClass('hidden');
                hasError = true;
            } else {
                $(`#required-error-${groupId}`).addClass('hidden');
            }
        }
    });

    if (hasError) {
        return;
    }

    // Get menu item data
    const $input = $(`input[data-item-id="${menuItemId}"]`);
    if ($input.length === 0) {
        console.error('Menu item not found');
        return;
    }

    const itemData = {
        id: parseInt($input.data('item-id')),
        name: $input.data('item-name'),
        item_name: $input.data('item-name'), // Add item_name for consistency with backend data
        price: parseFloat($input.data('item-price')),
        image: $input.data('item-image'),
        taxes: window.getMenuItemTaxesFromInput($input)
    };

    const euFromInputModifiers = typeof window.__posGetEuAllergenKeysFromMenuInput === 'function'
        ? window.__posGetEuAllergenKeysFromMenuInput($input)
        : [];
    if (euFromInputModifiers.length) {
        itemData.eu_allergen_keys = euFromInputModifiers;
    }

    const dietaryFromInputModifiers = typeof window.__posGetDietaryLabelsFromMenuInput === 'function'
        ? window.__posGetDietaryLabelsFromMenuInput($input)
        : [];
    if (dietaryFromInputModifiers.length) {
        itemData.dietary_labels = dietaryFromInputModifiers;
    }

    // Add to cart with variation and modifiers
    window.addItemWithVariationAndModifiers(itemData, variationId, selectedModifiers, modifierPrice, modifierOptionsData);

    // Close modal
    window.closeModifiersModal();
};

// Add item to cart with variation
window.addItemWithVariation = function(menuItemId, variation) {
    const $input = $(`input[data-item-id="${menuItemId}"]`);
    if ($input.length === 0) {
        console.error('Menu item not found');
        return;
    }

    const itemData = {
        id: parseInt($input.data('item-id')),
        name: $input.data('item-name'),
        item_name: $input.data('item-name'),
        price: parseFloat($input.data('item-price')),
        image: $input.data('item-image'),
        taxes: window.getMenuItemTaxesFromInput($input)
    };

    const euFromInputVariation = typeof window.__posGetEuAllergenKeysFromMenuInput === 'function'
        ? window.__posGetEuAllergenKeysFromMenuInput($input)
        : [];
    if (euFromInputVariation.length) {
        itemData.eu_allergen_keys = euFromInputVariation;
    }

    const dietaryFromInputVariation = typeof window.__posGetDietaryLabelsFromMenuInput === 'function'
        ? window.__posGetDietaryLabelsFromMenuInput($input)
        : [];
    if (dietaryFromInputVariation.length) {
        itemData.dietary_labels = dietaryFromInputVariation;
    }

    // Initialize state if needed
    if (!window.posState.orderItemList) {
        window.posState.orderItemList = {};
    }
    if (!window.posState.orderItemQty) {
        window.posState.orderItemQty = {};
    }
    if (!window.posState.orderItemAmount) {
        window.posState.orderItemAmount = {};
    }
    if (!window.posState.orderItemVariation) {
        window.posState.orderItemVariation = {};
    }

    // Generate unique key with variation
    const itemKey = 'item_' + itemData.id + '_' + variation.id + '_' + Date.now();

    // Add to cart
    window.posState.orderItemList[itemKey] = itemData;
    window.posState.orderItemQty[itemKey] = 1;
    window.posState.orderItemVariation[itemKey] = {
        id: variation.id,
        variation: variation.variation,
        price: parseFloat(variation.price) || 0
    };
    window.posState.orderItemAmount[itemKey] = parseFloat(variation.price) || 0;

    // Re-run stamp preview for this item (variation price) so discount applies if eligible
    if (window.posState.customerId && typeof window.autoApplyStampPreviewForItem === 'function') {
        window.autoApplyStampPreviewForItem(itemKey);
    }

    // Update UI
    window.updateOrderItemsContainer();

    // Auto-scroll cart to show latest added item.
    if (typeof window.scrollPosCartToLatest === 'function') {
        window.scrollPosCartToLatest();
    }

    // Recalculate totals
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }

    // Play beep sound
    if (typeof window.playBeep === 'function') {
        window.playBeep();
    }
};

// Add item to cart with variation and modifiers
window.addItemWithVariationAndModifiers = function(itemData, variationId, modifierIds, modifierPrice, modifierOptionsData) {
    // Normalize itemData to ensure it has both item_name and name fields
    if (!itemData.item_name && itemData.name) {
        itemData.item_name = itemData.name;
    }
    if (!itemData.name && itemData.item_name) {
        itemData.name = itemData.item_name;
    }

    // Initialize state if needed
    if (!window.posState.orderItemList) {
        window.posState.orderItemList = {};
    }
    if (!window.posState.orderItemQty) {
        window.posState.orderItemQty = {};
    }
    if (!window.posState.orderItemAmount) {
        window.posState.orderItemAmount = {};
    }
    if (!window.posState.orderItemVariation) {
        window.posState.orderItemVariation = {};
    }
    if (!window.posState.itemModifiersSelected) {
        window.posState.itemModifiersSelected = {};
    }
    if (!window.posState.orderItemModifiersPrice) {
        window.posState.orderItemModifiersPrice = {};
    }
    if (!window.posState.modifierOptions) {
        window.posState.modifierOptions = {};
    }

    // Generate unique key
    const sortNumber = modifierIds.sort().join('');
    let itemKey;
    if (variationId) {
        itemKey = 'item_' + itemData.id + '_' + variationId + '_' + sortNumber + '_' + Date.now();
    } else {
        itemKey = 'item_' + itemData.id + '_' + sortNumber + '_' + Date.now();
    }

    // Get variation data if exists
    let variation = null;
    if (variationId && window.posState.pendingVariation) {
        variation = window.posState.pendingVariation;
    }

    // Calculate base price
    const basePrice = variation ? variation.price : itemData.price;
    const totalPrice = basePrice + modifierPrice;

    // Add to cart
    window.posState.orderItemList[itemKey] = itemData;
    window.posState.orderItemQty[itemKey] = 1;
    window.posState.orderItemAmount[itemKey] = totalPrice;

    if (variation) {
        window.posState.orderItemVariation[itemKey] = {
            id: variation.id,
            variation: variation.variation,
            price: variation.price
        };
    }

    if (modifierIds.length > 0) {
        window.posState.itemModifiersSelected[itemKey] = modifierIds;
        window.posState.orderItemModifiersPrice[itemKey] = modifierPrice;

        // Store modifier options for display (passed from saveModifiers)
        window.posState.modifierOptions = window.posState.modifierOptions || {};
        if (modifierOptionsData && typeof modifierOptionsData === 'object') {
            Object.keys(modifierOptionsData).forEach(modId => {
                window.posState.modifierOptions[modId] = modifierOptionsData[modId];
            });
        }
    }

    // Re-run stamp preview for this item (with or without modifiers) so discount uses correct unit price
    if (window.posState.customerId && typeof window.autoApplyStampPreviewForItem === 'function') {
        window.autoApplyStampPreviewForItem(itemKey);
    }

    // Update UI
    window.updateOrderItemsContainer();

    // Auto-scroll cart to show latest added item.
    if (typeof window.scrollPosCartToLatest === 'function') {
        window.scrollPosCartToLatest();
    }

    // Recalculate totals
    if (typeof window.calculateTotal === 'function') {
        window.calculateTotal();
    }

    // Play beep sound
    if (typeof window.playBeep === 'function') {
        window.playBeep();
    }

    // Clear pending data
    window.posState.pendingMenuItem = null;
    window.posState.pendingVariation = null;
    window.posState.pendingVariationId = null;
};

// Modal functions for kot_items
window.showReservationModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#reservationModal').show();
    }
};

window.closeReservationModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#reservationModal').hide();
    }
};

window.confirmSameCustomer = function() {
    window.posState.isSameCustomer = true;
    if (typeof window.closeReservationModal === 'function') {
        window.closeReservationModal();
    }
    // Continue with order save
};

window.confirmDifferentCustomer = function() {
    window.posState.isSameCustomer = false;
    if (typeof window.closeReservationModal === 'function') {
        window.closeReservationModal();
    }
    // Continue with order save
};

window.showTableChangeConfirmationModal = function() {
    // Open table modal client-side only (no request on open).
    if (typeof window.openTableChangeConfirmation === 'function') {
        window.openTableChangeConfirmation();
        return;
    }
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#tableChangeModal').show();
    }
};

window.closeTableChangeModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#tableChangeModal').hide();
    }
};

window.closeTableChangeConfirmationModal = function() {
    window.closeTableChangeModal();
};

// cancelTableChange / confirmTableChange are defined below with full behavior (avoid duplicate stubs).

if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:initialized', () => {
        // Listen for the setTable event from Livewire SetTable component
        Livewire.on('setTable', (event) => {
            const table = event.table;

            if (!table || !table.id) {
                console.error('Invalid table data received');
                return;
            }

            // Save table via AJAX using existing pattern
            saveTableSelectionViaAPI(table);
        });

        // Listen for table updates from Livewire (when table is changed/updated)
        Livewire.on('tableUpdated', (event) => {
            if (event && event.table) {
                window.posState.tableId = event.table.id;
                window.posState.tableNo = event.table.table_code;
                if (event.table.seating_capacity != null && event.table.seating_capacity !== '') {
                    const sc = parseInt(event.table.seating_capacity, 10);
                    window.posState.tableSeatingCapacity = (!Number.isNaN(sc) && sc > 0) ? sc : null;
                }
                if (typeof window.__posClampPaxToTableCapacity === 'function') {
                    window.__posClampPaxToTableCapacity({ silent: false });
                }

                // Update the UI without full reload
                if (typeof updateTableDisplay === 'function') {
                    updateTableDisplay(event.table);
                }
            }
        });

        // Also listen for tableSelected browser event
        Livewire.on('tableSelected', (event) => {
            const tableId = event.tableId;
            const tableCode = event.tableCode;

            console.log('Table selected:', tableId, tableCode);
        });

        // Order detail slide-over closed: do not full-reload the POS (backdrop / close felt broken).
        // Sync JS state and drop ?show-order-detail=true from the URL when present so totals / KOT
        // context match the address bar without tearing down the whole page.
        const orderDetailLw = Livewire.all().find((c) => c.name === 'order.order-detail');
        if (orderDetailLw) {
            let wasOrderDetailOpen = !!orderDetailLw.$wire.showOrderDetail;
            orderDetailLw.$wire.$watch('showOrderDetail', (isOpen) => {
                if (wasOrderDetailOpen && !isOpen) {
                    if (window.posState) {
                        window.posState.showOrderDetail = false;
                    }
                    try {
                        const href = window.location.href;
                        if (!/[?&]show-order-detail=true(?:&|$)/.test(href)) {
                            wasOrderDetailOpen = !!isOpen;
                            return;
                        }
                        const next = new URL(href);
                        next.searchParams.delete('show-order-detail');
                        const nextUrl = next.pathname + (next.search || '') + next.hash;
                        window.history.replaceState(window.history.state || {}, document.title, nextUrl);
                    } catch (e) {
                        console.warn('Could not strip show-order-detail from URL', e);
                    }
                }
                wasOrderDetailOpen = !!isOpen;
            });
        }
    });
}

/**
 * Save table selection via AJAX API call
 */
function saveTableSelectionViaAPI(table) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    // Get current order ID (same resolution as Livewire / submit-order helpers)
    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : (window.posState?.orderID || window.posState?.orderDetail?.id || null);

    if (typeof window.__posTableBlockedForSelection === 'function' && window.__posTableBlockedForSelection(table, orderId)) {
        window.__posShowTableHasActiveOrderMessage(table.table_code);
        return;
    }

    // New order (no server order id yet): persist selected table only in local state.
    if (!orderId) {
        window.posState.tableId = table.id;
        window.posState.tableNo = table.table_code;
        if (table.seating_capacity != null && table.seating_capacity !== '') {
            const sc = parseInt(table.seating_capacity, 10);
            window.posState.tableSeatingCapacity = (!Number.isNaN(sc) && sc > 0) ? sc : null;
        }
        const rs = parseInt(table.seats_left, 10);
        window.posState.tableRemainingSeats = !Number.isNaN(rs) && rs >= 0 ? rs : null;
        if (typeof updateTableDisplay === 'function') {
            updateTableDisplay(table);
        }
        if (typeof window.__posClampPaxToTableCapacity === 'function') {
            window.__posClampPaxToTableCapacity({ silent: false });
        }
        window.closeTableChangeModal();
        return;
    }

    // Show loading state
    const $modal = $('#tableChangeModal');
    $modal.css({'pointer-events': 'none', 'opacity': '0.6'});

    // Make AJAX call to save the table
    $.easyAjax({
        url: '/ajax/pos/set-table',
        type: 'POST',
        data: {
            table_id: table.id,
            order_id: orderId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update posState
                window.posState.tableId = table.id;
                window.posState.tableNo = table.table_code;
                if (table.seating_capacity != null && table.seating_capacity !== '') {
                    const sc = parseInt(table.seating_capacity, 10);
                    window.posState.tableSeatingCapacity = (!Number.isNaN(sc) && sc > 0) ? sc : null;
                }
                const rs = parseInt(table.seats_left, 10);
                window.posState.tableRemainingSeats = !Number.isNaN(rs) && rs >= 0 ? rs : null;
                if (orderId && !window.posState.orderID) {
                    window.posState.orderID = orderId;
                }

                // Update the table display in the UI using centralized function
                if (typeof updateTableDisplay === 'function') {
                    updateTableDisplay(table);
                }

                if (typeof window.__posClampPaxToTableCapacity === 'function') {
                    window.__posClampPaxToTableCapacity({ silent: false });
                }

                // Close the modal
                window.closeTableChangeModal();

            }
        },
        error: function(xhr) {
            let errorMessage = @json(__('messages.tableLockFailed'));

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            window.showToast('error', errorMessage);
        },
        complete: function() {
            // Hide loading state
            $modal.css({'pointer-events': '', 'opacity': ''});
        }
    });
}



/**
 * Open merge table modal and load tables with unpaid orders
 */
window.showMergeTableModal = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    // Show modal with loading state
    $('#mergeTableModal').show();
    $('#mergeTableModalContent').html(`
        <div class="text-center py-8">
            <svg class="animate-spin mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">@lang('app.loading')</p>
        </div>
    `);

    // Load tables with unpaid orders
    $.easyAjax({
        url: "{{ route('ajax.pos.tables-with-unpaid-orders') }}",
        type: "GET",
        success: function(response) {
            if (response.success) {
                const tables = response.tables || [];
                const currentTableId = window.posState.tableId ? parseInt(window.posState.tableId) : null;
                let html = '';

                if (!tables.length) {
                    html = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                @lang('modules.order.noTablesWithUnpaidOrders')
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.order.noTablesWithUnpaidOrdersDescription')
                            </p>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="space-y-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                @lang('modules.order.mergeTableDescription')
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <div class="grid grid-cols-1 gap-3">
                    `;

                    tables.forEach(table => {
                        const isCurrentTable = currentTableId && currentTableId === parseInt(table.id);

                        // Get order info from unpaidOrders array
                        let orderStatus = '';
                        let orderInfo = '';
                        let itemCount = 0;

                        if (table.unpaid_orders && table.unpaid_orders.length > 0) {
                            const latestOrder = table.unpaid_orders[0];
                            orderStatus = latestOrder.status || 'draft';

                            // Count items
                            if (latestOrder.items && latestOrder.items.length > 0) {
                                itemCount = latestOrder.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
                            } else if (latestOrder.kot && latestOrder.kot.length > 0) {
                                latestOrder.kot.forEach(kot => {
                                    if (kot.items && kot.items.length > 0) {
                                        itemCount += kot.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
                                    }
                                });
                            }

                            orderInfo = `${itemCount} ${@json(__('modules.menu.items'))}`;
                        }

                        const statusClass = isCurrentTable ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700' : '';
                        const statusBadgeClass = orderStatus === 'kot' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                                 orderStatus === 'billed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';

                        html += `
                            <label class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer ${statusClass} ${isCurrentTable ? 'opacity-60 cursor-not-allowed' : ''}" id="table-checkbox-${table.id}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 flex-1">
                                        <input type="checkbox"
                                            value="${table.id}"
                                            onchange="toggleTableSelection(${table.id})"
                                            ${isCurrentTable ? 'disabled' : ''}
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <svg fill="currentColor" class="w-6 h-6 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.999 44.999" xml:space="preserve">
                                            <path d="m42.558 23.378 2.406-10.92a1.512 1.512 0 0 0-2.954-.652l-2.145 9.733h-9.647a1.512 1.512 0 0 0 0 3.026h.573l-3.258 7.713a1.51 1.51 0 0 0 1.393 2.102c.59 0 1.15-.348 1.394-.925l2.974-7.038 4.717.001 2.971 7.037a1.512 1.512 0 1 0 2.787-1.177l-3.257-7.713h.573a1.51 1.51 0 0 0 1.473-1.187m-28.35 1.186h.573a1.512 1.512 0 0 0 0-3.026H5.134L2.99 11.806a1.511 1.511 0 1 0-2.954.652l2.406 10.92a1.51 1.51 0 0 0 1.477 1.187h.573L1.234 32.28a1.51 1.51 0 0 0 .805 1.98 1.515 1.515 0 0 0 1.982-.805l2.971-7.037 4.717-.001 2.972 7.038a1.514 1.514 0 0 0 1.982.805 1.51 1.51 0 0 0 .805-1.98z"/>
                                            <path d="M24.862 31.353h-.852V18.308h8.13a1.513 1.513 0 1 0 0-3.025H12.856a1.514 1.514 0 0 0 0 3.025h8.13v13.045h-.852a1.514 1.514 0 0 0 0 3.027h4.728a1.513 1.513 0 1 0 0-3.027"/>
                                        </svg>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900 dark:text-white select-none">
                                                ${table.table_code}
                                                ${isCurrentTable ? '<span class="text-xs text-blue-600 dark:text-blue-400 ml-2">(' + @json(__('modules.order.currentTable')) + ')</span>' : ''}
                                            </div>
                                            ${orderInfo ? `<div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${orderInfo}</div>` : ''}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        ${orderStatus ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadgeClass}">${orderStatus.charAt(0).toUpperCase() + orderStatus.slice(1)}</span>` : ''}
                                    </div>
                                </div>
                            </label>
                        `;
                    });

                    html += `
                                </div>
                            </div>
                            <div id="selectedTablesCount" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800" style="display: none;">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong id="selectedCount">0</strong> ` + @json(__('modules.order.tablesSelectedForMerge')) + `
                                </p>
                            </div>
                        </div>
                    `;
                }

                $('#mergeTableModalContent').html(html);
                // Reset button state
                $('#mergeTablesButton').prop('disabled', true);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || @json(__('messages.somethingWentWrong'));
            $('#mergeTableModalContent').html(`
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">${errorMessage}</p>
                </div>
            `);
        }
    });
};

/**
 * Close merge table modal
 */
window.closeMergeTableModal = function() {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#mergeTableModal').hide();
        // Clear selections
        $('#mergeTableModalContent input[type="checkbox"]').prop('checked', false);
        $('#mergeTablesButton').prop('disabled', true);
    }
};

/**
 * Toggle table selection and update UI
 */
window.toggleTableSelection = function(tableId) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    // Get selected count
    const selectedCount = $('#mergeTableModalContent input[type="checkbox"]:checked').length;

    // Enable/disable merge button
    $('#mergeTablesButton').prop('disabled', selectedCount === 0);

    // Update selected tables count display
    if (selectedCount > 0) {
        $('#selectedCount').text(selectedCount);
        $('#selectedTablesCount').show();
    } else {
        $('#selectedTablesCount').hide();
    }

    // Update checkbox label styling
    const $checkbox = $(`input[value="${tableId}"]`);
    const $label = $checkbox.closest('label');

    if ($checkbox.is(':checked')) {
        $label.addClass('bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700');
    } else {
        $label.removeClass('bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700');
    }
};

/**
 * Merge selected tables into current order
 */
window.mergeSelectedTables = function() {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }

    const selectedTableIds = [];
    $('#mergeTableModalContent input[type="checkbox"]:checked').each(function() {
        selectedTableIds.push(parseInt($(this).val()));
    });

    if (selectedTableIds.length === 0) {
        window.showToast('error', @json(__('modules.order.selectAtLeastOneTable')));
        return;
    }

    // Show loading state on button
    const $button = $('#mergeTablesButton');
    $button.prop('disabled', true);
    $('#mergeButtonText').hide();
    $('#mergeButtonLoading').removeClass('hidden');

    $.easyAjax({
        url: "{{ route('ajax.pos.merge-tables') }}",
        type: "POST",
        data: {
            table_ids: selectedTableIds,
            current_table_id: window.posState.tableId || null,
            order_type_id: window.posState.orderTypeId || null,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                window.closeMergeTableModal();
                window.showToast('success', response.message || @json(__('modules.order.tablesmergedSuccessfully')));
                const shouldReload = response?.data?.reload_required === true;
                if (shouldReload) {
                    // Merge payload is stored in session and consumed on next page load.
                    // Reload is required so merged items/totals are hydrated into POS state.
                    setTimeout(function() {
                        window.location.reload();
                    }, 250);
                    return;
                }

            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || @json(__('messages.somethingWentWrong'));
            window.showToast('error', errorMessage);

            // Reset button state
            $button.prop('disabled', false);
            $('#mergeButtonText').show();
            $('#mergeButtonLoading').addClass('hidden');
        }
    });
};

// Ensure table modal always loads data when opened from POS
window.showTableModal = function() {
    if (typeof window.openTableChangeConfirmation === 'function') {
        window.openTableChangeConfirmation();
    }
};

// All functions are already globally accessible via window.*
// All DOM manipulation uses jQuery
// All AJAX requests use $.easyAjax
// Native JavaScript is only used for: URL manipulation, Array/Object methods, window.location (all standard APIs)

// Additional functions for KOT items (for new_order_items.blade.php)
window.updatePickupDate = function(value) {
    if (typeof window.posState !== 'undefined') {
        window.posState.pickupDate = value;
    }
};

window.updatePickupTime = function(value) {
    if (typeof window.posState !== 'undefined') {
        window.posState.pickupTime = value;
    }
};

window.confirmCancelOrder = function() {
    const orderID = window.posState ? window.posState.orderID : null;
    if (!orderID) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }

    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery not loaded');
        return;
    }

    if (typeof window.showCancelOrderModal === 'function') {
        window.showCancelOrderModal();
        return;
    }

    // Fallback only if modal handler is unavailable.
    if (confirm(@json(__('modules.order.cancelOrderMessage')))) {
        window.cancelOrder();
    }
};

window.confirmDeleteOrder = function() {
    const orderID = window.posState ? window.posState.orderID : null;
    if (!orderID) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }

    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery not loaded');
        return;
    }

    const doDelete = function() {
        window.__posRunAjax({
            url: "{{ route('ajax.pos.delete-order', ['id' => '__ORDER_ID__']) }}".replace('__ORDER_ID__', orderID),
            type: "DELETE",
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Same SPA reset as the "New order" button (no full page reload).
                    if (typeof window.startNewOrder === 'function') {
                        window.startNewOrder();
                    } else {
                        window.location.href = "{{ route('pos.index') }}";
                    }
                }
            }
        });
    };

    if (typeof window.openPosSimpleConfirm === 'function') {
        window.openPosSimpleConfirm(@json(__('modules.order.deleteOrderMessage')), doDelete);
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: @json(__('modules.order.deleteOrder') . '?'),
            text: @json(__('modules.order.deleteOrderMessage')),
            showCancelButton: true,
            confirmButtonText: @json(__('app.delete')),
            cancelButtonText: @json(__('app.cancel')),
            icon: 'warning',
        }).then((result) => {
            if (result.isConfirmed) {
                doDelete();
            }
        });
    } else if (confirm(@json(__('modules.order.deleteOrderMessage')))) {
        doDelete();
    }
};

function updateOrderStatusUI(newStatus) {
    const stepsContainer = document.getElementById('order-status-steps');
    if (!stepsContainer) return;

    const steps = Array.from(stepsContainer.querySelectorAll('.order-status-step'));
    if (!steps.length) return;

    // Compute statuses based on order type
    const orderType = window.posState ? window.posState.orderType : 'dine_in';
    let statuses;
    switch (orderType) {
        case 'delivery':
            statuses = ['placed', 'confirmed', 'preparing', 'food_ready', 'picked_up', 'out_for_delivery', 'reached_destination', 'delivered', 'completed'];
            break;
        case 'pickup':
            statuses = ['placed', 'confirmed', 'preparing', 'ready_for_pickup', 'delivered', 'completed'];
            break;
        default:
            statuses = ['placed', 'confirmed', 'preparing', 'food_ready', 'served', 'completed'];
    }

    let resolvedStatus = newStatus;
    if (orderType === 'delivery' && newStatus === 'ready_for_pickup') {
        resolvedStatus = 'food_ready';
    } else if (orderType === 'pickup' && newStatus === 'food_ready') {
        resolvedStatus = 'ready_for_pickup';
    }

    const currentIndex = statuses.indexOf(resolvedStatus);
    if (currentIndex === -1) return;

    // Update step icons (per-status colors from data-classes-* on each icon)
    const orderStatusIconBase = 'order-status-icon flex size-8 shrink-0 items-center justify-center rounded-full relative z-10 bg-white shadow-sm dark:bg-gray-900 [&_.order-status-step-svg]:text-current';

    steps.forEach((step, index) => {
        const icon = step.querySelector('.order-status-icon');
        if (!icon) return;

        const isCompleted = index < currentIndex;
        const isCurrent = index === currentIndex;
        const isNext = index === currentIndex + 1;

        let stateKey = 'pending';
        if (isCurrent) {
            stateKey = 'current';
        } else if (isCompleted) {
            stateKey = 'completed';
        } else if (isNext) {
            stateKey = 'next';
        }

        const stateClasses = icon.getAttribute('data-classes-' + stateKey) || icon.getAttribute('data-classes-pending') || '';
        const iconBase = icon.dataset.iconBase || orderStatusIconBase;
        const overflowClass = isCurrent
            ? 'overflow-visible order-status-step-is-current'
            : 'overflow-hidden';
        icon.className = iconBase + ' ' + overflowClass + ' ' + stateClasses;
    });

    const progressBar = document.getElementById('order-status-progress-bar');
    if (progressBar && statuses.length > 1) {
        progressBar.style.width = `${(currentIndex / (statuses.length - 1)) * 100}%`;
    }

    // Update badge
    const badge = document.getElementById('order-status-badge');
    if (badge) {
        const labelEl = steps[currentIndex]?.querySelector('.order-status-label');
        if (labelEl) {
            badge.textContent = labelEl.textContent.trim();
        }
        badge.setAttribute('data-status', newStatus);
    }

    // Update cancel button
    const cancelBtn = document.getElementById('order-status-cancel-btn');
    if (cancelBtn) {
        cancelBtn.classList.toggle('hidden', newStatus !== 'placed');
    }

    // Update next button
    const nextBtn = document.getElementById('order-status-next-btn');
    const nextLabel = document.getElementById('order-status-next-label');
    if (nextBtn && nextLabel) {
        if (currentIndex < statuses.length - 1) {
            const nextStatus = statuses[currentIndex + 1];
            const nextLabelEl = steps[currentIndex + 1]?.querySelector('.order-status-label');
            const nextLabelText = nextLabelEl ? nextLabelEl.textContent.trim() : '';

            nextBtn.classList.remove('hidden');
            nextBtn.setAttribute('onclick', `updateOrderStatus('${nextStatus}')`);

            const moveToLabel = window.posConfig?.moveToLabel || 'Move to';
            nextLabel.textContent = `${moveToLabel} ${nextLabelText}`;
        } else {
            nextBtn.classList.add('hidden');
        }
    }

    if (typeof window.initOrderStatusStepTooltips === 'function') {
        window.initOrderStatusStepTooltips();
    }
}

window.updateOrderStatus = function(newStatus) {
    const orderID = window.posState ? window.posState.orderID : null;
    if (!orderID) {
        console.error('Order ID not found');
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }

    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery not loaded');
        return;
    }

    $.easyAjax({
        url: "{{ route('ajax.pos.update-order-status', ['id' => '__ORDER_ID__']) }}".replace('__ORDER_ID__', orderID),
        type: "POST",
        data: {
            status: newStatus,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update local state
                if (window.posState) {
                    window.posState.orderStatus = newStatus;
                    // Keep orderDetail in sync so later actions (bill/kot) post the latest status.
                    if (window.posState.orderDetail) {
                        if (typeof window.posState.orderDetail === 'object' && window.posState.orderDetail !== null) {
                            if (window.posState.orderDetail.order_status && typeof window.posState.orderDetail.order_status === 'object') {
                                window.posState.orderDetail.order_status.value = newStatus;
                            } else {
                                window.posState.orderDetail.order_status = { value: newStatus };
                            }
                        }
                    }
                }

                // Show success message
                if (typeof Livewire !== 'undefined' && Livewire.dispatch) {
                    Livewire.dispatch('alert', {
                        type: 'success',
                        message: response.message,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    showToast('success', response.message);
                }

                // Update UI without page reload
                updateOrderStatusUI(newStatus);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || @json(__('messages.somethingWentWrong'));
            alert(errorMessage);
        }
    });
};

/**
 * Delete order item via AJAX
 */
window.deleteOrderItem = function(itemId, itemKey = null) {
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        alert(@json(__('messages.somethingWentWrong')));
        return;
    }

    const orderId = window.posState ? window.posState.orderID : null;

    if (!orderId) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }

    const doDelete = function() {
        window.__posRunAjax({
            url: `/ajax/pos/orders/${orderId}/items/${itemId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof Livewire !== 'undefined' && Livewire.dispatch) {
                        Livewire.dispatch('alert', {
                            type: 'success',
                            message: response.message,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        showToast('success', response.message);
                    }

                    // If order was deleted, same reset as "New order" (no full reload).
                    if (response.redirect) {
                        if (typeof window.startNewOrder === 'function') {
                            window.startNewOrder();
                        } else {
                            window.location.href = response.redirect;
                        }
                        return;
                    }
                    // Always remove visible row immediately when present for live UI feedback.
                    var row = document.querySelector('tr[data-order-item-id="' + itemId + '"]');
                    if (row) {
                        row.remove();
                    }

                    // Keep local state in sync for both detail and KOT cart panels.
                    if (itemKey) {
                        window.removeItemKeyFromPosState(itemKey);
                    } else {
                        window.removeOrderItemFromPosStateById(itemId);
                    }

                    if (typeof window.calculateTotal === 'function') {
                        window.calculateTotal();
                    }
                    if (typeof window.updateOrderItemsContainer === 'function') {
                        window.updateOrderItemsContainer();
                    }

                    if (response.order) {
                        var countEl = document.getElementById('order-detail-items-count');
                        if (countEl && response.order.items_count !== undefined) {
                            countEl.textContent = response.order.items_count;
                        }
                        if (typeof window.updateOrderDetailTotalsFromResponse === 'function') {
                            window.updateOrderDetailTotalsFromResponse(response.order, null);
                        }
                        if (window.posState) {
                            window.posState.orderDetail = response.order;
                            window.posState.orderID = response.order.id || orderId;
                            if (response.order.status && ['billed', 'paid', 'payment_due'].includes(response.order.status)) {
                                window.posState.showOrderDetail = false;
                            }
                        }
                    } else if (typeof window.refreshOrderPanelsFromServer === 'function' && /\/pos\/kot\/\d+/.test((window.location && window.location.pathname) ? window.location.pathname : '')) {
                        window.refreshOrderPanelsFromServer({ url: window.location.href });
                    }
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || @json(__('messages.somethingWentWrong'));
                alert(errorMessage);
            }
        });
    };

    const isLastItem = window.__posGetApproxVisibleItemCount() <= 1;
    window.__posConfirmDeleteItem(isLastItem, doDelete);
};

// ============================
// Order Detail Functions
// ============================

/**
 * Open table change confirmation modal
 */
window.openTableChangeConfirmation = function() {
    if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
        window.Livewire.dispatch('refreshSetTableComponent');
    }
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        return;
    }
    $('#tableChangeModal').show();
};

/**
 * Load available tables for selection
 */
function loadAvailableTables() {
    $.easyAjax({
        url: '/ajax/pos/tables',
        type: 'GET',
        success: function(response) {
            renderTablesModal(response.tables || response);
        }
    });
}

/**
 * Render tables in modal
 */
function renderTablesModal(tables) {
    let html = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">';

    tables.forEach(table => {
        const seatCap = Number(table.seating_capacity || 0);
        const occupiedPax = Number(table.occupied_pax || 0);
        const seatsLeft = seatCap > 0 ? Math.max(seatCap - occupiedPax, 0) : null;
        const currentOrderId = typeof window.getCurrentPosOrderId === 'function'
            ? window.getCurrentPosOrderId()
            : (window.posState?.orderID || window.posState?.orderDetail?.id || null);
        const isCurrentTable = window.posState.tableId === table.id;
        const hasActiveOrderBlock = typeof window.__posTableBlockedForSelection === 'function'
            ? window.__posTableBlockedForSelection(table, currentOrderId)
            : !!table.has_active_order;
        const isSeatFull = seatCap > 0 ? seatsLeft <= 0 : !!table.is_seat_blocked;
        const isBlocked = (hasActiveOrderBlock || isSeatFull) && !isCurrentTable;
        const tableStatusLabel = isCurrentTable
            ? 'Current'
            : (hasActiveOrderBlock ? @json(__('modules.table.running')) : (isSeatFull ? 'Full' : ((table.available_status || 'available').toString().replace('_', ' '))));
        const seatsMeta = seatCap > 0
            ? `Remaining: ${seatsLeft} / ${seatCap}`
            : 'Remaining: --';

        const activeOrderLockIcon = hasActiveOrderBlock && !isCurrentTable
            ? `<div class="absolute top-2 right-2 z-10">
                    <div class="bg-red-500 text-white p-1 rounded-full shadow cursor-help" title="${@json(__('modules.table.running'))}">
                        <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1"/>
                        </svg>
                    </div>
               </div>`
            : '';

        html += `
            <button type="button"
                onclick="selectTable(${table.id}, ${JSON.stringify(table.table_code)}, ${table.seating_capacity != null ? Number(table.seating_capacity) : 'null'}, ${seatsLeft != null ? Number(seatsLeft) : 'null'}, ${table.has_active_order ? 'true' : 'false'}, ${table.active_order_id != null ? Number(table.active_order_id) : 'null'})"
                class="relative p-4 border-2 rounded-lg transition-all ${
                    isCurrentTable ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' :
                    isBlocked ? 'border-red-300 bg-red-50 dark:bg-red-900/20 cursor-not-allowed opacity-60' :
                    'border-gray-300 dark:border-gray-600 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10'
                }"
                ${isBlocked && !isCurrentTable ? 'disabled' : ''}>
                ${activeOrderLockIcon}
                <div class="text-center">
                    <div class="font-semibold text-lg">${table.table_code}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        ${tableStatusLabel}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        ${seatsMeta}
                    </div>
                </div>
            </button>
        `;
    });

    html += '</div>';
    html += `<div class="mt-4 flex justify-end">
        <button type="button" onclick="closeTableModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
            ${window.trans?.app?.cancel || 'Cancel'}
        </button>
    </div>`;

    $('#tableModalContent').html(html);
}

/**
 * Select a table (optional seating_capacity from /ajax/pos/tables)
 */
window.selectTable = function(tableId, tableCode, seatingCapacity, seatsLeft, hasActiveOrder, activeOrderId) {
    const currentOrderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : (window.posState?.orderID || window.posState?.orderDetail?.id || null);
    const tableMeta = {
        id: tableId,
        table_code: tableCode,
        has_active_order: !!hasActiveOrder,
        active_order_id: activeOrderId != null && activeOrderId !== 'null' ? activeOrderId : null,
    };
    if (typeof window.__posTableBlockedForSelection === 'function' && window.__posTableBlockedForSelection(tableMeta, currentOrderId)) {
        window.__posShowTableHasActiveOrderMessage(tableCode);
        return;
    }

    // Check if this is a different table
    if (window.posState.tableId && window.posState.tableId !== tableId) {
        // Show confirmation modal
        window.posState.pendingTable = {
            id: tableId,
            code: tableCode,
            seating_capacity: seatingCapacity != null ? seatingCapacity : null,
            seats_left: seatsLeft != null ? seatsLeft : null
        };
        $('#pendingTableNo').text(tableCode);
        $('#currentTableNo').text(window.posState.tableNo || '--');
        closeTableModal();
        $('#showTableChangeConfirmationModal').show();
    } else {
        // Set the table directly
        setTableForOrder(tableId, tableCode, seatingCapacity, seatsLeft);
        closeTableModal();
    }
};

/**
 * Set table for order
 */
function setTableForOrder(tableId, tableCode, seatingCapacity, seatsLeft) {
    function applyTableSeatingCapacityFromArg(cap) {
        if (cap === undefined || cap === null || cap === 'null' || cap === '') {
            return;
        }
        const c = parseInt(cap, 10);
        window.posState.tableSeatingCapacity = (!Number.isNaN(c) && c > 0) ? c : null;
    }
    function applyTableRemainingSeatsFromArg(remainingSeats) {
        if (remainingSeats === undefined || remainingSeats === null || remainingSeats === 'null' || remainingSeats === '') {
            window.posState.tableRemainingSeats = null;
            return;
        }
        const r = parseInt(remainingSeats, 10);
        window.posState.tableRemainingSeats = (!Number.isNaN(r) && r >= 0) ? r : null;
    }

    applyTableSeatingCapacityFromArg(seatingCapacity);
    applyTableRemainingSeatsFromArg(seatsLeft);

    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : (window.posState.orderID || window.posState.orderDetail?.id || null);

    if (!orderId) {
        // For new orders, just update the state
        window.posState.tableId = tableId;
        window.posState.tableNo = tableCode;
        if (typeof window.__posClampPaxToTableCapacity === 'function') {
            window.__posClampPaxToTableCapacity({ silent: false });
        }
        closeTableModal();
        return;
    }

    // For existing orders, update via API
    $.easyAjax({
        url: '/ajax/pos/set-table',
        type: 'POST',
        data: {
            order_id: orderId,
            table_id: tableId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                if (!window.posState.orderID) {
                    window.posState.orderID = orderId;
                }
                window.posState.tableId = tableId;
                window.posState.tableNo = tableCode;
                if (typeof updateTableDisplay === 'function') {
                    updateTableDisplay({ id: tableId, table_code: tableCode });
                }
                if (typeof window.__posClampPaxToTableCapacity === 'function') {
                    window.__posClampPaxToTableCapacity({ silent: false });
                }
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.message || 'Failed to update table';
            showToast('error', error);
        }
    });
}

/**
 * Cancel table change (pending confirmation + table picker modal)
 */
window.cancelTableChange = function() {
    window.posState.pendingTable = null;
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        $('#showTableChangeConfirmationModal').hide();
    }
    if (typeof window.closeTableChangeModal === 'function') {
        window.closeTableChangeModal();
    }
};

/**
 * Confirm table change (apply pending table or just close picker)
 */
window.confirmTableChange = function() {
    if (window.posState.pendingTable) {
        const pt = window.posState.pendingTable;
        setTableForOrder(pt.id, pt.code, pt.seating_capacity, pt.seats_left);
        if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
            $('#showTableChangeConfirmationModal').hide();
        }
        window.posState.pendingTable = null;
    } else if (typeof window.closeTableChangeModal === 'function') {
        window.closeTableChangeModal();
    }
};

/**
 * Show cancel order modal
 */
window.showCancelOrderModal = function() {
    $('#confirmDeleteModal').show();
};

/**
 * Close cancel order modal
 */
window.closeCancelOrderModal = function() {
    $('#confirmDeleteModal').hide();
    $('#cancelReason').val('');
    $('#cancelReasonText').val('');
    $('#cancelReasonError').hide();
};

/**
 * Cancel order
 */
window.cancelOrder = function() {
    const orderId = window.posState.orderID;
    const cancelReason = $('#cancelReason').val();
    const cancelReasonText = $('#cancelReasonText').val();

    if (!orderId) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }

    if (!cancelReason && !cancelReasonText) {
        $('#cancelReasonError').text(@json(__('modules.settings.selectCancelReason'))).show();
        return;
    }

    $.easyAjax({
        url: `/ajax/pos/orders/${orderId}/cancel`,
        type: 'POST',
        data: {
            cancel_reason_id: cancelReason,
            cancel_reason_text: cancelReasonText
        },
        success: function(response) {
            if (response.success) {
                closeCancelOrderModal();
                showToast('success', response.message || 'Order cancelled successfully');
                if (window.posState) {
                    window.posState.status = 'canceled';
                    window.posState.orderStatus = 'cancelled';
                }
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.message || 'Failed to cancel order';
            $('#cancelReasonError').text(error).show();
        }
    });
};


/**
 * Show payment modal/page
 */
window.showPayment = function(orderId) {
    window.location.href = `/orders/payment/${orderId}`;
};

/**
 * Print order via AJAX (PosAjaxController::ajaxPrintOrder) — directPrint vs URL same as Livewire Pos.
 */
window.printOrder = function(orderId, triggerButton = null) {
    if (window.__posPrintOrderInProgress) {
        return;
    }
    const id = orderId || (window.posState && window.posState.orderID) || null;
    if (!id) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }
    window.__posPrintOrderInProgress = true;
    if (typeof window.setGlobalOrderActionLock === 'function') {
        window.setGlobalOrderActionLock(true);
    }
    if (typeof window.toggleSingleActionButton === 'function') {
        window.toggleSingleActionButton(triggerButton, true);
    }
    const releasePrintButton = function() {
        window.__posPrintOrderInProgress = false;
        if (typeof window.setGlobalOrderActionLock === 'function') {
            window.setGlobalOrderActionLock(false);
        }
        if (typeof window.toggleSingleActionButton === 'function') {
            window.toggleSingleActionButton(triggerButton, false);
        }
    };
    if (typeof window.ajaxPrintOrderById === 'function') {
        window.ajaxPrintOrderById(id);
        setTimeout(releasePrintButton, 900);
        return;
    }
    const printUrl = '/orders/print/' + id;
    if (typeof window.printLocation === 'function') {
        window.printLocation(printUrl);
    } else {
        window.open(printUrl, '_blank');
    }
    setTimeout(releasePrintButton, 900);
};

if (typeof Livewire !== 'undefined' && typeof Livewire.on === 'function') {
    Livewire.on('posPaymentCompletedPrint', (payload) => {
        const data = Array.isArray(payload) ? payload[0] : payload;
        const orderId = data?.id ?? null;
        if (!orderId || typeof window.printOrder !== 'function') {
            return;
        }
        setTimeout(() => window.printOrder(orderId), 120);
    });
}

/**
 * Print KOT via AJAX (PosAjaxController::ajaxPrintKot). Pass kotId or uses currentKotId / last KOT on order.
 */
window.printKot = function(kotId) {
    let id = kotId;
    if (!id && typeof window.currentKotId !== 'undefined' && window.currentKotId) {
        id = window.currentKotId;
    }
    if (!id && window.posState && window.posState.orderDetail) {
        const od = window.posState.orderDetail;
        const list = od.kot || od.kots;
        if (Array.isArray(list) && list.length) {
            id = list[list.length - 1].id;
        }
    }
    if (!id) {
        alert(@json(__('modules.order.orderNotFound')));
        return;
    }
    const orderIdForPrint = window.posState && window.posState.orderID ? parseInt(window.posState.orderID, 10) : 0;
    if (orderIdForPrint && typeof window.ajaxPrintKotForOrder === 'function') {
        window.ajaxPrintKotForOrder(orderIdForPrint, [id]);
        return;
    }
    if (typeof window.ajaxPrintKotById === 'function') {
        window.ajaxPrintKotById(id);
        return;
    }
    const orderId = typeof window.getCurrentPosOrderId === 'function'
        ? window.getCurrentPosOrderId()
        : null;
    const printUrl = '/pos/kot/' + (orderId || '');
    if (typeof window.printLocation === 'function') {
        window.printLocation(printUrl);
    } else {
        window.open(printUrl, '_blank');
    }
};

/**
 * Create new KOT for existing order
 */
window.newKot = function() {
    const orderId = window.posState.orderID;
    if (!orderId) {
        console.error('Order ID not found');
        return;
    }
    window.location.href = `/pos/kot/${orderId}`;
};

// Initialize order detail data if viewing order detail page
@if(isset($orderDetail) && $orderDetail)
    // Override posState with order detail data using JSON parse to avoid XrayWrapper issues
    try {
        window.posState.orderID = {{ $orderDetail->id }};
        window.posState.orderDetail = JSON.parse({!! json_encode(json_encode($orderDetail)) !!});
        window.posState.orderStatus = @json($orderDetail->order_status->value);
        window.posState.orderStatus = '{{ $orderDetail->order_status->value }}';

        // Persist Hotel room-service stay selection across "See order" / order detail view.
        // This prevents Bill failing validation after navigating to order detail.
        try {
            const od = window.posState.orderDetail || {};
            const ctxType = (od.context_type || '').toString();
            const ctxId = parseInt(od.context_id || 0, 10);
            if (ctxType === 'HOTEL_ROOM' && ctxId > 0) {
                window.posState.selectedStayId = ctxId;
                window.posState.selectedStayRoomNumber = od.context_room_number || window.posState.selectedStayRoomNumber || null;
                window.posState.selectedStayNumber = od.context_stay_number || window.posState.selectedStayNumber || null;
                window.posState.billTo = od.bill_to || window.posState.billTo || 'POST_TO_ROOM';

                // Update small summary UI (if present) used by Hotel room-service selector.
                if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                    if (od.context_room_number) {
                        $('#ajax-room-stay-summary-room').text(od.context_room_number);
                    }
                    if (od.context_stay_number) {
                        $('#ajax-room-stay-summary-stay').text(od.context_stay_number).removeClass('hidden');
                    }
                    if (typeof window.syncPosRoomServiceBillToSelect === 'function') {
                        window.syncPosRoomServiceBillToSelect();
                    }
                    if (typeof window.syncSelectedHotelStaySummaryFromState === 'function') {
                        window.syncSelectedHotelStaySummaryFromState();
                    }
                }
            }
        } catch (e2) {
            // no-op
        }
    } catch (e) {
        console.error('Error initializing order detail:', e);
    }
@endif

@php
    $posOfflineInitLabels = [
        'statusOffline' => __('messages.posOfflineStatusOffline'),
        'statusSyncing' => __('messages.posOfflineStatusSyncing'),
        'badgeTitleOnline' => __('messages.posOfflineBadgeTitleOnline'),
        'badgeTitleOffline' => __('messages.posOfflineBadgeTitleOffline'),
        'modalTitleOnline' => __('messages.posOfflineModalTitleOnline'),
        'modalTitleOffline' => __('messages.posOfflineModalTitleOffline'),
        'modalSubtitleOnlineTpl' => __('messages.posOfflineModalSubtitleOnline'),
        'modalSubtitleOfflineTpl' => __('messages.posOfflineModalSubtitleOffline'),
        'modalClose' => __('messages.posOfflineModalClose'),
        'footerOnline' => __('messages.posOfflineFooterOnline'),
        'footerOffline' => __('messages.posOfflineFooterOffline'),
        'noPending' => __('messages.posOfflineNoPending'),
        'orderNumberPrefix' => __('messages.posOfflineOrderLabel'),
        'customerLabel' => __('messages.posOfflineCustomer'),
        'tableLabel' => __('messages.posOfflineTable'),
        'tableIdTpl' => __('messages.posOfflineTableId'),
        'itemsLabel' => __('messages.posOfflineItems'),
        'subtotalLabel' => __('messages.posOfflineSubtotal'),
        'discountLabel' => __('messages.posOfflineDiscount'),
        'totalLabel' => __('messages.posOfflineTotal'),
        'actionsLabel' => __('messages.posOfflineActions'),
        'itemCountTpl' => __('messages.posOfflineItemCount'),
        'sessionExpired' => __('messages.posOfflineSessionExpired'),
        'navGuardTitle' => __('messages.posOfflineNavGuardTitle'),
        'navGuardBody' => __('messages.posOfflineNavGuardBody'),
        'navGuardStay' => __('messages.posOfflineNavGuardStay'),
        'navGuardLeave' => __('messages.posOfflineNavGuardLeave'),
        'reloadBody' => __('messages.posOfflineReloadBody'),
        'reloadProceed' => __('messages.posOfflineReloadProceed'),
        'printKot' => __('messages.posOfflinePrintKot'),
        'printBill' => __('messages.posOfflinePrintBill'),
        'addNewKot' => __('modules.order.newKot'),
        'newKotUnavailable' => __('messages.orderNotFound'),
        'offlineOrderLoaded' => 'Offline order loaded. You can add a new KOT now.',
        'printReceiptsLabel' => __('messages.posOfflinePrintReceiptsLabel'),
        'offlinePaymentPending' => __('modules.order.payment'),
        'offlinePaymentMethodLabel' => __('modules.order.method'),
        'offlinePaymentDueLabel' => __('modules.order.dueAmount'),
        'offlinePaymentTenderedLabel' => __('modules.order.amountPaid'),
        'offlinePaymentChangeLabel' => __('modules.order.change'),
        'payNowQueue' => __('messages.posOfflinePayNowQueue'),
        'offlinePayHint' => __('messages.posOfflinePayHint'),
        'offlinePaymentAttachedSection' => __('messages.posOfflinePaymentAttachedSection'),
        'offlineQueuedPaymentTitle' => __('messages.posOfflineQueuedPaymentTitle'),
        'offlineOrphanPaymentNote' => __('messages.posOfflineOrphanPaymentNote'),
        'orderTypeMap' => [
            'dine_in' => __('modules.order.dine_in'),
            'delivery' => __('modules.order.delivery'),
            'pickup' => __('modules.order.pickup'),
            'room_service' => __('modules.order.room_service'),
        ],
    ];
@endphp

window.__posOfflineSyncToast = @json(__('messages.posOfflineSyncComplete'));
window.addEventListener('load', function() {
    if (window.PosOffline && typeof window.PosOffline.init === 'function') {
        window.PosOffline.init({
            saveOrderUrl: @json(route('ajax.pos.save-order')),
            syncPaymentUrl: @json(route('ajax.pos.sync-offline-payment')),
            currencyCode: (window.posConfig && window.posConfig.currencyCode) || 'USD',
            currencySymbol: (window.posConfig && window.posConfig.currencySymbol) || '$',
            navGuardLeaveUrl: @json(route('dashboard')),
            labels: @json($posOfflineInitLabels),
        });
    }

    window.__updatePosClearCacheButtonVisibility = function() {
        var btn = document.getElementById('pos-clear-cache-btn');
        if (!btn) {
            return;
        }
        var online = true;
        if (typeof window.__posIsEffectiveOnline === 'function') {
            online = !!window.__posIsEffectiveOnline();
        } else if (typeof navigator !== 'undefined') {
            online = navigator.onLine !== false;
        }
        if (online) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    };

    var clearCacheBtn = document.getElementById('pos-clear-cache-btn');
    if (clearCacheBtn) {
        clearCacheBtn.addEventListener('click', function() {
            var proceed = function() {
                var clearLocalPosCache = function() {
                    try {
                        // Keep server-backed order detail/cart view intact (e.g. /pos/kot/:id?show-order-detail=true).
                        // Only clear in-memory cart state for fresh client-side carts.
                        var hasServerBackedOrderView = !!(
                            window.posState &&
                            window.posState.orderID &&
                            (window.posState.showOrderDetail || window.posState.orderDetail)
                        );
                        if (!hasServerBackedOrderView) {
                            window.__clearPosCartStateNow?.();
                        }
                        for (var i = window.localStorage.length - 1; i >= 0; i--) {
                            var k = window.localStorage.key(i);
                            if (!k) continue;
                            if (k.indexOf('pos_draft_cart_v1_') === 0 || k === 'pos_draft_cart_v1_1') {
                                window.localStorage.removeItem(k);
                            }
                        }
                        window.localStorage.clear();
                    } catch (e) {
                        // ignore
                    }

                    // Refetch POS data without full page reload.
                    if (typeof window.syncPosTaxRevisionCache === 'function') {
                        window.syncPosTaxRevisionCache();
                    }
                    if (typeof window.refreshPosTaxesFromServer === 'function') {
                        window.refreshPosTaxesFromServer(true);
                    }
                    if (window.__posMenuClientSideCatalog && typeof window.reloadPosMenuCatalog === 'function') {
                        window.reloadPosMenuCatalog();
                    } else if (typeof window.loadMenuItems === 'function') {
                        window.loadMenuItems();
                    }
                    if (typeof window.updateCategoryCounts === 'function') {
                        window.updateCategoryCounts();
                    }
                    if (window.PosOffline && typeof window.PosOffline.refreshUI === 'function') {
                        window.PosOffline.refreshUI();
                    }
                };

                $.easyAjax({
                    url: "{{ route('ajax.pos.clear-cache') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        clearLocalPosCache();
                    },
                    error: function() {
                        // Even if server cache clear fails, still clear local cache so user can continue.
                        clearLocalPosCache();
                    }
                });
            };
            if (typeof window.openPosSimpleConfirm === 'function') {
                window.openPosSimpleConfirm(@json(__('messages.posClearCacheConfirm')), proceed, { anchorEl: clearCacheBtn });
            } else if (window.confirm(@json(__('messages.posClearCacheConfirm')))) {
                proceed();
            }
        });
    }
    window.__updatePosClearCacheButtonVisibility();
    window.addEventListener('online', window.__updatePosClearCacheButtonVisibility);
    window.addEventListener('offline', window.__updatePosClearCacheButtonVisibility);
    window.addEventListener('posOfflineRender', window.__updatePosClearCacheButtonVisibility);

    window.__posUpdateOfflineTestToolbar = function() {
        var tb = document.getElementById('pos-offline-test-toggle');
        if (!tb) {
            return;
        }
        var realOff = typeof navigator !== 'undefined' && navigator.onLine === false;
        if (realOff) {
            tb.disabled = true;
            tb.textContent = @json(__('messages.posOfflineDebugBrowserOffline'));
            tb.setAttribute('aria-pressed', 'false');
            return;
        }
        tb.disabled = false;
        if (window.__posForceOfflineTest) {
            tb.textContent = @json(__('messages.posOfflineDebugUseNetwork'));
            tb.setAttribute('aria-pressed', 'true');
        } else {
            tb.textContent = @json(__('messages.posOfflineDebugSimulate'));
            tb.setAttribute('aria-pressed', 'false');
        }
    };
    window.addEventListener('posOfflineRender', window.__posUpdateOfflineTestToolbar);
    window.addEventListener('online', window.__posUpdateOfflineTestToolbar);
    window.addEventListener('offline', window.__posUpdateOfflineTestToolbar);

    window.addEventListener('posOfflineRender', function() {
        if (typeof window.__posCachePlannedOrderSequence === 'function') {
            window.__posCachePlannedOrderSequence();
        }
        if (typeof window.__posSyncNewCartOrderNumberAheadOfOfflineQueue === 'function') {
            window.__posSyncNewCartOrderNumberAheadOfOfflineQueue();
        }
        if (typeof window.__posUpdateRunningOrderBanner === 'function') {
            window.__posUpdateRunningOrderBanner();
        }
    });

    if (typeof window.__posCachePlannedOrderSequence === 'function') {
        window.__posCachePlannedOrderSequence();
    }
    if (typeof window.__posSyncNewCartOrderNumberAheadOfOfflineQueue === 'function') {
        window.__posSyncNewCartOrderNumberAheadOfOfflineQueue();
    }
    if (typeof window.__posUpdateRunningOrderBanner === 'function') {
        window.__posUpdateRunningOrderBanner();
    }

    window.__initPosOfflineTestToolbarDrag = function() {
        var toolbar = document.getElementById('pos-offline-test-toolbar');
        var handle = document.getElementById('pos-offline-test-drag');
        if (!toolbar || !handle) {
            return;
        }
        if (toolbar.dataset.dragInit === '1') {
            return;
        }
        toolbar.dataset.dragInit = '1';

        var key = 'pos_offline_test_toolbar_pos_v1';
        var clamp = function(v, min, max) {
            return Math.max(min, Math.min(max, v));
        };
        var applyPosition = function(x, y) {
            var pad = 6;
            var maxX = Math.max(pad, window.innerWidth - toolbar.offsetWidth - pad);
            var maxY = Math.max(pad, window.innerHeight - toolbar.offsetHeight - pad);
            var left = clamp(Number(x) || pad, pad, maxX);
            var top = clamp(Number(y) || pad, pad, maxY);
            toolbar.style.left = left + 'px';
            toolbar.style.top = top + 'px';
            toolbar.style.right = 'auto';
            toolbar.style.bottom = 'auto';
            try {
                window.localStorage.setItem(key, JSON.stringify({ left: left, top: top }));
            } catch (e) {
                // ignore
            }
        };
        var applyMobileDefaultPosition = function() {
            if (window.innerWidth >= 1024) {
                return;
            }
            toolbar.style.left = '8px';
            toolbar.style.top = 'auto';
            toolbar.style.bottom = '5.25rem';
            toolbar.style.right = 'auto';
        };
        var restorePosition = function() {
            try {
                var raw = window.localStorage.getItem(key);
                if (!raw) {
                    applyMobileDefaultPosition();
                    return;
                }
                var p = JSON.parse(raw);
                if (!p || typeof p.left === 'undefined' || typeof p.top === 'undefined') {
                    applyMobileDefaultPosition();
                    return;
                }
                requestAnimationFrame(function() {
                    if (window.innerWidth < 1024) {
                        var midY = window.innerHeight * 0.35;
                        if (Number(p.top) > midY && Number(p.top) < window.innerHeight * 0.75) {
                            applyMobileDefaultPosition();
                            return;
                        }
                    }
                    applyPosition(p.left, p.top);
                });
            } catch (e) {
                applyMobileDefaultPosition();
            }
        };
        restorePosition();

        var dragging = false;
        var offsetX = 0;
        var offsetY = 0;
        var onMove = function(clientX, clientY) {
            if (!dragging) return;
            applyPosition(clientX - offsetX, clientY - offsetY);
        };
        var stopDrag = function() {
            dragging = false;
            document.body.style.userSelect = '';
            window.removeEventListener('mousemove', onMouseMove);
            window.removeEventListener('mouseup', onMouseUp);
            window.removeEventListener('touchmove', onTouchMove);
            window.removeEventListener('touchend', onTouchEnd);
        };
        var startDrag = function(clientX, clientY) {
            var rect = toolbar.getBoundingClientRect();
            offsetX = clientX - rect.left;
            offsetY = clientY - rect.top;
            dragging = true;
            document.body.style.userSelect = 'none';
        };
        var onMouseMove = function(e) { onMove(e.clientX, e.clientY); };
        var onMouseUp = function() { stopDrag(); };
        var onTouchMove = function(e) {
            if (!e.touches || !e.touches[0]) return;
            onMove(e.touches[0].clientX, e.touches[0].clientY);
        };
        var onTouchEnd = function() { stopDrag(); };

        handle.addEventListener('mousedown', function(e) {
            e.preventDefault();
            startDrag(e.clientX, e.clientY);
            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);
        });
        handle.addEventListener('touchstart', function(e) {
            if (!e.touches || !e.touches[0]) return;
            startDrag(e.touches[0].clientX, e.touches[0].clientY);
            window.addEventListener('touchmove', onTouchMove, { passive: true });
            window.addEventListener('touchend', onTouchEnd);
        }, { passive: true });
        window.addEventListener('resize', function() {
            if (!toolbar.style.left || !toolbar.style.top) return;
            applyPosition(parseFloat(toolbar.style.left), parseFloat(toolbar.style.top));
        });
    };

    window.openPosSimpleConfirm = function(message, onConfirm, options) {
        options = options || {};
        var backdrop = document.getElementById('posSimpleConfirmBackdrop');
        var modal = document.getElementById('posSimpleConfirmModal');
        var msgEl = document.getElementById('posSimpleConfirmMessage');
        var cancelBtn = document.getElementById('posSimpleConfirmCancel');
        var okBtn = document.getElementById('posSimpleConfirmOk');
        if (!modal || !msgEl || !cancelBtn || !okBtn) {
            if (window.confirm(message || @json(__('app.confirm'))) && typeof onConfirm === 'function') {
                onConfirm();
            }
            return;
        }

        msgEl.textContent = message || @json(__('app.confirm'));
        if (backdrop) {
            backdrop.classList.remove('hidden');
        }
        modal.classList.remove('hidden');

        var positionPopover = function() {
            var pad = 8;
            var anchor = options.anchorEl || null;
            var left = Math.max(pad, Math.floor((window.innerWidth - modal.offsetWidth) / 2));
            var top = Math.max(pad, Math.floor((window.innerHeight - modal.offsetHeight) / 2));
            if (anchor && typeof anchor.getBoundingClientRect === 'function') {
                var r = anchor.getBoundingClientRect();
                var preferredTop = r.bottom + 8;
                var fallbackTop = r.top - modal.offsetHeight - 8;
                top = (preferredTop + modal.offsetHeight <= window.innerHeight - pad)
                    ? preferredTop
                    : Math.max(pad, fallbackTop);
                left = Math.min(
                    Math.max(pad, r.right - modal.offsetWidth),
                    Math.max(pad, window.innerWidth - modal.offsetWidth - pad)
                );
            }
            modal.style.left = left + 'px';
            modal.style.top = top + 'px';
        };
        requestAnimationFrame(positionPopover);

        var close = function() {
            if (backdrop) {
                backdrop.classList.add('hidden');
            }
            modal.classList.add('hidden');
            okBtn.removeEventListener('click', handleOk);
            cancelBtn.removeEventListener('click', handleCancel);
            if (backdrop) {
                backdrop.removeEventListener('click', handleCancel);
            }
            document.removeEventListener('mousedown', handleOutside, true);
            document.removeEventListener('keydown', handleEsc);
            window.removeEventListener('resize', handleResize);
            window.removeEventListener('scroll', handleScroll, true);
        };
        var handleOk = function() {
            close();
            if (typeof onConfirm === 'function') onConfirm();
        };
        var handleCancel = function() { close(); };
        var handleOutside = function(e) {
            if (!modal.contains(e.target)) close();
        };
        var handleEsc = function(e) { if (e.key === 'Escape') close(); };
        var handleResize = function() { positionPopover(); };
        var handleScroll = function() { positionPopover(); };

        okBtn.addEventListener('click', handleOk);
        cancelBtn.addEventListener('click', handleCancel);
        if (backdrop) {
            backdrop.addEventListener('click', handleCancel);
        }
        document.addEventListener('mousedown', handleOutside, true);
        document.addEventListener('keydown', handleEsc);
        window.addEventListener('resize', handleResize);
        window.addEventListener('scroll', handleScroll, true);
    };

    window.__initPosOfflineTestToolbarDrag();
    var testBtn = document.getElementById('pos-offline-test-toggle');
    if (testBtn) {
        window.__posUpdateOfflineTestToolbar();
        testBtn.addEventListener('click', function() {
            if (!window.PosOffline || typeof window.PosOffline.setForceOfflineTest !== 'function') {
                return;
            }
            if (typeof navigator !== 'undefined' && navigator.onLine === false) {
                return;
            }
            window.PosOffline.setForceOfflineTest(!window.__posForceOfflineTest);
            window.__posUpdateOfflineTestToolbar();
        });
    }
});

</script>
@endpush
