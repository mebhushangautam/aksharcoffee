<div class="delivery-app-report  mx-auto px-3 sm:px-4 lg:px-6 pb-6 space-y-3 text-sm">
    <!-- Header Section -->
    <div class="p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="mb-3">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">@lang('menu.deliveryAppReport')</h1>
            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                @lang('modules.report.deliveryAppReportMessage')
                <strong>
                    ({{ $startDate === $endDate
                        ? __('modules.report.salesDataFor') . " $startDate, " . __('modules.report.timePeriod') . " " . \Carbon\Carbon::createFromFormat('H:i', $startTime)->format(timeFormat()) . " - " . \Carbon\Carbon::createFromFormat('H:i', $endTime)->format(timeFormat())
                        : __('modules.report.salesDataFrom') . " $startDate " . __('app.to') . " $endDate, " . __('modules.report.timePeriodEachDay') . " " . \Carbon\Carbon::createFromFormat('H:i', $startTime)->format(timeFormat()) . " - " . \Carbon\Carbon::createFromFormat('H:i', $endTime)->format(timeFormat()) }})
                </strong>
            </p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 gap-2 mb-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
            <!-- Total Orders -->
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-blue-600 dark:text-blue-400">@lang('modules.report.totalOrders')</h3>
                    <div class="p-1.5 bg-blue-100 rounded-md dark:bg-blue-900/50">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-gray-800 dark:text-gray-100">{{ $totalOrders }}</p>
            </div>

            <!-- Total Revenue -->
            <div class="p-3 bg-skin-base/10 rounded-lg shadow-sm dark:bg-skin-base/10 border border-skin-base/30 dark:border-skin-base/40">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-skin-base dark:text-skin-base">@lang('modules.report.totalRevenue')</h3>
                    <div class="p-1.5 bg-skin-base/10 rounded-md dark:bg-skin-base/10">
                        <svg class="w-3.5 h-3.5 text-skin-base dark:text-skin-base" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 13.75c0 .97.75 1.75 1.67 1.75h1.88c.8 0 1.45-.68 1.45-1.53 0-.91-.4-1.24-.99-1.45l-3.01-1.05c-.59-.21-.99-.53-.99-1.45 0-.84.65-1.53 1.45-1.53h1.88c.92 0 1.67.78 1.67 1.75M12 7.5v9"/><path d="M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2m10 4V2h-4m-1 5 5-5"/></g></svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-skin-base dark:text-skin-base">
                    {{ currency_format($totalRevenue, restaurant()->currency_id) }}
                </p>
            </div>

            <!-- Total Commission -->
            <div class="p-3 bg-orange-50 rounded-lg shadow-sm dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-orange-600 dark:text-orange-400">@lang('modules.report.totalCommission')</h3>
                    <div class="p-1.5 bg-orange-100 rounded-md dark:bg-orange-900/50">
                        <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-gray-800 dark:text-gray-100">
                    {{ currency_format($totalCommission, restaurant()->currency_id) }}
                </p>
            </div>

            <!-- Total Delivery Fees -->
            <div class="p-3 bg-purple-50 rounded-lg shadow-sm dark:bg-purple-900/10 border border-purple-100 dark:border-purple-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-purple-600 dark:text-purple-400">@lang('modules.report.totalDeliveryFees')</h3>
                    <div class="p-1.5 bg-purple-100 rounded-md dark:bg-purple-900/50">
                        <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-gray-800 dark:text-gray-100">
                    {{ currency_format($totalDeliveryFees, restaurant()->currency_id) }}
                </p>
            </div>

            <!-- Net Revenue -->
            <div class="p-3 bg-emerald-50 rounded-lg shadow-sm dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800 sm:col-span-2 md:col-span-1 lg:col-span-1">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-emerald-600 dark:text-emerald-400">@lang('modules.report.netRevenue')</h3>
                    <div class="p-1.5 bg-emerald-100 rounded-md dark:bg-emerald-900/50">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-gray-800 dark:text-gray-100">
                    {{ currency_format($netRevenue, restaurant()->currency_id) }}
                </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="flex flex-wrap justify-between items-start gap-3 p-3 bg-gray-50 rounded-lg dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600">
            <div class="w-full lg:flex-1 min-w-0">
                <form class="w-full" action="#" method="GET">
                    <div class="flex flex-wrap gap-2 items-center">
                        <x-select id="dateRangeType" class="block w-full sm:w-fit min-w-[7.5rem] text-xs" wire:model.defer="dateRangeType" wire:change="setDateRange">
                            <option value="today">@lang('app.today')</option>
                            <option value="currentWeek">@lang('app.currentWeek')</option>
                            <option value="lastWeek">@lang('app.lastWeek')</option>
                            <option value="last7Days">@lang('app.last7Days')</option>
                            <option value="currentMonth">@lang('app.currentMonth')</option>
                            <option value="lastMonth">@lang('app.lastMonth')</option>
                            <option value="currentYear">@lang('app.currentYear')</option>
                            <option value="lastYear">@lang('app.lastYear')</option>
                        </x-select>

                        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                            <x-datepicker wire:model.change='startDate' placeholder="@lang('app.selectStartDate')" />
                            <span class="text-xs text-gray-500 dark:text-gray-300 whitespace-nowrap">@lang('app.to')</span>
                            <x-datepicker wire:model.live='endDate' placeholder="@lang('app.selectEndDate')" />
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <div class="w-full max-w-[15rem]">
                                <label for="start-time" class="sr-only">@lang('modules.reservation.timeStart'):</label>
                                <div x-on:input.debounce.500ms="$wire.set('startTime', $event.detail)">
                                    <x-time-picker value="{{ $startTime }}" />
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 w-8 text-center shrink-0">@lang('app.to')</span>
                            <div class="w-full max-w-[15rem]">
                                <label for="end-time" class="sr-only">@lang('modules.reservation.timeEnd'):</label>
                                <div x-on:input.debounce.500ms="$wire.set('endTime', $event.detail)">
                                    <x-time-picker value="{{ $endTime }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="w-full sm:w-auto shrink-0">
                <x-select id="deliveryAppFilter" class="block w-full sm:w-fit min-w-[10rem] text-xs" wire:model.live="selectedDeliveryApp">
                    <option value="all">@lang('modules.report.allDeliveryApps')</option>
                    <option value="direct">@lang('modules.report.directDelivery')</option>
                    @foreach($deliveryApps as $app)
                        <option value="{{ $app->id }}">{{ $app->name }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    <!-- Delivery Apps Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="w-full min-w-[48rem] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="text-[10px] uppercase tracking-wide text-gray-600 bg-gray-50 dark:bg-gray-700/80 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        @lang('modules.report.deliveryApp')
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        @lang('modules.report.totalOrders')
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        @lang('modules.report.totalRevenue')
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        @lang('modules.report.totalDeliveryFees')
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        @lang('modules.report.avgOrderValue')
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        @lang('modules.report.commissionRate')
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        @lang('modules.report.totalCommission')
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-end rtl:text-right">
                        @lang('modules.report.netRevenue')
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80 bg-white dark:bg-gray-800">
                @forelse ($reportData as $data)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2 min-w-0">
                                @if($data['delivery_app']->logo_url)
                                    <img src="{{ $data['delivery_app']->logo_url }}" alt="{{ $data['delivery_app']->name }}" class="w-6 h-6 rounded object-cover shrink-0">
                                @elseif($data['is_direct'] ?? false)
                                    <div class="w-6 h-6 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                    {{ $data['delivery_app']->name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ $data['total_orders'] }}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($data['total_revenue'], restaurant()->currency_id) }}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($data['total_delivery_fees'], restaurant()->currency_id) }}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($data['avg_order_value'], restaurant()->currency_id) }}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">
                                @if($data['delivery_app']->commission_type === 'percent')
                                    {{ $data['delivery_app']->commission_value }}%
                                @else
                                    {{ currency_format($data['delivery_app']->commission_value, restaurant()->currency_id) }} @lang('modules.report.perOrder')
                                @endif
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="text-xs font-medium tabular-nums text-orange-600 dark:text-orange-400">{{ currency_format($data['commission'], restaurant()->currency_id) }}</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <span class="text-xs font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">{{ currency_format($data['net_revenue'], restaurant()->currency_id) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">
                            @lang('modules.report.noDeliveryAppOrders')
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($reportData->count() > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-700/60 border-t border-gray-200 dark:border-gray-600">
                    <tr class="font-semibold text-gray-900 dark:text-white">
                        <td class="px-3 py-2 text-xs">
                            @lang('modules.dashboard.total')
                        </td>
                        <td class="px-3 py-2 text-xs text-center tabular-nums">
                            {{ $totalOrders }}
                        </td>
                        <td class="px-3 py-2 text-xs text-center tabular-nums">
                            {{ currency_format($totalRevenue, restaurant()->currency_id) }}
                        </td>
                        <td class="px-3 py-2 text-xs text-center tabular-nums">
                            {{ currency_format($totalDeliveryFees, restaurant()->currency_id) }}
                        </td>
                        <td class="px-3 py-2 text-xs text-center">
                            -
                        </td>
                        <td class="px-3 py-2 text-xs text-center">
                            -
                        </td>
                        <td class="px-3 py-2 text-xs text-center tabular-nums text-orange-600 dark:text-orange-400">
                            {{ currency_format($totalCommission, restaurant()->currency_id) }}
                        </td>
                        <td class="px-3 py-2 text-xs text-right tabular-nums text-emerald-600 dark:text-emerald-400">
                            {{ currency_format($netRevenue, restaurant()->currency_id) }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</div>
