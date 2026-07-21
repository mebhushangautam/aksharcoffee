        <!-- Loyalty Points Redemption Modal -->
        @if($isPointsEnabledForCustomerSite ?? false)
        <x-dialog-modal wire:model.live="showLoyaltyRedemptionModal" maxWidth="md">
            <x-slot name="title">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('loyalty::app.redeemLoyaltyPoints') }}
                </div>
            </x-slot>

            <x-slot name="content">
                @if($customer && ($availableLoyaltyPoints ?? 0) > 0)
                    <div class="space-y-4">
                        <!-- Current Order Summary -->
                        <div class="p-4 mb-6 rounded-lg bg-gray-50 dark:bg-gray-700">
                            @php
                                // Ensure order has taxes loaded for calculation
                                if (!$order->relationLoaded('taxes')) {
                                    $order->load('taxes.tax');
                                }
                                if (!$order->relationLoaded('items')) {
                                    $order->load('items');
                                }

                                // Calculate preview total correctly:
                                // 1. Start with subtotal (use order->sub_total to match order detail page calculation)
                                // This ensures consistency with how taxes are calculated elsewhere
                                $previewSubTotal = (float)($order->sub_total ?? 0);

                                // 2. Apply regular discount (if any)
                                $previewSubTotal -= (float)($order->discount_amount ?? 0);

                                // 3. Apply loyalty discount (only to subtotal)
                                $previewDiscount = (float)($loyaltyDiscountAmount ?? 0);
                                $newSubTotal = $previewSubTotal - $previewDiscount;

                                // 4. Calculate taxes - FIRST calculate current tax, THEN calculate tax after loyalty discount
                                // Match the calculation method used in order detail page (line 438)
                                $currentTaxAmount = 0.0; // Tax on current subtotal (before loyalty discount)
                                $previewTaxAmount = 0.0; // Tax on new subtotal (after loyalty discount)
                                $newTaxAmountAfterDiscount = 0.0; // Tax amount after loyalty discount (for new total calculation)

                                if ($order->tax_mode === 'order' && $order->taxes && $order->taxes->count() > 0) {
                                    // IMPORTANT: Don't round individual tax amounts, only round the final sum
                                    // Ensure we process ALL taxes (no deduplication)
                                    // Make sure taxes relationship is loaded
                                    if (!$order->relationLoaded('taxes')) {
                                        $order->load('taxes.tax');
                                    }

                                    // Get the base for CURRENT tax calculation (subtotal - regular discount, BEFORE loyalty discount)
                                    // This matches the order detail page calculation: (tax_percent / 100) * (sub_total - discount_amount)
                                    $currentTaxBase = (float)($order->sub_total ?? 0) - (float)($order->discount_amount ?? 0);

                                    // Process ALL taxes - iterate through ALL order taxes without any filtering
                                    // Get all taxes directly from the relationship to ensure no filtering
                                    $allOrderTaxes = $order->taxes()->with('tax')->get();
                                    foreach ($allOrderTaxes as $orderTax) {
                                        $tax = $orderTax->tax ?? null;
                                        if ($tax && isset($tax->tax_percent) && $tax->tax_percent > 0) {
                                            $taxPercent = (float)$tax->tax_percent;

                                            // Calculate CURRENT tax amount (on current subtotal before loyalty discount)
                                            // This is what the order currently has: 7.50 (3.75 + 3.75)
                                            $currentTax = ($taxPercent / 100.0) * (float)$currentTaxBase;
                                            $currentTaxAmount += $currentTax;

                                            // Calculate NEW tax amount (on new subtotal after loyalty discount)
                                            // This is what the tax will be after loyalty discount is applied
                                            $newTax = ($taxPercent / 100.0) * (float)$newSubTotal;
                                            $previewTaxAmount += $newTax;
                                        }
                                    }
                                    // Round ONLY the final sums to 2 decimal places for display
                                    $currentTaxAmount = round($currentTaxAmount, 2);
                                    $previewTaxAmount = round($previewTaxAmount, 2);

                                    // Store the new tax amount (after discount) for reference
                                    // Tax should be calculated on discounted subtotal (after loyalty discount)
                                    $newTaxAmountAfterDiscount = $previewTaxAmount;

                                    // For display in modal, show the NEW tax (after loyalty discount)
                                    // $previewTaxAmount already contains the correct tax calculated on newSubTotal
                                } else {
                                    // Item-level taxes - sum from order items (already calculated with precision)
                                    $previewTaxAmount = (float)($order->items->sum('tax_amount') ?? 0);
                                    // For item-level taxes, tax amount doesn't change with subtotal discount (it's per item)
                                    // So the new tax amount is the same as current tax
                                    $newTaxAmountAfterDiscount = $previewTaxAmount;
                                    // Order doesn't have direct restaurant relationship, use $restaurant from component
                                    $isInclusive = ($restaurant->tax_inclusive ?? false);
                                    // For inclusive taxes, tax is already in item prices, so don't add again
                                    // For exclusive taxes, we'll add it below
                                }

                                // 5. Calculate charges on discounted subtotal (ensure float precision)
                                $previewCharges = 0.0;
                                if ($order->charges && $order->charges->count() > 0) {
                                    foreach ($order->charges as $chargeRelation) {
                                        if ($chargeRelation->charge) {
                                            $chargeAmount = $chargeRelation->charge->getAmount((float)$newSubTotal);
                                            $previewCharges += (float)$chargeAmount;
                                        }
                                    }
                                    // Round to 2 decimal places
                                    $previewCharges = round($previewCharges, 2);
                                }

                                // 6. Build new total: new subtotal + taxes (if exclusive) + charges + tip + delivery
                                // Ensure all values are floats for proper calculation
                                // Order doesn't have direct restaurant relationship, use $restaurant from component
                                $isInclusive = ($restaurant->tax_inclusive ?? false);
                                $newTotal = (float)$newSubTotal;

                                // Use the NEW tax amount (after loyalty discount) for the new total
                                // $newTaxAmountAfterDiscount is always set (either from order-level or item-level calculation)
                                if (!$isInclusive && $order->tax_mode !== 'order') {
                                    // Add item-level exclusive taxes (use new tax amount after discount)
                                    $newTotal += (float)$newTaxAmountAfterDiscount;
                                } elseif ($order->tax_mode === 'order') {
                                    // Add order-level taxes (always exclusive) - use new tax amount after discount
                                    $newTotal += (float)$newTaxAmountAfterDiscount;
                                }
                                $newTotal += (float)$previewCharges;
                                $newTotal += (float)($order->tip_amount ?? 0);
                                $newTotal += (float)($order->delivery_fee ?? 0);
                                // Round final total to 2 decimal places
                                $newTotal = round($newTotal, 2);

                                // Current total for display
                                $currentTotal = $order->total ?? 0;
                            @endphp
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">@lang('modules.order.subTotal')</span>
                                    <span class="font-medium">{{ currency_format($previewSubTotal, $restaurant->currency_id) }}</span>
                                </div>
                                @if(($order->discount_amount ?? 0) > 0)
                                    <div class="flex justify-between text-green-600 dark:text-green-400">
                                        <span>@lang('modules.order.discount')</span>
                                        <span class="font-medium">- {{ currency_format($order->discount_amount, $restaurant->currency_id) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-blue-600 dark:text-blue-400">
                                    <span>@lang('loyalty::app.loyaltyDiscount')</span>
                                    <span class="font-medium">- {{ currency_format($previewDiscount, $restaurant->currency_id) }}</span>
                                </div>
                                @if($previewTaxAmount > 0)
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>@lang('modules.order.totalTax')</span>
                                        <span class="font-medium">{{ currency_format($previewTaxAmount, $restaurant->currency_id) }}</span>
                                    </div>
                                @endif
                                @if($previewCharges > 0)
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>@lang('modules.order.extraCharges')</span>
                                        <span class="font-medium">{{ currency_format($previewCharges, $restaurant->currency_id) }}</span>
                                    </div>
                                @endif
                                @if(($order->tip_amount ?? 0) > 0)
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>@lang('modules.order.tip')</span>
                                        <span class="font-medium">{{ currency_format($order->tip_amount, $restaurant->currency_id) }}</span>
                                    </div>
                                @endif
                                @if(($order->delivery_fee ?? 0) > 0)
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>@lang('modules.delivery.deliveryFee')</span>
                                        <span class="font-medium">{{ currency_format($order->delivery_fee, $restaurant->currency_id) }}</span>
                                    </div>
                                @endif
                                <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-800">
                                    <div class="flex justify-between">
                                        <span class="font-medium">@lang('modules.order.newTotal')</span>
                                        <span class="text-lg font-bold">
                                            {{ currency_format($newTotal, $restaurant->currency_id) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-2">
                                {{ $customer->name }} {{ __('loyalty::app.hasAvailablePoints') }}: {{ number_format($availableLoyaltyPoints) }} @lang('loyalty::app.points')
                            </p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                {{ __('loyalty::app.pointsValue') }}: {{ currency_format($loyaltyPointsValue, $restaurant->currency_id) }}
                            </p>
                            @if($maxLoyaltyDiscount > 0)
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    {{ __('loyalty::app.maxDiscountToday') }}: {{ currency_format($maxLoyaltyDiscount, $restaurant->currency_id) }}
                                </p>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('loyalty::app.pointsToRedeem') }}
                                </label>
                                <x-input type="number"
                                         value="{{ $maxRedeemablePoints }}"
                                         disabled
                                         readonly
                                         class="block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed"
                                         placeholder="{{ __('loyalty::app.enterPoints') }}" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @if($maxRedeemablePoints > 0)
                                        {{ __('Maximum applicable points based on order subtotal') }}: {{ number_format($maxRedeemablePoints) }} @lang('loyalty::app.points')
                                    @else
                                        {{ __('loyalty::app.maxPoints') }}: {{ number_format($availableLoyaltyPoints) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('loyalty::app.noPointsAvailable') }}</p>
                @endif
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-between w-full gap-2">
                    <x-button-cancel wire:click="closeLoyaltyRedemptionModal" wire:loading.attr="disabled">
                        {{ __('app.cancel') }}
                    </x-button-cancel>
                    <x-button wire:click="redeemLoyaltyPoints({{ $maxRedeemablePoints }})" wire:loading.attr="disabled">
                        {{ __('loyalty::app.redeem') }}
                    </x-button>
                </div>
            </x-slot>
        </x-dialog-modal>
        @endif

        <!-- Stamp Redemption Modal -->
        @if($isStampsEnabledForCustomerSite ?? false)
            <x-dialog-modal wire:model.live="showStampRedemptionModal" maxWidth="md">
                <x-slot name="title">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('loyalty::app.redeemStamps') }}
                    </div>
                </x-slot>

                <x-slot name="content">
                    @if($customer && !empty($customerStamps))
                        @php
                            $redeemableStamps = collect($customerStamps)->filter(function ($stampData) {
                                return $stampData['can_redeem'] ?? false;
                            });
                        @endphp
                        @if($redeemableStamps->count() > 0)
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                    <p class="text-sm font-medium text-green-900 dark:text-green-200 mb-2">
                                        {{ __('loyalty::app.selectStampRuleToRedeem') }}
                                    </p>
                                </div>

                                <div class="space-y-3">
                                    @foreach($redeemableStamps as $stampData)
                                        @php
                                            $rule = $stampData['rule'];
                                            $availableStamps = $stampData['available_stamps'];
                                            $stampsRequired = $stampData['stamps_required'];
                                            $menuItem = $rule->menuItem ?? null;
                                            $rewardMenuItem = $rule->rewardMenuItem ?? null;
                                            $isSelected = in_array($rule->id, $selectedStampRuleIds ?? []);
                                        @endphp
                                        <div class="border rounded-lg p-4 cursor-pointer transition-colors {{ $isSelected ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}"
                                             wire:click="toggleStampRuleSelection({{ $rule->id }})">
                                            <div class="flex items-start justify-between gap-3">
                                                <!-- Checkbox -->
                                                <div class="flex-shrink-0 mt-1">
                                                    <input type="checkbox"
                                                           class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600"
                                                           {{ $isSelected ? 'checked' : '' }}
                                                           wire:click.stop="toggleStampRuleSelection({{ $rule->id }})"
                                                           wire:loading.attr="disabled">
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                                            {{ $menuItem->item_name ?? __('loyalty::app.unknownItem') }}
                                                        </h4>
                                                        @if($isSelected)
                                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                                        {{ __('loyalty::app.stampsRequired') }}: {{ $stampsRequired }} |
                                                        {{ __('loyalty::app.stampsEarned') }}: {{ $availableStamps }}
                                                    </p>
                                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                                        <span class="font-medium">{{ __('loyalty::app.reward') }}:</span>
                                                        @if($rule->reward_type === 'free_item')
                                                            <span class="text-green-600 dark:text-green-400">
                                                                {{ __('loyalty::app.freeItem') }}: {{ $rewardMenuItem->item_name ?? __('loyalty::app.unknownItem') }}
                                                            </span>
                                                        @elseif($rule->reward_type === 'discount_percent')
                                                            <span class="text-green-600 dark:text-green-400">
                                                                {{ $rule->reward_value }}% {{ __('loyalty::app.discount') }}
                                                            </span>
                                                        @elseif($rule->reward_type === 'discount_amount')
                                                            <span class="text-green-600 dark:text-green-400">
                                                                {{ currency_format($rule->reward_value, $restaurant->currency_id) }} {{ __('loyalty::app.discount') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">{{ __('loyalty::app.noStampsAvailable') }}</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">{{ __('loyalty::app.noStampsAvailable') }}</p>
                        </div>
                    @endif
                </x-slot>

                <x-slot name="footer">
                    <div class="flex justify-between w-full gap-2">
                        <x-button-cancel wire:click="closeStampRedemptionModal" wire:loading.attr="disabled">
                            {{ __('app.cancel') }}
                        </x-button-cancel>
                        @if(!empty($selectedStampRuleIds) && count($selectedStampRuleIds) > 0)
                            <x-button wire:click="redeemStamps"
                                      wire:loading.attr="disabled">
                                {{ __('loyalty::app.redeem') }} ({{ count($selectedStampRuleIds) }})
                            </x-button>
                        @else
                            <x-button wire:click="redeemStamps"
                                      wire:loading.attr="disabled"
                                      disabled>
                                {{ __('loyalty::app.redeem') }}
                            </x-button>
                        @endif
                    </div>
                </x-slot>
            </x-dialog-modal>
        @endif

