@if(($posLoyaltyEnabled ?? false) && user_can('Update Order'))
    @php
        $showLoyaltyRedeemButton = ($customerId ?? null)
            && count($orderItemList ?? []) > 0
            && (int) ($loyaltyPointsRedeemed ?? 0) === 0
            && (float) ($subTotal ?? 0) > 0;
    @endphp
    <div id="loyalty-redeem-button-container" style="display: {{ $showLoyaltyRedeemButton ? 'block' : 'none' }};">
        <button
            type="button"
            onclick="if (typeof window.openLoyaltyRedemptionModal === 'function') { window.openLoyaltyRedemptionModal(); }"
            class="inline-flex items-center px-1 py-1 bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-500 rounded-lg font-semibold text-xs text-blue-700 dark:text-blue-300 shadow-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 gap-1 leading-none"
            title="@lang('loyalty::app.redeemLoyaltyPoints')">
            <svg class="w-4 h-4 text-current me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @lang('loyalty::app.redeemLoyaltyPoints')
        </button>
    </div>
@endif
