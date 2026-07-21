@php
    $metaParts = [];

    if ($order->date_time) {
        $metaParts[] = $order->date_time->timezone(timezone())->translatedFormat(timeFormat());
    }

    if ($order->status != 'draft') {
        $metaParts[] = $order->show_formatted_order_number;
    }

    $orderTypeLabel = $order->custom_order_type_name ?? $order->orderType?->order_type_name;
    if ($orderTypeLabel) {
        $metaParts[] = $orderTypeLabel;
    }

    $metaParts[] = $order->status == 'kot'
        ? $order->kot->count() . ' ' . __('modules.order.kot')
        : $order->items->count() . ' ' . __('modules.menu.item');
@endphp
<p class="text-[11px] text-gray-400 dark:text-gray-400">{{ implode(' · ', $metaParts) }}</p>
