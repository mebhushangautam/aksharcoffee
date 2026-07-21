@php
    $deliveryPlatformRows = !empty($posDeliveryPlatformsForModal)
        ? $posDeliveryPlatformsForModal
        : collect($deliveryPlatforms ?? [])->map(function ($platform) {
            return [
                'id' => (int) $platform->id,
                'name' => (string) $platform->name,
                'logo_url' => $platform->logo_url ?? null,
            ];
        })->all();

    $deliveryPlatformsList = collect($deliveryPlatformRows)
        ->map(fn ($platform) => (object) (is_array($platform) ? $platform : [
            'id' => (int) $platform->id,
            'name' => (string) $platform->name,
            'logo_url' => $platform->logo_url ?? null,
        ]))
        ->filter(fn ($platform) => !empty($platform->id))
        ->values();

    $deliveryOrderType = collect($orderTypes ?? [])->firstWhere('slug', 'delivery');
    $deliveryTilesOnMainGrid = $deliveryOrderType && $deliveryPlatformsList->isNotEmpty();
    $otmTypeTileClass = 'pos-otm-non-delivery group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-skin-base/50 hover:bg-skin-base/5 transition-all cursor-pointer min-h-[5.25rem]';
    $otmDeliveryTileClass = 'pos-otm-delivery-tile group flex flex-col items-center justify-center gap-1.5 p-3.5 rounded-xl border-2 border-skin-base/20 dark:border-skin-base/30 bg-skin-base/[0.04] dark:bg-skin-base/10 hover:border-skin-base/50 hover:bg-skin-base/10 transition-all cursor-pointer min-h-[5rem]';
    $otmTypeIconWrap = 'flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-900 text-skin-base group-hover:bg-skin-base/10';
    $otmDeliveryIconWrap = 'flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-800 overflow-hidden';
