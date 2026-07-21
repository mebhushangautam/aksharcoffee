{{-- Customer-site order type picker (Livewire). Expects $orderTypes collection and $selectedOrderTypeId int. --}}
@php
    $pickerOrderTypes = collect($orderTypes ?? [])->reject(
        fn ($ot) => ($ot->type ?? null) === 'room_service' || ($ot->slug ?? null) === 'room_service'
    );
@endphp

@if ($pickerOrderTypes->count() > 1)
    <div class="px-4 mt-3">
        <div class="rounded-xl border border-gray-100 bg-skin-base/[0.08] p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="mb-3 text-center sm:text-left">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                    @lang('modules.order.selectOrderType')
                </h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    @lang('modules.order.selectOrderTypeWithDeliveryDescription')
                </p>
            </div>
            <div @class([
                'grid gap-2 sm:gap-3',
                'grid-cols-2' => $pickerOrderTypes->count() === 2,
                'grid-cols-3' => $pickerOrderTypes->count() === 3,
                'grid-cols-2 sm:grid-cols-3' => $pickerOrderTypes->count() > 3,
            ])>
                @foreach ($pickerOrderTypes as $orderType)
                    @php
                        $isActive = (int) $selectedOrderTypeId === (int) $orderType->id;
                        $typeDescription = match ($orderType->type) {
                            'dine_in' => __('messages.dineInDescription'),
                            'delivery' => __('messages.deliveryDescription'),
                            'pickup' => __('messages.pickupDescription'),
                            default => '',
                        };
                    @endphp
                    <button type="button"
                        wire:click="selectOrderTypeFromModal({{ $orderType->id }})"
                        @class([
                            'group flex flex-col items-center justify-center rounded-lg border p-3 sm:p-4 text-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-skin-base/40',
                            'bg-skin-base border-skin-base text-white shadow-md' => $isActive,
                            'bg-white border-gray-200 text-gray-800 hover:border-skin-base hover:shadow-md dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100 dark:hover:border-skin-base' => ! $isActive,
                        ])>
                        <span @class([
                            'mb-2 flex h-10 w-10 items-center justify-center rounded-full transition-colors',
                            'bg-white/20 text-white' => $isActive,
                            'bg-gray-50 text-gray-600 group-hover:bg-skin-base/[0.15] group-hover:text-skin-base dark:bg-gray-700 dark:text-gray-300' => ! $isActive,
                        ])>
                            @if ($orderType->type === 'dine_in')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            @elseif ($orderType->type === 'delivery')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            @elseif ($orderType->type === 'pickup')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            @endif
                        </span>
                        <span class="text-xs font-semibold leading-tight sm:text-sm">
                            {{ $orderType->translated_name }}
                        </span>
                        @if ($typeDescription !== '')
                            <span @class([
                                'mt-1 max-w-full text-[10px] leading-snug line-clamp-3 sm:text-xs',
                                'text-white/90' => $isActive,
                                'text-gray-500 dark:text-gray-400' => ! $isActive,
                            ])>
                                {{ $typeDescription }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endif
