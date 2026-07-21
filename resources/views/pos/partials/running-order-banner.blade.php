@php
    // On full page load, posState/JS can lag; for "view this order" KOT URL the banner must show immediately.
    $isKotRoute = request()->is('pos/kot/*') && ! empty($orderID ?? null);
    $isShowOrderDetail = request()->boolean('show-order-detail');
    $posRunningBannerSsrExistingOrder = $isKotRoute && $isShowOrderDetail;
    $posRunningBannerSsrNewKot = $isKotRoute && ! $isShowOrderDetail;
    $posRunningBannerVisible = $posRunningBannerSsrExistingOrder || $posRunningBannerSsrNewKot;
    $posRunningBannerLine = '';
    if ($posRunningBannerVisible) {
        if (! isOrderPrefixEnabled()) {
            $orderLabel = (string) ($orderNumber ?? '');
            if ($orderLabel === '') {
                $orderLabel = (string) $orderID;
            } else {
                $orderLabel = '#' . $orderLabel;
            }
        } else {
            $orderLabel = (string) ($formattedOrderNumber ?? '');
            if (trim($orderLabel) === '') {
                $orderLabel = (string) $orderID;
            }
        }
        $bannerTitle = $posRunningBannerSsrNewKot
            ? __('modules.order.newKot')
            : __('messages.posRunningOrderBannerTitle');
        $posRunningBannerLine = $bannerTitle
            . ' · '
            . $orderLabel
            . ' · '
            . __('messages.posRunningOrderBannerExistingOrder');
    }
@endphp
{{-- Blade POS: running order / offline queue context (updated via __posUpdateRunningOrderBanner in pos.blade.php) --}}
<div id="pos-running-order-banner" class="{{ $posRunningBannerVisible ? 'shrink-0' : 'hidden shrink-0' }} mb-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 dark:border-amber-600/40 dark:bg-amber-900/25" role="status">
    <div class="flex items-center gap-2.5">
        <span class="pos-running-order-banner-pulse inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50" style="animation: posRunningOrderPulse 1.4s ease-in-out infinite" aria-hidden="true"></span>
        <div class="min-w-0 flex-1 text-xs leading-snug">
            {{-- Single line (title · sub) to save vertical space on narrow POS columns --}}
            <div class="font-semibold text-amber-950 dark:text-amber-100 truncate" id="pos-running-order-banner-text">{{ $posRunningBannerLine }}</div>
        </div>
    </div>
</div>
<style>
@keyframes posRunningOrderPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.35; transform: scale(0.88); }
}
</style>
