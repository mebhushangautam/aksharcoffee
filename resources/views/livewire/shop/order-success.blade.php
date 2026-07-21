<div class="px-4 space-y-6" @if(pusherSettings()->is_enabled_pusher_broadcast) wire:poll.10s @endif>

    <h2 class="text-xl font-bold dark:text-white inline-flex gap-2 items-center text-green-600">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-patch-check text-green-600" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
            <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
        </svg>

        @lang('messages.orderPlacedSuccess')
    </h2>


    <div >
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3 cursor-pointer">
            <div class="flex items-center min-w-0">
                <div class="space-y-2">
                    <p class="flex flex-wrap items-center gap-2 font-medium text-gray-900 dark:text-white">

                            {{ $order->show_formatted_order_number }}


                        @if ($order->status == 'kot')
                            <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-400 border border-yellow-400 text-xs font-medium px-2 py-1 rounded uppercase tracking-wide whitespace-nowrap">
                                @lang('modules.order.infokot')
                            </span>
                        @endif
                    </p>
                    <div class="flex items-center flex-1 text-xs text-gray-500">
                        {{ $order->items->count() }} @lang('modules.menu.item') | {{
                        $order->date_time->timezone(timezone())->translatedFormat($dateFormat . ' ' . $timeFormat) }}
                    </div>

                    @php
                        $maxPreparationTime = $order->items->max(function($item) {
                            return $item->menuItem->preparation_time;
                        });
                    @endphp

                    @if ($maxPreparationTime)
                        <div class="text-xs font-normal text-gray-500 dark:text-gray-400 max-w-56 items-center inline-flex my-1">
                            @lang('modules.menu.preparationTime') :
                            {{ $maxPreparationTime }} @lang('modules.menu.minutes') (@lang('app.approx'))
                        </div>
                    @endif
                </div>
            </div>
            <div class="inline-flex w-full flex-col text-left text-base font-semibold text-gray-900 dark:text-white sm:w-auto sm:text-right">
                <div>{{ currency_format($order->total, $restaurant->currency_id) }}</div>
                <div class="text-xs text-gray-500 font-light">@lang('modules.order.includeTax')</div>
            </div>
        </div>

        <div
            class="w-full divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200 dark:divide-gray-700 dark:border-gray-700">
            @php
                $orderSuccessEuSelectable = $restaurant ? $restaurant->selectableEuAllergenKeys() : [];
                $orderSuccessEuEnabled = count($orderSuccessEuSelectable) > 0;
            @endphp
            @foreach ($order->items as $key => $item)
            <div class="space-y-4 p-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                    <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                        <a class="shrink-0">
                            <img class="w-12 h-12 rounded-md object-cover shadow-sm" src="{{ $item->menuItem->item_photo_url }}"
                                alt="{{ $item->menuItem->item_name }}" />
                        </a>

                        <a class="min-w-0 flex-1 flex flex-col font-medium text-gray-900 dark:text-white">
                            <div class="inline-flex items-center text-gray-900 dark:text-white break-words">
                                {{ $item->menuItem->item_name }}
                            </div>
                            <div class="inline-flex items-center text-xs text-gray-600 dark:text-white break-words">
                                {{ (isset($item->menuItemVariation) ? $item->menuItemVariation->variation : '')
                                }}
                            </div>
                            @if($item->modifierOptions->isNotEmpty())
                            <div class="text-xs text-gray-600 dark:text-white">
                                @foreach ($item->modifierOptions as $modifier)
                                <div class="mb-1 flex items-start justify-between gap-2 rounded-md border-l-2 border-blue-500 bg-gray-200 px-1 py-0.5 text-xs dark:bg-gray-800">
                                    <span class="break-words text-gray-900 dark:text-white">{{ $modifier->name }}</span>
                                    <span class="whitespace-nowrap text-gray-600 dark:text-gray-300">{{ currency_format($modifier->price, $restaurant->currency_id) }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @if ($item->menuItem->preparation_time)
                                <div class="text-xs font-normal text-gray-500 dark:text-gray-400 max-w-56 items-center inline-flex my-1">
                                    @lang('modules.menu.preparationTime') :
                                    {{ $item->menuItem->preparation_time }} @lang('modules.menu.minutes')
                                </div>
                            @endif

                            @if ($orderSuccessEuEnabled && !empty($item->menuItem->eu_allergen_keys))
                                @php
                                    $osLineAllergens = array_values(array_unique(array_intersect(
                                        \App\Support\EuAnnexIiAllergens::keys(),
                                        $orderSuccessEuSelectable,
                                        array_filter((array) $item->menuItem->eu_allergen_keys, 'is_string')
                                    )));
                                @endphp
                                @if (count($osLineAllergens) > 0)
                                    <div class="mt-1.5 flex w-full min-w-0 flex-wrap gap-1.5"
                                        role="group"
                                        aria-label="{{ __('modules.settings.euAllergensCustomerDisplayHeading') }}">
                                        @foreach ($osLineAllergens as $osAllergenKey)
                                            @php
                                                $osAllergenLabel = __(\App\Support\EuAnnexIiAllergens::langKey($osAllergenKey));
                                            @endphp
                                            <span class="inline-flex max-w-full items-center gap-1.5 rounded-md border border-amber-200/85 bg-amber-50/95 px-2 py-1 dark:border-amber-700/50 dark:bg-amber-950/35">
                                                <img src="{{ \App\Support\EuAnnexIiAllergens::defaultIconUrl($osAllergenKey) }}"
                                                    alt=""
                                                    class="h-4 w-4 shrink-0 object-contain"
                                                    width="16"
                                                    height="16"
                                                    loading="lazy" />
                                                <span class="max-w-[12rem] truncate text-xs font-medium leading-tight text-gray-800 dark:text-gray-100 sm:max-w-[14rem]">{{ $osAllergenLabel }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            @endif

                            @php
                                $osLineDietary = \App\Support\DietaryLabels::normalize(
                                    is_array($item->menuItem->dietary_labels ?? null) ? $item->menuItem->dietary_labels : []
                                );
                            @endphp
                            @if (count($osLineDietary) > 0)
                                <div class="mt-1.5 flex w-full min-w-0 flex-wrap gap-1.5"
                                    role="group"
                                    aria-label="{{ __('modules.menu.dietaryLabelsSectionTitle') }}">
                                    @foreach ($osLineDietary as $osDietaryKey)
                                        @php
                                            $osDietaryLabel = __(\App\Support\DietaryLabels::langKey($osDietaryKey));
                                        @endphp
                                        <span class="inline-flex max-w-full items-center gap-1.5 rounded-md border border-emerald-200/85 bg-emerald-50/95 px-2 py-1 dark:border-emerald-700/50 dark:bg-emerald-950/35">
                                            <img src="{{ \App\Support\DietaryLabels::defaultIconUrl($osDietaryKey) }}"
                                                alt=""
                                                class="h-4 w-4 shrink-0 object-contain"
                                                width="16"
                                                height="16"
                                                loading="lazy" />
                                            <span class="max-w-[12rem] truncate text-xs font-medium leading-tight text-emerald-900 dark:text-emerald-100 sm:max-w-[14rem]">{{ $osDietaryLabel }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </a>
                    </div>

                    <div class="flex items-center justify-between gap-3 sm:justify-end sm:gap-4">
                        <p class="text-sm font-normal text-gray-900 dark:text-white">x{{ $item->quantity }}</p>

                        <div class="flex flex-col items-end gap-1">
                            @if($taxMode === 'item' && $restaurant?->tax_inclusive && $item->tax_amount > 0)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ currency_format(($item->price + $item->modifierOptions->sum('price')) - ($item->tax_amount / $item->quantity), $restaurant->currency_id) }} + tax
                                </div>
                            @endif
                            <p class="text-lg font-medium leading-tight text-gray-900 dark:text-white">
                                {{ currency_format($item->price + $item->modifierOptions->sum('price'), $restaurant->currency_id) }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach

            <div class="space-y-4 bg-gray-50 p-3 dark:bg-gray-800">
                <div class="space-y-2">
                    <dl class="flex items-center justify-between gap-4">
                        <dt class="font-normal text-gray-500 dark:text-gray-400"> @lang('modules.order.subTotal')</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ currency_format($order->sub_total, $restaurant->currency_id) }}</dd>
                    </dl>

                    @if ($order->discount_amount && !($order->loyalty_points_redeemed ?? 0))
                        <dl class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <dt class="font-normal">@lang('modules.order.discount')</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">-{{ currency_format($order->discount_amount, $restaurant->currency_id) }}</dd>
                        </dl>
                    @endif

                    @if (($order->loyalty_points_redeemed ?? 0) > 0 && ($order->loyalty_discount_amount ?? 0) > 0)
                        <dl class="flex items-center justify-between gap-4 text-sm text-green-600 dark:text-green-400">
                            <dt class="font-normal">
                                @lang('app.loyaltyDiscount')
                                <span class="text-gray-500 dark:text-gray-400 font-normal">
                                    ({{ number_format($order->loyalty_points_redeemed, 0) }} @lang('app.points'))
                                </span>
                            </dt>
                            <dd class="font-medium">-{{ currency_format($order->loyalty_discount_amount, $restaurant->currency_id) }}</dd>
                        </dl>
                    @endif

                    @php
                        // Calculate net for charges display
                        $net = $order->sub_total - ($order->discount_amount ?? 0);

                        // Use saved tax_base from database
                        $taxBase = $order->tax_base ?? ($net + $order->charges->sum(fn($item) => $item->charge->getAmount($net)));
                    @endphp

                    @foreach ($order->charges as $item)
                    <div class="flex justify-between text-gray-500 text-sm dark:text-gray-400">
                        <div class="inline-flex items-center gap-x-1">
                            {{ $item->charge->charge_name }}
                            @if ($item->charge->charge_type == 'percent')
                                ({{ $item->charge->charge_value }}%)
                            @endif
                        </div>
                        <div>
                            {{ currency_format($item->charge->getAmount($net), $restaurant->currency_id) }}
                        </div>
                    </div>
                    @endforeach

                    @foreach ($order->taxes as $item)
                    <dl class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <dt class="font-normal">{{ $item->tax->tax_name }} ({{ $item->tax->tax_percent }}%)</dt>
                        <dd class="text-sm font-medium ">{{ currency_format(($item->tax->tax_percent / 100) * $taxBase, $restaurant->currency_id) }}</dd>
                    </dl>
                    @endforeach

                    @if($order->tip_amount > 0)
                        <dl class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <dt class="font-normal">@lang('modules.order.tip')</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ currency_format($order->tip_amount, $restaurant->currency_id) }}</dd>
                        </dl>
                    @endif
                </div>

                <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                    <dt class="text-lg font-bold text-gray-900 dark:text-white">@lang('modules.order.total')</dt>
                    <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ currency_format($order->total, $restaurant->currency_id) }}</dd>
                </dl>
            </div>

        </div>
    </div>

    @if ($showLoyaltyOnSuccess && customer())
        <div wire:key="order-success-loyalty-{{ $order->id }}">
            @livewire('shop.order-detail', [
                'id' => $order->id,
                'restaurant' => $restaurant,
                'embedLoyaltyOnly' => true,
            ], key('order-success-loyalty-' . $order->id))
        </div>
    @endif

    <div class="flex">
        @if ($order->isFullyPaid())

            @if ($order->table_id)
                @php
                    $newOrderLink = route('table_order', [$order->table->hash]);
                @endphp
            @else
                @php
                    $newOrderLink = module_enabled('Subdomain') ? url('/?new_order=1') : route('shop_restaurant', ['hash' => $restaurant->hash, 'new_order' => 1]);
                @endphp
            @endif
        @else
            @php
                $newOrderLink = module_enabled('Subdomain')?url('/'):route('shop_restaurant',['hash' => $restaurant->hash]).'?current_order='.$order->id;
            @endphp
        @endif



        <x-primary-link class="inline-flex items-center mb-2" href="{{ $newOrderLink }}">
            @lang('modules.order.newOrder')
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </x-primary-link>

    </div>


</div>


@push('scripts')

    @if(pusherSettings()->is_enabled_pusher_broadcast)
        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const channel = PUSHER.subscribe('order-success');
                channel.bind('order-success.created', function(data) {
                    @this.call('refreshOrderSuccess');
                    new Audio("{{ asset('sound/new_order.wav')}}").play();
                    console.log('✅ Pusher received data for order success!. Refreshing...');
                });
                PUSHER.connection.bind('connected', () => {
                    console.log('✅ Pusher connected for Order Success!');
                });
                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Subscribed to order-success channel!');
                });
            });
        </script>
    @endif
@endpush
