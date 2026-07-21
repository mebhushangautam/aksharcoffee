                    @if($isLoyaltyEnabled ?? false)
                        @php
                            // Always prioritize order data from database (source of truth)
                            $displayLoyaltyPointsRedeemed = (float)($order->loyalty_points_redeemed ?? 0);
                            $displayLoyaltyDiscountAmount = (float)($order->loyalty_discount_amount ?? 0);

                            // Fallback to component variables only if order values are 0
                            if ($displayLoyaltyPointsRedeemed == 0) {
                                $displayLoyaltyPointsRedeemed = (float)($loyaltyPointsRedeemed ?? 0);
                            }
                            if ($displayLoyaltyDiscountAmount == 0) {
                                $displayLoyaltyDiscountAmount = (float)($loyaltyDiscountAmount ?? 0);
                            }
                        @endphp
                        @if($displayLoyaltyPointsRedeemed > 0 && $displayLoyaltyDiscountAmount > 0)
                            <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3">
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-green-900 dark:text-green-200">
                                                {{ __('loyalty::app.loyaltyDiscountApplied') }}
                                            </p>
                                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                                {{ number_format($displayLoyaltyPointsRedeemed, 0) }} @lang('loyalty::app.points') = {{ currency_format($displayLoyaltyDiscountAmount, $restaurant->currency_id) }}
                                            </p>
                                        </div>
                                        @php
                                            $isPaid = $order->isFullyPaid();
                                        @endphp
                                        @if(!$isPaid)
                                            <x-danger-button type="button" wire:click="removeLoyaltyRedemption" size="sm" wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="removeLoyaltyRedemption">{{ __('loyalty::app.remove') }}</span>
                                                <span wire:loading wire:target="removeLoyaltyRedemption">...</span>
                                            </x-danger-button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @php
                        // Display stamp redemption with remove button (similar to loyalty points)
                        $displayStampDiscountAmount = (float)($order->stamp_discount_amount ?? 0);
                        if ($displayStampDiscountAmount == 0) {
                            $displayStampDiscountAmount = (float)($stampDiscountAmount ?? 0);
                        }
                        $hasFreeStampItems = $order->items()->where('is_free_item_from_stamp', true)->exists();
                    @endphp
                    @if(($isStampsEnabledForCustomerSite ?? false) && ($displayStampDiscountAmount > 0 || $hasFreeStampItems))
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3">
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-green-900 dark:text-green-200">
                                            {{ __('loyalty::app.stampRedemptionApplied') }}
                                        </p>
                                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                            @if($hasFreeStampItems && $displayStampDiscountAmount > 0)
                                                @lang('app.freeItem') + {{ currency_format($displayStampDiscountAmount, $restaurant->currency_id) }} @lang('app.discount')
                                            @elseif($hasFreeStampItems)
                                                @lang('app.freeItem')
                                            @else
                                                {{ currency_format($displayStampDiscountAmount, $restaurant->currency_id) }} @lang('app.discount')
                                            @endif
                                        </p>
                                    </div>
                                    @php
                                        $isPaid = $order->isFullyPaid();
                                        $isCancelled = in_array($order->status, ['canceled', 'cancelled'], true)
                                            || (($order->order_status->value ?? null) === 'cancelled');
                                    @endphp
                                    @if(!$isPaid)
                                        <x-danger-button type="button" wire:click="removeStampRedemption" size="sm" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="removeStampRedemption">{{ __('loyalty::app.remove') }}</span>
                                            <span wire:loading wire:target="removeStampRedemption">...</span>
                                        </x-danger-button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!(in_array($order->status, ['canceled', 'cancelled'], true)
                            || (($order->order_status->value ?? null) === 'cancelled')))
                        <!-- Loyalty Information Display (after order placed) -->
                        @if(($isPointsEnabledForCustomerSite ?? false) && ($customer ?? null))
                            <!-- Loyalty Points Summary -->
                            @if(($availableLoyaltyPoints ?? 0) > 0 || isset($currentTier))
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200">{{ __('loyalty::app.loyaltyAccount') }}</h3>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-blue-700 dark:text-blue-300">{{ __('loyalty::app.pointsBalance') }}</div>
                                                <div class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($availableLoyaltyPoints ?? 0) }}</div>
                                            </div>
                                        </div>

                                        <!-- Tier Display -->
                                        @if(isset($currentTier) && $currentTier)
                                            @include('loyalty::components.tier-display', [
                                                'currentTier' => $currentTier,
                                                'nextTier' => $nextTier ?? null,
                                                'pointsToNextTier' => $pointsToNextTier ?? null,
                                                'tierProgress' => $tierProgress ?? 0,
                                                'availableLoyaltyPoints' => $availableLoyaltyPoints ?? 0
                                            ])
                                        @elseif(($availableLoyaltyPoints ?? 0) > 0)
                                            <div class="text-xs text-blue-700 dark:text-blue-300 mt-2">
                                                {{ __('loyalty::app.noTierAssigned') }}
                                            </div>
                                        @endif
                                    </div>
                            @endif
                        @endif

                        @if(($isPointsEnabledForCustomerSite ?? false) && ($customer ?? null))
                            @php
                                // Check if points are already redeemed (from database or component)
                                $hasRedeemedPoints = ($order->loyalty_points_redeemed ?? 0) > 0 || ($loyaltyPointsRedeemed ?? 0) > 0;
                            @endphp
                            @if($customer && ($availableLoyaltyPoints ?? 0) > 0 && !$order->isFullyPaid() && !$hasRedeemedPoints)
                                <div class="border-t border-gray-200 dark:border-gray-700" wire:key="loyalty-redemption-section">
                                    <div class="flex items-center justify-between gap-3 mt-2">
                                        <!-- Left Section: Icon and Text -->
                                        <div class="flex items-center gap-2">
                                            <div class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                                <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-gray-900 dark:text-white">
                                                    @lang('loyalty::app.redeemLoyaltyPoints')
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Right Section: Action Button -->
                                        <div class="flex items-center gap-2">
                                            <x-button
                                                wire:click="openLoyaltyRedemptionModal"
                                                class="flex items-center text-xs"
                                                wire:loading.attr="disabled">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>@lang('loyalty::app.redeem')</span>
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if(($isStampsEnabledForCustomerSite ?? false) && ($customer ?? null))
                            @php
                                // Check if stamps are already redeemed (from database or component)
                                // Check for discount amount OR free items
                                $hasStampDiscount = ($order->stamp_discount_amount ?? 0) > 0 || ($stampDiscountAmount ?? 0) > 0;
                                $hasFreeStampItems = $order->items()->where('is_free_item_from_stamp', true)->exists();
                                $hasRedeemedStamps = $hasStampDiscount || $hasFreeStampItems || (!empty($selectedStampRuleIds ?? []));
                                // Check if customer has redeemable stamps
                                $hasRedeemableStamps = false;
                                if (!empty($customerStamps)) {
                                    $hasRedeemableStamps = collect($customerStamps)->contains(function ($stampData) {
                                        return ($stampData['can_redeem'] ?? false) === true;
                                    });
                                }
                            @endphp
                            @if($customer && $hasRedeemableStamps && !$order->isFullyPaid() && !$hasRedeemedStamps)
                                <div class="border-t border-gray-200 dark:border-gray-700" wire:key="stamp-redemption-section">
                                    <div class="flex items-center justify-between gap-3 mt-2">
                                        <!-- Left Section: Icon and Text -->
                                        <div class="flex items-center gap-2">
                                            <div class="p-1.5 rounded-lg bg-green-50 dark:bg-green-900/20">
                                                <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="text-xs font-medium text-gray-900 dark:text-white">
                                                    @lang('loyalty::app.redeemStamps')
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Right Section: Action Button -->
                                        <div class="flex items-center gap-2">
                                            <x-button
                                                wire:click="openStampRedemptionModal"
                                                class="flex items-center text-xs"
                                                wire:loading.attr="disabled">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>@lang('loyalty::app.redeem')</span>
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif

