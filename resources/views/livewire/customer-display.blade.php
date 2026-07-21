<div class="h-[100dvh] min-h-0 flex flex-col overflow-hidden bg-gray-50 w-full" @if(!pusherSettings()->is_enabled_pusher_broadcast) wire:poll.2s @endif>

    @if($status == 'billed')
        <div class="flex flex-1 flex-col items-center justify-center p-0 sm:p-8">
            <div class="w-full max-w-2xl flex flex-col items-center justify-center py-12">
                <div class="text-4xl font-bold text-skin-base mb-4">@lang('modules.order.thankYouMessage', ['restaurant' => restaurant()->name])</div>
                <div class="text-xl text-gray-700 mb-4">@lang('modules.order.pleaseProceedToPayment')</div>
                @if($cashDue)
                    <div class="text-2xl font-semibold text-gray-800 mb-2">@lang('modules.order.amountDue'): {{ currency_format($cashDue, restaurant()->currency_id) }}</div>
                @endif
                @if($qrCodeImageUrl)
                    <div class="flex flex-col items-center mt-6">
                        <img src="{{ $qrCodeImageUrl }}" alt="QR Code" class="h-40 w-40 object-contain rounded shadow border border-gray-200 bg-white">
                        <div class="text-sm text-gray-500 mt-2">@lang('modules.billing.paybyQr')</div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="flex flex-1 flex-col min-h-0 w-full max-w-2xl mx-auto">
        <!-- Header -->
        <div class="shrink-0 w-full flex flex-col items-center px-4 pt-2 pb-1 relative">
            @if(restaurant()->logo_url)
                <x-restaurant-logo class="h-10 mb-1" />
            @endif
            <div class="text-lg font-bold text-gray-800 tracking-wide">{{ restaurant()->name }}</div>
            <div class="flex flex-row items-center gap-2 mt-0.5">

                    <span class="text-base text-gray-500 font-medium"><span class="font-bold text-gray-800">
                        {{ (isOrderPrefixEnabled()) ? $formattedOrderNumber : __('modules.order.orderNumber') . ' #' . $orderNumber }}
                    </span></span>

                @if($orderType)
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide ml-2"
                        style="background-color: #e0e7ff; color: #3730a3;">
                        {{ ucwords(str_replace('_', ' ', __("modules.order.$orderType"))) }}
                    </span>
                @endif
            </div>
        </div>
        @if(pusherSettings()->is_enabled_pusher_broadcast)
            <div class="fixed top-4 right-4 z-50 flex items-center gap-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium shadow-md">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span> @lang('app.realTime')</span>
            </div>
        @endif
        <!-- Item List -->
        <div id="customer-display-items-list" class="flex-1 min-h-0 w-full flex flex-col gap-0 overflow-y-auto" data-item-count="{{ count($orderItems) }}">
            @php
                $customerDisplayEuSelectable = restaurant()->selectableEuAllergenKeys();
                $customerDisplayEuEnabled = count($customerDisplayEuSelectable) > 0;
            @endphp
            <!-- Heading Row -->
            <div class="sticky top-0 z-10 flex items-center px-3 py-1 bg-gray-50 border-b border-gray-200 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                <div class="w-7 text-center shrink-0">#</div>
                <div class="w-10 text-center shrink-0">@lang('modules.order.qty')</div>
                <div class="flex-1 pl-2 min-w-0">@lang('modules.menu.itemName')</div>
                <div class="w-20 text-right shrink-0">@lang('modules.order.amount')</div>
            </div>
            @if(count($orderItems) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($orderItems as $index => $item)
                        <div class="flex items-start px-3 py-1.5 bg-white" data-cd-item wire:key="cd-item-{{ $index }}-{{ md5(($item['name'] ?? '') . data_get($item, 'variation.name', '')) }}">
                            <div class="w-7 shrink-0 flex justify-center pt-0.5">
                                <span class="text-xs font-medium text-gray-400">{{ $index + 1 }}</span>
                            </div>
                            <div class="w-10 shrink-0 flex justify-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-skin-base text-white text-xs font-bold">{{ $item['qty'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0 pl-2">
                                <div class="flex items-center gap-1 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-900 leading-tight">{{ $item['name'] }}</span>
                                    @if($item['variation'] && $item['variation']['name'])
                                        <span class="text-[10px] text-blue-700 font-semibold bg-blue-50 px-1.5 py-0 rounded">{{ $item['variation']['name'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($item['modifiers']))
                                    <div class="flex flex-wrap gap-0.5 mt-0.5">
                                        @foreach($item['modifiers'] as $mod)
                                            <span class="inline-block bg-green-50 text-green-700 text-[10px] px-1 py-0 rounded">{{ $mod['name'] }}@if(isset($mod['price'])) ({{ currency_format($mod['price'], restaurant()->currency_id) }})@endif</span>
                                        @endforeach
                                    </div>
                                @endif
                                @php
                                    $cdDietary = \App\Support\DietaryLabels::normalize(
                                        array_filter((array) ($item['dietary_labels'] ?? []), 'is_string')
                                    );
                                @endphp
                                @if (count($cdDietary) > 0)
                                    <div class="mt-0.5 flex flex-wrap items-center gap-0.5"
                                        role="group"
                                        aria-label="{{ __('modules.menu.dietaryLabelsSectionTitle') }}">
                                        @foreach ($cdDietary as $cdDk)
                                            @php
                                                $cdDietaryLabel = __(\App\Support\DietaryLabels::langKey($cdDk));
                                            @endphp
                                            <span class="inline-flex max-w-full items-center gap-0.5 rounded border border-emerald-200/85 bg-emerald-50/90 px-1 py-0 dark:border-emerald-700/50 dark:bg-emerald-950/40">
                                                <img src="{{ \App\Support\DietaryLabels::defaultIconUrl($cdDk) }}"
                                                    alt=""
                                                    class="h-3 w-3 shrink-0 object-contain"
                                                    width="12"
                                                    height="12"
                                                    loading="lazy" />
                                                <span class="truncate text-[10px] font-medium leading-tight text-emerald-900 dark:text-emerald-100">{{ $cdDietaryLabel }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($customerDisplayEuEnabled && !empty($item['eu_allergen_keys']))
                                    @php
                                        $cdRowAllergens = array_values(array_unique(array_intersect(
                                            \App\Support\EuAnnexIiAllergens::keys(),
                                            $customerDisplayEuSelectable,
                                            array_filter((array) $item['eu_allergen_keys'], 'is_string')
                                        )));
                                    @endphp
                                    @if (count($cdRowAllergens) > 0)
                                        <div class="mt-1 rounded border border-amber-200/90 bg-amber-50 dark:border-amber-700/60 dark:bg-amber-950/35 px-2 py-1"
                                            role="region"
                                            aria-label="{{ __('modules.settings.euAllergensCustomerDisplayHeading') }}">
                                            <div class="text-[10px] font-semibold uppercase tracking-wide text-amber-900 dark:text-amber-200 mb-0.5">
                                                {{ __('modules.settings.euAllergensCustomerDisplayHeading') }}
                                            </div>
                                            <ul class="m-0 flex list-none flex-col gap-0.5 p-0">
                                                @foreach ($cdRowAllergens as $cdAllergenKey)
                                                    @php
                                                        $cdAllergenLabel = __(\App\Support\EuAnnexIiAllergens::langKey($cdAllergenKey));
                                                    @endphp
                                                    <li class="flex items-center gap-1.5">
                                                        <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center" aria-hidden="true">
                                                            <img src="{{ \App\Support\EuAnnexIiAllergens::defaultIconUrl($cdAllergenKey) }}"
                                                                alt=""
                                                                class="max-h-3.5 max-w-3.5 object-contain"
                                                                width="14"
                                                                height="14"
                                                                loading="lazy" />
                                                        </span>
                                                        <span class="flex-1 text-[10px] font-medium leading-tight text-gray-900 dark:text-gray-100">{{ $cdAllergenLabel }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endif
                                @if($item['notes'])
                                    <div class="text-[10px] text-gray-400 italic mt-0.5 leading-tight">@lang('modules.order.note'): {{ $item['notes'] }}</div>
                                @endif
                            </div>
                            <div class="w-20 shrink-0 flex flex-col items-end">
                                <span class="text-sm font-bold text-gray-800 leading-tight">{{ currency_format($item['qty'] * ($item['total_unit_price'] ?? $item['price']), restaurant()->currency_id) }}</span>
                                <span class="text-[10px] text-gray-400 leading-tight">@lang('modules.order.price'): {{ currency_format($item['total_unit_price'] ?? $item['price'], restaurant()->currency_id) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 bg-white rounded-lg">
                    <svg class="w-14 h-14 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <div class="text-gray-300 text-lg">@lang('messages.noItemAdded')</div>
                </div>
            @endif
        </div>

        <!-- Summary (sticky at bottom) -->
        <div class="shrink-0 w-full border-t border-gray-200 bg-gray-50 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
            <div class="bg-white rounded-t-lg px-4 py-3 flex flex-col gap-1.5 text-base shadow-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">@lang('modules.order.subTotal')</span>
                    <span class="font-semibold text-gray-700">{{ currency_format($subTotal, restaurant()->currency_id) }}</span>
                </div>
                @if($discount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-500">@lang('modules.order.discount')</span>
                    <span class="text-green-600">-{{ currency_format($discount, restaurant()->currency_id) }}</span>
                </div>
                @endif
                @if(count($orderItems) > 0 && !empty($taxes))
                    @foreach($taxes as $tax)
                        @if(isset($tax['amount']) && (float) $tax['amount'] > 0)
                        <div class="flex justify-between text-base">
                            <span class="text-gray-500">@lang('modules.order.tax'): {{ $tax['name'] }} ({{ $tax['percent'] }}%)</span>
                            <span class="text-blue-600">+{{ currency_format($tax['amount'], restaurant()->currency_id) }}</span>
                        </div>
                        @endif
                    @endforeach
                @endif
                @if(count($orderItems) > 0 && !empty($extraCharges))
                    @foreach($extraCharges as $charge)
                        @if(isset($charge['amount']) && (float) $charge['amount'] > 0)
                        <div class="flex justify-between text-base">
                            <span class="text-gray-500">@lang('modules.order.charge'): {{ $charge['name'] }}</span>
                            <span class="text-orange-600">+{{ currency_format($charge['amount'], restaurant()->currency_id) }}</span>
                        </div>
                        @endif
                    @endforeach
                @endif
                @if($tip > 0)
                <div class="flex justify-between text-base">
                    <span class="text-gray-500">@lang('modules.order.tip')</span>
                    <span class="text-blue-600">+{{ currency_format($tip, restaurant()->currency_id) }}</span>
                </div>
                @endif
                @if($deliveryFee > 0)
                <div class="flex justify-between text-base">
                    <span class="text-gray-500">@lang('modules.order.deliveryFee')</span>
                    <span class="text-blue-600">+{{ currency_format($deliveryFee, restaurant()->currency_id) }}</span>
                </div>
                @endif
                <div class="border-t border-dashed border-gray-200 my-1.5"></div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-xl text-gray-900">@lang('modules.order.total')</span>
                    <span class="font-bold text-2xl text-skin-base">{{ currency_format($total, restaurant()->currency_id) }}</span>
                </div>
            </div>

            <div class="w-full text-center py-2 px-4 bg-gray-50">
                <div class="text-base text-gray-400 font-medium">@lang('modules.order.thankYouMessage', ['restaurant' => restaurant()->name])</div>
                <div class="text-xs text-gray-300 mt-0.5">@lang('modules.order.pleaseReviewOrder')</div>
            </div>
        </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        (function () {
            let prevItemCount = 0;

            function getItemCount() {
                const list = document.getElementById('customer-display-items-list');
                if (!list) {
                    return 0;
                }
                return parseInt(list.getAttribute('data-item-count') || '0', 10);
            }

            function scrollToLatestItem() {
                const list = document.getElementById('customer-display-items-list');
                if (!list) {
                    return;
                }
                const count = getItemCount();
                if (count > prevItemCount) {
                    requestAnimationFrame(function () {
                        list.scrollTop = list.scrollHeight;
                    });
                }
                prevItemCount = count;
            }

            function initItemCount() {
                prevItemCount = getItemCount();
            }

            document.addEventListener('DOMContentLoaded', initItemCount);

            document.addEventListener('livewire:init', function () {
                Livewire.hook('morph.updated', function () {
                    scrollToLatestItem();
                });
            });
        })();
    </script>

    @if(pusherSettings()->is_enabled_pusher_broadcast)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const userId = {{ auth()->id() }};
                const channelName = 'customer-display-user-' + userId;
                const channel = PUSHER.subscribe(channelName);
                channel.bind('customer-display.updated', function(data) {
                    @this.call('refreshCustomerDisplay');
                    console.log('✅ Pusher received data for customer display user ' + userId + '!. Refreshing...');
                });
                PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher connected for Customer Display!');
                });
                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to ' + channelName + ' channel!');
                });
            });
        </script>
    @endif
@endpush