@endphp
{{-- Client-side order type + delivery platform picker (no Livewire). Shown/hidden via window.showPosOrderTypeModal / hidePosOrderTypeModal. --}}
<div id="pos-order-type-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" style="display: none;">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-xl max-h-[88vh] flex flex-col overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h2 id="pos-otm-title" class="text-lg font-bold text-gray-900 dark:text-white truncate">
                        @lang('modules.order.selectOrderType')
                    </h2>
                    <p id="pos-otm-description" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @if($deliveryTilesOnMainGrid)
                            @lang('modules.order.selectOrderTypeWithDeliveryDescription')
                        @else
                            @lang('modules.order.selectOrderTypeDescription')
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" id="pos-otm-back-btn" onclick="window.posOrderTypeModalGoBack()" class="hidden p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="@lang('modules.order.backToOrderTypes')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
                    </button>
                    <a href="{{ route('pos.index') }}" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="@lang('modules.order.goToPOS')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
                    </a>
                    <a href="{{ route('dashboard') }}" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="@lang('menu.dashboard')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/></svg>
                    </a>
                    <a href="{{ route('orders.index') }}" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300" title="@lang('menu.orders')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 512.005 512.005"><g><g><rect y="389.705" width="512.005" height="66.607"></rect></g></g><g><g><path d="M297.643,131.433c4.862-7.641,7.693-16.696,7.693-26.404c0-27.204-22.132-49.336-49.336-49.336 c-27.204,0-49.336,22.132-49.336,49.337c0,9.708,2.831,18.763,7.693,26.404C102.739,149.772,15.208,240.563,1.801,353.747h508.398 C496.792,240.563,409.261,149.772,297.643,131.433z M256,118.415c-7.38,0-13.384-6.005-13.384-13.385S248.62,91.646,256,91.646 s13.384,6.004,13.384,13.384S263.38,118.415,256,118.415z"></path></g></g></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-4">
            <p class="mb-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                Changing order type may change price and options
            </p>

            <label class="mb-4 flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/60 px-3 py-2.5 cursor-pointer">
                <input type="checkbox" id="pos-set-order-type-default" class="h-4 w-4 rounded border-gray-300 text-skin-base focus:ring-skin-base">
                <div class="text-sm leading-tight text-gray-800 dark:text-gray-100">
                    <div class="font-semibold">{{ __('modules.order.setAsDefault') }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('modules.order.skipThisSelectionNextTime') }}</div>
                </div>
            </label>

            <div id="pos-otm-stage-types">
                <div id="pos-otm-order-types-section">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @forelse (($orderTypes ?? []) as $orderType)
                            @if($orderType->slug === 'delivery' && $deliveryTilesOnMainGrid)
                                @continue
                            @endif

                            <button type="button"
                                onclick='window.posOrderTypeModalPickType({{ (int) $orderType->id }}, @json($orderType->slug))'
                                class="{{ $otmTypeTileClass }}">
                                <div class="{{ $otmTypeIconWrap }}">
                                    @if($orderType->slug === 'dine_in')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/></svg>
                                    @elseif($orderType->slug === 'delivery')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/></svg>
                                    @elseif($orderType->slug === 'pickup')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24" height="24" fill="currentColor"><path d="M24 46a21.9 21.9 0 0 1-6.124-.865 1 1 0 0 1-.718-.889l-.92-12.915a1 1 0 0 1 .731-1.035A5.51 5.51 0 0 0 21 25c0-3.263-1.345-10-5.5-10S10 21.737 10 25a5.51 5.51 0 0 0 4.031 5.3 1 1 0 0 1 .731 1.035L14 41.966a1 1 0 0 1-1.522.781A22 22 0 1 1 46 24a21.87 21.87 0 0 1-10.48 18.747 1 1 0 0 1-1.52-.781l-.86-12.029a1 1 0 0 1 .77-1.046A3.98 3.98 0 0 0 37 25V15a1 1 0 0 1 2 0v10a5.97 5.97 0 0 1-3.812 5.584l.681 9.518A20 20 0 1 0 4 24a19.86 19.86 0 0 0 8.131 16.1l.581-8.144A7.52 7.52 0 0 1 8 25c0-4.64 2.036-12 7.5-12S23 20.36 23 25a7.52 7.52 0 0 1-4.712 6.958L19.1 43.4a20.24 20.24 0 0 0 9.794 0l.915-12.812A5.97 5.97 0 0 1 26 25V15a1 1 0 0 1 2 0v10a3.98 3.98 0 0 0 3.092 3.891 1 1 0 0 1 .77 1.046l-1.02 14.309a1 1 0 0 1-.718.889A21.9 21.9 0 0 1 24 46"/></svg>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white text-center">{{ $orderType->translated_name }}</span>
                            </button>
                        @empty
                            <div class="col-span-full text-center py-8">
                                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('modules.order.noOrderTypesAvailable')</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($deliveryTilesOnMainGrid && $deliveryOrderType)
                    <div id="pos-otm-delivery-section" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-skin-base/10 text-skin-base">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $deliveryOrderType->translated_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.order.selectDeliveryPlatformDescription')</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <button type="button"
                                onclick="window.posOrderTypeModalPickDelivery({{ (int) $deliveryOrderType->id }}, 'default')"
                                class="{{ $otmDeliveryTileClass }}">
                                <div class="{{ $otmDeliveryIconWrap }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/></svg>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.order.delivery')</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white text-center leading-tight">{{ __('modules.order.defaultDeliveryPlatform') }}</span>
                            </button>

                            @foreach($deliveryPlatformsList as $platform)
                                <button type="button"
                                    onclick="window.posOrderTypeModalPickDelivery({{ (int) $deliveryOrderType->id }}, {{ (int) $platform->id }})"
                                    class="{{ $otmDeliveryTileClass }}">
                                    <div class="{{ $otmDeliveryIconWrap }}">
                                        @if(!empty($platform->logo_url))
                                            <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" class="h-9 w-9 object-contain" loading="lazy"/>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.6.8l3 4A2 2 0 0 1 15.5 7H14v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11 0H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7h2.5a1 1 0 0 0 .8-1.6l-3-4A1 1 0 0 0 11 3"/></svg>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">@lang('modules.order.delivery')</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white text-center leading-tight line-clamp-2">{{ $platform->name }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div id="pos-otm-stage-platforms" class="hidden">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @if($deliveryOrderType)
                        <button type="button" onclick="window.posOrderTypeModalPickDelivery({{ (int) $deliveryOrderType->id }}, 'default')" class="{{ $otmDeliveryTileClass }}">
                            <div class="{{ $otmDeliveryIconWrap }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white text-center">{{ __('modules.order.defaultDeliveryPlatform') }}</span>
                        </button>
                        @foreach($deliveryPlatformsList as $platform)
                            <button type="button" onclick="window.posOrderTypeModalPickDelivery({{ (int) $deliveryOrderType->id }}, {{ (int) $platform->id }})" class="{{ $otmDeliveryTileClass }}">
                                <div class="{{ $otmDeliveryIconWrap }}">
                                    @if(!empty($platform->logo_url))
                                        <img src="{{ $platform->logo_url }}" alt="{{ $platform->name }}" class="h-9 w-9 object-contain" loading="lazy"/>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="text-skin-base" viewBox="0 0 16 16"><path d="M0 3a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.6.8l3 4A2 2 0 0 1 15.5 7H14v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11 0H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7h2.5a1 1 0 0 0 .8-1.6l-3-4A1 1 0 0 0 11 3"/></svg>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white text-center line-clamp-2">{{ $platform->name }}</span>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
