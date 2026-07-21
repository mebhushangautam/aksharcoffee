<div class="cancelled-order-report  mx-auto px-3 sm:px-4 lg:px-6 pb-6 space-y-3 text-sm">
    <!-- Header Section -->
    <div class="p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="mb-3">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">@lang('menu.cancelledOrderReport')</h1>
            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                @lang('modules.report.cancelledOrderReportDescription')
            </p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-2 mb-3 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Total Cancelled Orders Card -->
            <div class="p-3 bg-red-50 rounded-lg shadow-sm dark:bg-red-900/10 border border-red-100 dark:border-red-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.totalCancelledOrders')</h3>
                    <div class="p-1.5 bg-red-100 text-red-600 rounded-md dark:bg-red-900/20 dark:text-red-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-bold tabular-nums text-gray-900 dark:text-white">
                    {{ $totalCancelledOrders }}
                </p>
            </div>

            <!-- Total Cancelled Amount Card -->
            <div class="p-3 bg-orange-50 rounded-lg shadow-sm dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.totalCancelledAmount')</h3>
                    <div class="p-1.5 bg-orange-100 text-orange-600 rounded-md dark:bg-orange-900/20 dark:text-orange-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-bold tabular-nums text-gray-900 dark:text-white">
                    {{ currency_format($totalCancelledAmount, $currencyId) }}
                </p>
            </div>

            <!-- Top Cancelled Reasons Card -->
            <div class="p-3 bg-yellow-50 rounded-lg shadow-sm dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.topCancelledReasons')</h3>
                    <div class="p-1.5 bg-yellow-100 text-yellow-600 rounded-md dark:bg-yellow-900/20 dark:text-yellow-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                @if(!empty($topCancelledReasons))
                    <div class="space-y-1">
                        @foreach($topCancelledReasons as $index => $reason)
                            <div class="flex items-center justify-between px-2 py-1 rounded-md bg-yellow-100/50 dark:bg-yellow-900/20">
                                <div class="flex items-center gap-1.5 flex-1 min-w-0">
                                    <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 text-[10px] font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                        {{ $reason['name'] }}
                                    </span>
                                </div>
                                <span class="flex-shrink-0 ml-1.5 text-xs font-semibold tabular-nums text-gray-700 dark:text-gray-300">
                                    {{ $reason['count'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        @lang('modules.report.noDataAvailable')
                    </p>
                @endif
            </div>
        </div>

        <!-- Filter Section -->
        <div class="flex flex-wrap justify-between items-start gap-3 p-3 bg-gray-50 rounded-lg dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600">
            <div class="w-full lg:flex-1 min-w-0">
                <form action="#" method="GET" class="w-full">
                    <div class="flex flex-wrap gap-2 items-center">
                        <!-- Date Range Type -->
                        <x-select id="dateRangeType" class="block w-full sm:w-auto min-w-[7.5rem] text-xs" wire:model.live="dateRangeType" wire:change="setDateRange">
                            <option value="today">@lang('app.today')</option>
                            <option value="currentWeek">@lang('app.currentWeek')</option>
                            <option value="lastWeek">@lang('app.lastWeek')</option>
                            <option value="last7Days">@lang('app.last7Days')</option>
                            <option value="currentMonth">@lang('app.currentMonth')</option>
                            <option value="lastMonth">@lang('app.lastMonth')</option>
                            <option value="currentYear">@lang('app.currentYear')</option>
                            <option value="lastYear">@lang('app.lastYear')</option>
                        </x-select>

                        <!-- Date Range Picker -->
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="w-auto min-w-[6.875rem]">
                                <x-datepicker wire:model.change='startDate' placeholder="@lang('app.selectStartDate')" />
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 whitespace-nowrap">@lang('app.to')</span>
                            <div class="w-auto min-w-[6.875rem]">
                                <x-datepicker wire:model.live='endDate' placeholder="@lang('app.selectEndDate')" />
                            </div>
                        </div>

                        <!-- Time Range Picker -->
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="w-auto min-w-[5.625rem]">
                                <label for="start-time" class="sr-only">@lang('modules.reservation.timeStart'):</label>
                                <div x-on:input.debounce.500ms="$wire.set('startTime', $event.detail)">
                                    <x-time-picker value="{{ $startTime }}" />
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 whitespace-nowrap">@lang('app.to')</span>
                            <div class="w-auto min-w-[5.625rem]">
                                <label for="end-time" class="sr-only">@lang('modules.reservation.timeEnd'):</label>
                                <div x-on:input.debounce.500ms="$wire.set('endTime', $event.detail)">
                                    <x-time-picker value="{{ $endTime }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Reason Filter -->
                        <select wire:model.live="selectedCancelReason" class="px-2 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 w-full sm:w-auto min-w-[8.75rem]">
                            <option value="">@lang('modules.report.allCancellationReasons')</option>
                            @foreach($cancelReasons ?? [] as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                            @endforeach
                        </select>

                        <!-- Cancelled By Filter -->
                        <select wire:model.live="selectedCancelledBy" class="px-2 py-1.5 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 w-full sm:w-auto min-w-[7.5rem]">
                            <option value="">@lang('modules.report.allUsers')</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="flex items-center w-full sm:w-auto shrink-0">
                <a href="javascript:;" wire:click='exportReport'
                    class="inline-flex items-center justify-center w-full sm:w-auto px-2 py-1.5 text-xs font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:ring-2 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-1.5 -ml-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586z" clip-rule="evenodd"/></svg>
                    @lang('app.export')
                </a>
            </div>
        </div>
    </div>

    <!-- Cancelled Orders Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="w-full min-w-[52rem] text-xs text-left border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="text-[10px] uppercase tracking-wide text-gray-600 bg-gray-50 dark:bg-gray-700/80 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.orderNumber')</th>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.orderDate')</th>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.cancelledDate')</th>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.customer')</th>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.tableWaiter')</th>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.cancellationReason')</th>
                    <th scope="col" class="px-3 py-2">@lang('modules.report.cancelledBy')</th>
                    <th scope="col" class="px-3 py-2 text-right">@lang('modules.report.orderTotal')</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80 bg-white dark:bg-gray-800">
                @forelse ($cancelledOrders ?? [] as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $order->show_formatted_order_number ?? '#' . $order->order_number }}
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white whitespace-nowrap tabular-nums">
                            @if($order->date_time)
                                @php
                                    $dateFormat = restaurant()->date_format ?? 'd-m-Y';
                                    $timeFormat = restaurant()->time_format ?? 'h:i A';
                                    $orderDate = \Carbon\Carbon::parse($order->date_time)->setTimezone(timezone());
                                @endphp
                                {{ $orderDate->format($dateFormat . ' ' . $timeFormat) }}
                            @else
                                {{ __('modules.report.notAvailable') }}
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white whitespace-nowrap tabular-nums">
                            @if($order->updated_at)
                                @php
                                    $dateFormat = restaurant()->date_format ?? 'd-m-Y';
                                    $timeFormat = restaurant()->time_format ?? 'h:i A';
                                    $cancelledDate = \Carbon\Carbon::parse($order->updated_at)->setTimezone(timezone());
                                @endphp
                                {{ $cancelledDate->format($dateFormat . ' ' . $timeFormat) }}
                            @else
                                {{ __('modules.report.notAvailable') }}
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white max-w-[12rem]">
                            <span class="font-medium text-[13px] leading-tight">{{ $order->customer->name ?? __('modules.report.walkIn') }}</span>
                            @if($order->customer && $order->customer->phone)
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $order->customer->phone }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white">
                            @if($order->table)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    @lang('modules.report.table'): {{ $order->table->table_code }}
                                </span>
                            @endif
                            @if($order->waiter_id && $order->waiter && $order->waiter->roles->pluck('display_name')->contains('Waiter'))
                                @if($order->table)
                                    <div class="mt-1"></div>
                                @endif
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 {{ $order->table ? 'block' : '' }}">@lang('modules.report.waiter'): {{ $order->waiter->name }}</span>
                            @endif
                            @if(!$order->table && !($order->waiter_id && $order->waiter && $order->waiter->roles->pluck('display_name')->contains('Waiter')))
                                <span class="text-gray-400">@lang('modules.report.notAvailable')</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white">
                            @if($order->cancelReason)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $order->cancelReason->reason }}
                                </span>
                            @elseif($order->cancel_reason_text)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    {{ $order->cancel_reason_text }}
                                </span>
                            @else
                                <span class="text-gray-400">@lang('modules.report.notAvailable')</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-900 dark:text-white">
                            @if($order->cancelledBy)
                                <div class="flex items-center">
                                    <div>
                                        <div class="font-medium text-[13px] leading-tight">{{ $order->cancelledBy->name }}</div>
                                        @if($order->cancelledBy->email)
                                            <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ $order->cancelledBy->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">@lang('modules.report.notAvailable')</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 font-semibold text-right tabular-nums text-gray-900 dark:text-white whitespace-nowrap">
                            {{ currency_format($order->total, $currencyId) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            @lang('modules.report.noCancelledOrdersFound')
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($cancelledOrders ?? []) > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-700/60 border-t border-gray-200 dark:border-gray-600">
                    <tr>
                        <td colspan="7" class="px-3 py-2 text-xs font-semibold text-right uppercase tracking-wide text-gray-900 dark:text-white">
                            @lang('modules.report.total'):
                        </td>
                        <td class="px-3 py-2 text-sm font-bold text-right tabular-nums text-gray-900 dark:text-white bg-orange-50/80 dark:bg-orange-900/20 border-l border-orange-200/80 dark:border-orange-800">
                            {{ currency_format($totalCancelledAmount, $currencyId) }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</div>
