@php
    $lineQty = $orderItemQty[$cartKey] ?? ($orderItemQty[$normKey] ?? 1);
    $lineAmount = (float) ($orderItemAmount[$cartKey] ?? ($orderItemAmount[$normKey] ?? 0));
    $itemName = $item->item_name ?? ($item->name ?? __('app.item'));
@endphp
<div class="flex justify-between items-start gap-2 border-b border-gray-100 dark:border-gray-700 pb-2" data-item-key="{{ $normKey }}">
    <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $itemName }}</div>
        <div class="text-[11px] text-gray-500 dark:text-gray-400">
            {{ __('modules.order.qty') }}: {{ $lineQty }}
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ currency_format($lineAmount, restaurant()->currency_id) }}
        </div>
        @include('pos.partials.cart-item-delete-button', ['cartItemKey' => $cartKey])
    </div>
</div>
