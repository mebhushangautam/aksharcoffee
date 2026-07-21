<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

@php
    $appLocale = app()->getLocale();
@endphp

<head>
    <meta charset="UTF-8">
    <title>{{ $restaurant->name ?? 'Demo Restaurant' }} - @lang('modules.order.kotTicket')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
        }

        [dir="rtl"] {
            text-align: right;
        }

        [dir="ltr"] {
            text-align: left;
        }

        .receipt {
            width: {{ $width - 10 }}mm;
            padding: {{ $thermal ? '1mm' : '6.35mm' }};
            page-break-after: {{ (!empty($generateImage) || !empty($forPdf)) ? 'avoid' : 'always' }};
        }
        .header {
            text-align: center;
            margin-bottom: 3mm;
        }
        .bold {
            font-weight: bold;
        }

        .restaurant-info {
            font-size: {{ $width == 56 ? '8pt' : ($width == 80 ? '9pt' : '10pt') }};
            margin-bottom: 1mm;
        }
        .order-info {
            text-align: left;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: {{ $width == 56 ? '0.8mm 0' : '1.1mm 0' }};
            margin-bottom: 2mm;
            font-size: {{ $width == 56 ? '6.5pt' : ($width == 80 ? '7.5pt' : '8pt') }};
            line-height: 1.15;
        }
        [dir="rtl"] .order-info { text-align: right; }
        [dir="ltr"] .order-info { text-align: left; }
        .kot-title {
            font-size: {{ $width == 56 ? '10pt' : ($width == 80 ? '14pt' : '16pt') }};
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            font-size: {{ $width == 56 ? '8pt' : ($width == 80 ? '10pt' : '10pt') }};
        }
        .items-table th {
            padding: 1mm;
            border-bottom: 1px solid #000;
        }
        [dir="rtl"] .items-table th { text-align: right; }
        [dir="ltr"] .items-table th { text-align: left; }
        .items-table td {
            padding: 1mm 0;
            vertical-align: top;
        }
        .qty {
            width: {{ $width == 56 ? '20%' : ($width == 80 ? '15%' : '12%') }};
            text-align: center;
        }
        .description {
            width: {{ $width == 56 ? '80%' : ($width == 80 ? '85%' : '88%') }};
        }
        .item-name {
            font-weight: bold;
        }
        .item-variation,
        .item-modifier,
        .item-note {
            font-weight: normal;
        }
        .modifiers {
            font-size: 10pt;
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 3mm;
            font-size: 12pt;
            padding-top: 2mm;
            border-top: 1px dashed #000;
        }
        .italic {
            font-style: italic;
        }
        .kot-cancelled-banner {
            text-align: center;
            font-weight: bold;
            font-size: {{ $width == 56 ? '11pt' : ($width == 80 ? '14pt' : '16pt') }};
            text-transform: uppercase;
            border: 2px solid #000;
            padding: {{ $width == 56 ? '1.5mm' : '2mm' }};
            margin-bottom: 3mm;
        }
        .item-name-cancelled {
            text-decoration: line-through;
        }
        .order-row {
            width: 100%;
            margin-bottom: {{ $width == 56 ? '1px' : '2px' }};
        }
        .order-row:last-child {
            margin-bottom: 0;
        }
        .order-row table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .order-row td {
            padding: 0.3mm 0;
            vertical-align: top;
        }
        .order-left {
            text-align: left;
            width: 56%;
        }
        .order-right {
            text-align: right;
            width: 44%;
            padding-left: 2mm;
        }
        [dir="rtl"] .order-left { text-align: right; }
        [dir="rtl"] .order-right {
            text-align: left;
            padding-left: 0;
            padding-right: 2mm;
        }
        .back-button {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .back-button:hover {
            background-color: #2563eb;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            .back-button {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        @if($kot->status === 'cancelled')
            <div class="kot-cancelled-banner">
                {{ flipText(__('modules.order.cancelled'), $appLocale, $rtlPrintingChoice) }}
            </div>
        @endif
        <div class="header">

            @if(isset($kotPlace) && $kotPlace)
                <div class="restaurant-info">{{ $kotPlace->name }}</div>
            @endif
        </div>
        <div class="kot-title">
            {{ flipText(__('modules.order.kot'), $appLocale, $rtlPrintingChoice) }} <span class="bold">#{{ $kot->kot_number }}</span>
            @if($kot->token_number)
                <div style="font-size: {{ $width == 56 ? '9pt' : ($width == 80 ? '12pt' : '14pt') }}; margin-top: 1mm;">
                    {{ flipText(__('modules.order.tokenNumber'), $appLocale, $rtlPrintingChoice) }}: <span class="bold">{{ $kot->token_number }}</span>
                </div>
            @endif
        </div>
        <div class="order-info">
            <div class="order-row">
                <!-- Row 1: Order Number (left), Table (right) -->
                <table>
                    <tr>
                        <td class="order-left">
                            <span class="bold">
                                {{ $kot->order->show_formatted_order_number }}
                            </span>
                        </td>
                        <td class="order-right">
                            <span>{{ flipText(__('modules.table.table'), $appLocale, $rtlPrintingChoice) }}: <span class="bold">{{ $kot->order->table ? $kot->order->table->table_code : '-' }}</span></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="order-row">
                <!-- Row 2: Date (left), Time (right) -->
                <table>
                    <tr>
                        <td class="order-left">
                            {{ flipText(__('app.date'), $appLocale, $rtlPrintingChoice) }}: {{ $kot->created_at->timezone($kot->branch->restaurant->timezone)->format(dateFormat()) }}
                        </td>
                        <td class="order-right">
                            {{ flipText(__('app.time'), $appLocale, $rtlPrintingChoice) }}: {{ $kot->created_at->timezone($kot->branch->restaurant->timezone)->format(timeFormat()) }}
                        </td>
                    </tr>
                </table>
            </div>
            @if($kot->order->waiter)
            <div class="order-row">
                <!-- Row 3: Waiter (left), empty (right) -->
                <table>
                    <tr>
                        <td class="order-left">
                            {{ flipText(__('modules.order.waiter'), $appLocale, $rtlPrintingChoice) }}: <span class="bold">{{ $kot->order->waiter->name }}</span>
                        </td>
                        <td class="order-right"></td>
                    </tr>
                </table>
            </div>
            @endif
            @if($kot->order->order_type)
            <div class="order-row">
                <!-- Row 4: Order Type (left), Pickup Time if applicable (right) -->
                <table>
                    <tr>
                        <td class="order-left">
                            @php
                                $kotOrderType = (string) $kot->order->order_type;
                                $kotOrderTypeKey = 'modules.order.' . $kotOrderType;
                                $kotOrderTypeLabel = __($kotOrderTypeKey);
                                if ($kotOrderTypeLabel === $kotOrderTypeKey) {
                                    $kotOrderTypeLabel = Str::title(str_replace('_', ' ', $kotOrderType));
                                }
                            @endphp
                            {{ flipText(__('modules.settings.orderType'), $appLocale, $rtlPrintingChoice) }}: <span class="bold">{{ $kotOrderTypeLabel }}</span>
                        </td>
                        <td class="order-right">
                            @if($kot->order->order_type === 'pickup' && $kot->order->pickup_date)
                                {{ flipText(__('modules.order.pickupAt'), $appLocale, $rtlPrintingChoice) }}: <span class="bold">{{ \Carbon\Carbon::parse($kot->order->pickup_date)->timezone($kot->branch->restaurant->timezone)->format(timeFormat()) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @endif
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="description">{{ flipText(__('modules.menu.itemName'), $appLocale, $rtlPrintingChoice) }}</th>
                    <th class="qty">{{ flipText(__('modules.order.qty'), $appLocale, $rtlPrintingChoice) }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = isset($kotPlaceId)
                        ? $kot->items->filter(function($item) use($kotPlaceId) {
                            return $item->menuItem && $item->menuItem->kot_place_id == $kotPlaceId;
                        })
                        : $kot->items;
                @endphp
                @foreach($items as $item)
                    @php
                        $isItemCancelled = $item->status === 'cancelled';
                    @endphp
                    <tr>
                        <td class="description">
                            <span @class(['item-name', 'item-name-cancelled' => $isItemCancelled])>{{ flipText($item->menuItem->item_name, $appLocale, $rtlPrintingChoice) }}</span>
                            @if (count($receiptLanguages) > 1 && $item->menuItem->translations->count() > 1)
                            <br><span @class(['item-name-cancelled' => $isItemCancelled])>{{ flipText($item->menuItem->getTranslatedValue('item_name', $receiptLanguages[1]), $receiptLanguages[1], $rtlPrintingChoice) }}</span>
                            @endif
                            @if (isset($item->menuItemVariation))
                                <br><small class="item-variation">({{ $item->menuItemVariation->variation }})</small>
                            @endif
                            @foreach ($item->modifierOptions as $modifier)
                                <div class="modifiers item-modifier">• {{ $modifier->name }}</div>
                            @endforeach
                            @if ($item->note)
                                <div class="modifiers item-note">{{ flipText(__('modules.order.note'), $appLocale, $rtlPrintingChoice) }}: {{ $item->note }}</div>
                            @endif
                        </td>
                        <td class="qty">{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($kot->note)
            <div class="footer">
                <strong>{{ flipText(__('modules.order.specialInstructions'), $appLocale, $rtlPrintingChoice) }}:</strong>
                <div class="italic">{{$kot->note}}</div>
            </div>
        @endif
    </div>

    <script>
        // Only enable auto-close for browser popup print, not direct print
        @if(isset($printingChoice) && $printingChoice === 'browserPopupPrint')
        // Detect if running in PWA standalone mode
        function isPWA() {
            return (window.matchMedia('(display-mode: standalone)').matches) ||
                   (window.navigator.standalone === true) ||
                   (document.referrer.includes('android-app://'));
        }

        // Set cookie for server-side PWA detection
        if (isPWA()) {
            // Set cookie that expires in 1 hour
            const expires = new Date();
            expires.setTime(expires.getTime() + (60 * 60 * 1000)); // 1 hour
            document.cookie = 'pwa_mode=standalone; expires=' + expires.toUTCString() + '; path=/';

            // Also set session storage for consistency
            sessionStorage.setItem('is_pwa', 'true');
        }

        // Show back button if in PWA mode
        if (isPWA()) {
            const backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.style.display = 'block';
            }
        }

        // Go back function
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                // If no history, redirect to orders page or home
                window.location.href = '{{ route("orders.index") }}';
            }
        }

        // Auto-trigger print dialog when page loads and close the window afterward
        window.onload = function() {
            // Only call print if not in an iframe
            if (window.self === window.top) {
                const closeAfterPrint = () => {
                    // In PWA, navigate back instead of trying to close the window
                    if (isPWA()) {
                        goBack();
                    } else {
                        window.close();
                    }
                };

                // Set handler for after print where supported
                if ('onafterprint' in window) {
                    window.onafterprint = function() {
                        closeAfterPrint();
                    };
                } else {
                    // Fallback: attempt to close shortly after print is triggered
                    setTimeout(closeAfterPrint, 1000);
                }

                window.print();
            }
        };
        @else
        // For direct print, just trigger print without auto-close
        window.onload = function() {
            // Only call print if not in an iframe
            if (window.self === window.top) {
                window.print();
            }
        };
        @endif
    </script>


</body>
</html>
