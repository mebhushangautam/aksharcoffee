<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ isRtl() ? 'rtl' : 'ltr' }}">

@php
    $appLocale = app()->getLocale();
    $receiptLanguages = $receiptLanguages ?? ($receiptSettings->receipt_languages ?? ['en']);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $orderBranch->name ?? restaurant()->name }} - {{ $order->show_formatted_order_number }}</title>
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

        body {
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .receipt {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .restaurant-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            display: block;
        }

        .restaurant-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .restaurant-info {
            font-size: 12px;
            margin-bottom: 3px;
            color: #666;
        }

        .order-info {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .order-info h3 {
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-item {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .items-table th {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        [dir="rtl"] .items-table th {
            text-align: right;
        }

        [dir="ltr"] .items-table th {
            text-align: left;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .qty {
            width: 8%;
            text-align: center;
        }

        .description {
            width: 50%;
        }

        .price {
            width: 20%;
        }

        .amount {
            width: 22%;
        }

        [dir="rtl"] .price,
        [dir="rtl"] .amount {
            text-align: left;
        }

        [dir="ltr"] .price,
        [dir="ltr"] .amount {
            text-align: right;
        }

        .modifiers {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .summary {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .summary-row {
            width: 100%;
            margin-bottom: 8px;
            padding: 3px 0;
        }
        .summary-row table {
            width: 100%;
            border-collapse: collapse;
        }
        [dir="rtl"] .summary-row td:first-child {
            text-align: right;
        }

        [dir="rtl"] .summary-row td:last-child {
            text-align: left;
        }

        [dir="ltr"] .summary-row td:first-child {
            text-align: left;
        }

        [dir="ltr"] .summary-row td:last-child {
            text-align: right;
        }

        .summary-row.secondary {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
            padding-left: 20px;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }

        .qr_code {
            margin: 15px 0;
            text-align: center;
        }

        .qr_code img {
            max-width: 150px;
            height: auto;
        }

        .payment-details {
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .payment-details h4 {
            margin-bottom: 10px;
            color: #333;
        }

        @media print {
            body {
                font-size: 11px;
            }

            .receipt {
                max-width: none;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header">
            @if ($receiptSettings->show_restaurant_logo)
                <img src="{{ restaurant()->logo_url }}" alt="{{ $orderBranch->name ?? restaurant()->name }}" class="restaurant-logo">
            @endif
            <div class="restaurant-name">{{ $orderBranch->name ?? restaurant()->name }}</div>
            <div class="restaurant-info">{{ $orderBranch->address ?? '' }}</div>
            <div class="restaurant-info">
                {{ flipText(__('modules.customer.phone'), $appLocale) }}
                @if (count($receiptLanguages) > 1)
                <br>{{ flipText(__('modules.customer.phone', [], $receiptLanguages[1]), $receiptLanguages[1]) }}
                @endif:
                <span dir="ltr" style="unicode-bidi: embed;">{{ $orderBranch->phone ?: restaurant()->phone_number }}</span>
            </div>
            @if ($receiptSettings->show_tax)
                @if (empty($orderBranch->cr_number) && empty($orderBranch->vat_number))
                    @foreach ($taxDetails as $taxDetail)
                        <div class="restaurant-info">{{ $taxDetail->tax_name }}: {{ $taxDetail->tax_id }}</div>
                    @endforeach
                @endif
            @endif
            @if ($receiptSettings->show_cr_number && !empty($orderBranch->cr_number))
                <div class="restaurant-info">
                    {{ flipText(__('modules.settings.branchCrNumber'), $appLocale) }}
                    @if (count($receiptLanguages) > 1)
                    <br>{{ flipText(__('modules.settings.branchCrNumber', [], $receiptLanguages[1]), $receiptLanguages[1]) }}
                    @endif: <span dir="ltr" style="unicode-bidi: embed;">{{ $orderBranch->cr_number }}</span>
                </div>
            @endif
            @if ($receiptSettings->show_vat_number && !empty($orderBranch->vat_number))
                <div class="restaurant-info">
                    {{ flipText(__('modules.settings.branchVatNumber'), $appLocale) }}
                    @if (count($receiptLanguages) > 1)
                    <br>{{ flipText(__('modules.settings.branchVatNumber', [], $receiptLanguages[1]), $receiptLanguages[1]) }}
                    @endif: <span dir="ltr" style="unicode-bidi: embed;">{{ $orderBranch->vat_number }}</span>
                </div>
            @endif
        </div>

        <div class="order-info">
            <h3>{{ flipText(__('modules.settings.orderDetails'), $appLocale) }}</h3>
            <div class="info-grid">
                <div class="info-item">

                    <span>
                        @if(!isOrderPrefixEnabled())
                            <span class="info-label">{{ flipText(__('modules.order.orderNumber'), $appLocale) }}:</span>
                            <span>#{{ $order->order_number }}</span>
                        @else
                            {{ $order->formatted_order_number }}
                        @endif
                    </span>
                </div>
                @php
                    $tokenNumber = $order->kot->whereNotNull('token_number')->first()?->token_number;
                @endphp
                @if ($tokenNumber)
                    <div class="info-item">
                        <span class="info-label">{{ flipText(__('modules.order.tokenNumber'), $appLocale) }}:</span>
                        <span>{{ $tokenNumber }}</span>
                    </div>
                @endif
                <div class="info-item">
                    <span class="info-label">{{ flipText(__('app.dateTime'), $appLocale) }}:</span>
                    <span>{{ $order->date_time->timezone(timezone())->translatedFormat(dateFormat() . ' ' . timeFormat()) }}</span>
                </div>
                @if ($receiptSettings->show_table_number && $order->table && $order->table->table_code)
                    <div class="info-item">
                        <span class="info-label">{{ flipText(__('modules.settings.tableNumber'), $appLocale) }}:</span>
                        <span>{{ $order->table->table_code }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_total_guest && $order->number_of_pax)
                    <div class="info-item">
                        <span class="info-label">{{ flipText(__('modules.order.noOfPax'), $appLocale) }}:</span>
                        <span>{{ $order->number_of_pax }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_waiter && $order->waiter && $order->waiter->name)
                    <div class="info-item">
                        <span class="info-label">{{ flipText(__('modules.order.waiter'), $appLocale) }}:</span>
                        <span>{{ $order->waiter->name }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_customer_name && $order->customer && $order->customer->name)
                    <div class="info-item">
                        <span class="info-label">{{ flipText(__('modules.customer.customer'), $appLocale) }}:</span>
                        <span>{{ $order->customer->name }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_customer_address && $order->customer && $order->customer->delivery_address)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <span class="info-label">{{ flipText(__('modules.customer.customerAddress'), $appLocale) }}:</span>
                        <span>{{ $order->customer->delivery_address }}</span>
                    </div>
                @endif
                @if ($receiptSettings->show_customer_phone && $order->customer && $order->customer->phone)
                    <div class="info-item">
                        <span class="info-label">{{ flipText(__('modules.customer.phone'), $appLocale) }}:</span>
                        <span dir="ltr" style="unicode-bidi: embed;">{{ $order->customer->phone }}</span>
                    </div>
                @endif
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="qty">{{ flipText(__('modules.order.qty'), $appLocale) }}</th>
                    <th class="description">{{ flipText(__('modules.menu.itemName'), $appLocale) }}</th>
                    <th class="price">{{ flipText(__('modules.order.price'), $appLocale) }} ({{ restaurant()->currency->currency_code }})</th>
                    <th class="amount">{{ flipText(__('modules.order.amount'), $appLocale) }} ({{ restaurant()->currency->currency_code }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="description">
                            <strong>{{ flipText($item->menuItem->item_name, $appLocale) }}</strong>
                            @if (count($receiptLanguages) > 1 && $item->menuItem->translations->count() > 1)
                            <br>{{ flipText($item->menuItem->getTranslatedValue('item_name', $receiptLanguages[1]), $receiptLanguages[1]) }}
                            @endif
                            @if (isset($item->menuItemVariation))
                                <br><small>({{ $item->menuItemVariation->variation }})</small>
                            @endif
                            @foreach ($item->modifierOptions as $modifier)
                                <div class="modifiers">• {{ $modifier->name }}
                                    @if($modifier->price > 0)
                                        (+{{ currency_format($modifier->price, restaurant()->currency_id, false, true) }})
                                    @endif
                                </div>
                            @endforeach
                            @if($item->note)
                                <div class="modifiers"><em>{{ flipText(__('modules.order.note'), $appLocale) }}: {{ $item->note }}</em></div>
                            @endif
                        </td>
                        <td class="price">{{ currency_format($item->price, restaurant()->currency_id, false, false) }}</td>
                        <td class="amount">{{ currency_format($item->amount, restaurant()->currency_id, false, false) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <table>
                    <tr>
                        <td>{{ flipText(__('modules.order.subTotal'), $appLocale) }}:</td>
                        <td>{{ currency_format($order->sub_total, restaurant()->currency_id, false, true) }}</td>
                    </tr>
                </table>
            </div>

            @if (!is_null($order->discount_amount))
                <div class="summary-row">
                    <table>
                        <tr>
                            <td>{{ flipText(__('modules.order.discount'), $appLocale) }}
                                @if ($order->discount_type == 'percent')
                                    ({{ rtrim(rtrim($order->discount_value, '0'), '.') }}%)
                                @endif:
                            </td>
                            <td>-{{ currency_format($order->discount_amount, restaurant()->currency_id, false, true) }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            @foreach ($order->charges as $item)
                <div class="summary-row">
                    <table>
                        <tr>
                            <td>{{ $item->charge->charge_name }}
                                @if ($item->charge->charge_type == 'percent')
                                    ({{ $item->charge->charge_value }}%)
                                @endif:
                            </td>
                            <td>{{ currency_format(($item->charge->getAmount($order->sub_total - ($order->discount_amount ?? 0))), restaurant()->currency_id, true, true) }}</td>
                        </tr>
                    </table>
                </div>
            @endforeach

            @if ($order->tip_amount > 0)
                <div class="summary-row">
                    <table>
                        <tr>
                            <td>{{ flipText(__('modules.order.tip'), $appLocale) }}:</td>
                            <td>{{ currency_format($order->tip_amount, restaurant()->currency_id, false, true) }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            @if ($order->order_type === 'delivery' && !is_null($order->delivery_fee))
                <div class="summary-row">
                    <table>
                        <tr>
                            <td>{{ flipText(__('modules.delivery.deliveryFee'), $appLocale) }}:</td>
                            <td>
                                @if($order->delivery_fee > 0)
                                    {{ currency_format($order->delivery_fee, restaurant()->currency_id, false, true) }}
                                @else
                                    {{ flipText(__('modules.delivery.freeDelivery'), $appLocale) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

            @if ($taxMode == 'order')
                @foreach ($order->taxes as $item)
                    <div class="summary-row">
                        <table>
                            <tr>
                                <td>{{ $item->tax->tax_name }} ({{ $item->tax->tax_percent }}%):</td>
                                <td>{{ currency_format(($item->tax->tax_percent / 100) * $taxBase, restaurant()->currency_id, false, true) }}</td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            @else
                @if($order->total_tax_amount > 0)
                    @php
                        $taxTotals = [];
                        $totalTax = 0;
                        foreach ($order->items as $item) {
                            $qty = $item->quantity ?? 1;
                            $taxBreakdown = is_array($item->tax_breakup) ? $item->tax_breakup : (json_decode($item->tax_breakup, true) ?? []);
                            foreach ($taxBreakdown as $taxName => $taxInfo) {
                                if (!isset($taxTotals[$taxName])) {
                                    $taxTotals[$taxName] = [
                                        'percent' => $taxInfo['percent'] ?? 0,
                                        'amount' => ($taxInfo['amount'] ?? 0) * $qty
                                    ];
                                } else {
                                    $taxTotals[$taxName]['amount'] += ($taxInfo['amount'] ?? 0) * $qty;
                                }
                            }
                            $totalTax += $item->tax_amount ?? 0;
                        }
                    @endphp
                    @foreach ($taxTotals as $taxName => $taxInfo)
                        <div class="summary-row secondary">
                            <table>
                                <tr>
                                    <td>{{ $taxName }} ({{ $taxInfo['percent'] }}%)</td>
                                    <td>{{ currency_format($taxInfo['amount'], restaurant()->currency_id, false, true) }}</td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                    <div class="summary-row">
                        <table>
                            <tr>
                                <td>{{ flipText(__('modules.order.totalTax'), $appLocale) }}:</td>
                                <td>{{ currency_format($totalTax, restaurant()->currency_id, false, true) }}</td>
                            </tr>
                        </table>
                    </div>
                @endif
            @endif

            @if ($payment)
                <div class="summary-row">
                    <table>
                        <tr>
                            <td>{{ flipText(__('modules.order.balanceReturn'), $appLocale) }}:</td>
                            <td>{{ currency_format($payment->balance, restaurant()->currency_id, false, true) }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            <div class="summary-row total">
                <table>
                    <tr>
                        <td>{{ flipText(__('modules.order.total'), $appLocale) }}:</td>
                        <td>{{ currency_format($order->total, restaurant()->currency_id, false, true) }}</td>
                    </tr>
                </table>
            </div>

            @if ($receiptSettings->show_payment_status)
                <div class="summary-row" style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #333;">
                    <table>
                        <tr>
                            <td style="font-weight: bold; font-size: 14px;">{{ flipText(__('modules.order.paymentStatus'), $appLocale) }}</td>
                            <td style="font-weight: bold; font-size: 14px;">
                                @if($order->status === 'paid')
                                    <span style="color: #10b981;">{{ flipText(__('modules.order.paid'), $appLocale) }}</span>
                                @else
                                    <span style="color: #ef4444;">{{ flipText(__('modules.order.unpaid'), $appLocale) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
        </div>

        @if ($receiptSettings->show_payment_details && $order->payments->count())
            <div class="payment-details">
                <h4>{{ flipText(__('modules.order.paymentDetails'), $appLocale) }}</h4>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="qty">{{ flipText(__('modules.order.amount'), $appLocale) }}</th>
                            <th class="description">{{ flipText(__('modules.order.paymentMethod'), $appLocale) }}</th>
                            <th class="price">{{ flipText(__('app.dateTime'), $appLocale) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->payments as $payment)
                            <tr>
                                <td class="qty">{{ currency_format($payment->amount, restaurant()->currency_id, false, true) }}</td>
                                <td class="description">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td class="price">
                                    @if($payment->payment_method != 'due')
                                        {{ $payment->created_at->timezone(timezone())->translatedFormat(dateFormat() . ' ' . timeFormat()) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="footer">
            <p>{{ flipText(__('messages.thankYouVisit'), $appLocale) }}</p>

            @if ($order->status != 'paid' && $receiptSettings->show_payment_qr_code)
                <div class="qr_code">
                    <p>{{ flipText(__('modules.settings.payFromYourPhone'), $appLocale) }}</p>
                    <img src="{{ $receiptSettings->payment_qr_code_url }}" alt="QR Code">
                    <p>{{ flipText(__('modules.settings.scanQrCode'), $appLocale) }}</p>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
