<div class="order-report mx-auto px-3 sm:px-4 lg:px-6 pb-6 space-y-3 text-sm">
    <div class="p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="mb-3">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">@lang('modules.order.orderReport')</h1>
            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                @lang('modules.order.orderReportMessage')
                @php
                    $formattedStartTime = \Carbon\Carbon::parse($startTime)->format(timeFormat());
                    $formattedEndTime = \Carbon\Carbon::parse($endTime)->format(timeFormat());
                @endphp
                <strong>
                    ({{ $startDate === $endDate
                        ? __('modules.report.salesDataFor') . " $startDate, " . __('modules.report.timePeriod') . " $formattedStartTime - $formattedEndTime"
                        : __('modules.report.salesDataFrom') . " $startDate " . __('app.to') . " $endDate, " . __('modules.report.timePeriodEachDay') . " $formattedStartTime - $formattedEndTime" }})
                </strong>
            </p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 gap-2 mb-3 sm:grid-cols-2">
            <div class="p-3 bg-skin-base/10 rounded-lg shadow-sm dark:bg-skin-base/10 border border-skin-base/30 dark:border-skin-base/40">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-skin-base dark:text-skin-base">@lang('modules.report.totalOrders')</h3>
                    <div class="p-1.5 bg-skin-base/10 rounded-md dark:bg-skin-base/10">
                        <svg class="w-3.5 h-3.5 text-skin-base dark:text-skin-base" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-skin-base dark:text-skin-base">
                    {{ $this->totalOrders }}
                </p>
            </div>

            <div class="p-3 bg-blue-50 rounded-lg shadow-sm dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-blue-600 dark:text-blue-400">@lang('modules.report.sumOfTotalRevenue')</h3>
                    <div class="p-1.5 bg-blue-100 rounded-md dark:bg-blue-900/50">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-gray-800 dark:text-gray-100">
                    {{ currency_format($this->totalOrderAmount, restaurant()->currency_id) }}
                </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="space-y-3">
            <div class="flex flex-wrap justify-between items-start gap-3 p-3 bg-gray-50 rounded-lg dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600">
                <form class="w-full min-w-0 lg:flex-1" action="#" method="GET">
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

                        <div id="date-range-picker" date-rangepicker class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-40">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-2.5 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20zM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2"/></svg>
                                </div>
                                <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full ps-9 py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 shrink-0">@lang('app.to')</span>
                            <div class="relative w-full sm:w-40">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-2.5 pointer-events-none">
                                    <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20zM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2"/></svg>
                                </div>
                                <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full ps-9 py-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            <div class="w-full max-w-[6.5rem]">
                                <label for="start-time" class="sr-only">@lang('modules.reservation.timeStart'):</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 end-0 top-0 pe-2 flex items-center pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 7.5a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0m7 0V3h1v4.293l2.854 2.853-.708.708-3-3A.5.5 0 0 1 7 7.5" fill="currentColor"/></svg>
                                    </div>
                                    <x-input id="start-time" class="text-xs py-1.5" type="time" wire:model.live.debounce.500ms="startTime" />
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 w-8 text-center shrink-0">@lang('app.to')</span>
                            <div class="w-full max-w-[6.5rem]">
                                <label for="end-time" class="sr-only">@lang('modules.reservation.timeEnd'):</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 end-0 top-0 pe-2 flex items-center pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 7.5a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0m7 0V3h1v4.293l2.854 2.853-.708.708-3-3A.5.5 0 0 1 7 7.5" fill="currentColor"/></svg>
                                    </div>
                                    <x-input id="end-time" class="text-xs py-1.5" type="time" wire:model.live.debounce.500ms="endTime" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-[12rem]">
                    <x-input id="search" class="block w-full pr-9 text-xs" type="text"
                        placeholder="{{ __('placeholders.searchOrders') }}" wire:model.live.debounce.500ms="searchTerm" />
                    @if($searchTerm)
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2.5" wire:click="$set('searchTerm', '')" aria-label="{{ __('app.clear') }}">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <x-select id="orderTypeFilter" class="block w-full sm:w-auto min-w-[10rem] text-xs" wire:model.live="orderTypeFilter">
                    <option value="">@lang('app.all') @lang('modules.report.orderType')</option>
                    <option value="dine_in">@lang('modules.order.dine_in')</option>
                    <option value="delivery">@lang('modules.order.delivery')</option>
                    <option value="pickup">@lang('modules.order.pickup')</option>
                </x-select>

                <a href="javascript:;" wire:click='exportReport'
                    class="inline-flex items-center justify-center shrink-0 px-2 py-1.5 text-xs font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:ring-2 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-1.5 -ml-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586z" clip-rule="evenodd"/></svg>
                    @lang('app.export')
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="w-full min-w-[64rem] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="text-[10px] uppercase tracking-wide text-gray-600 bg-gray-50 dark:bg-gray-700/80 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('id')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'id' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('app.id')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('branch')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'branch' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.settings.branchName')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('order_number')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'order_number' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.orderNumber')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('date_time')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'date_time' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.orderDate')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('status')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'status' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('app.status')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('placed_by')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'placed_by' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.placedBy')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-right rtl:text-left">
                        <button type="button" wire:click="sortByToggle('amount_paid')" class="inline-flex hover:text-gray-900 dark:hover:text-white transition-colors ltr:ml-auto rtl:mr-auto {{ $sortBy === 'amount_paid' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.order.amountPaid')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right font-medium">
                        @lang('modules.order.paymentMethod')
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-right rtl:text-left">
                        <button type="button" wire:click="sortByToggle('total_tax_amount')" class="inline-flex hover:text-gray-900 dark:hover:text-white transition-colors ltr:ml-auto rtl:mr-auto {{ $sortBy === 'total_tax_amount' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.order.totalTax')
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('order_type')" class="text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'order_type' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.orderType')
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80 bg-white dark:bg-gray-800">
                @forelse ($this->orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-3 py-2 text-xs tabular-nums text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $order->id }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white max-w-[10rem] truncate" title="{{ $order->branch?->name ?? '' }}">
                            {{ $order->branch?->name ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-xs font-medium text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $order->show_formatted_order_number ?? $order->order_number }}
                        </td>
                        <td class="px-3 py-2 text-xs tabular-nums text-gray-900 dark:text-white whitespace-nowrap">
                            @if($order->date_time)
                                {{ \Carbon\Carbon::parse($order->date_time)->setTimezone(timezone())->format(dateFormat() . ' ' . timeFormat()) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs">
                            @php
                                $statusColors = [
                                    'kot' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'billed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'out_for_delivery' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'payment_due' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'canceled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                @lang('modules.order.' . $order->status)
                            </span>
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white max-w-[10rem] truncate" title="{{ $order->addedBy?->name ?? '' }}">
                            {{ $order->addedBy?->name ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-xs font-medium tabular-nums text-gray-900 dark:text-white text-right">
                            {{ currency_format($order->amount_paid ?? 0, restaurant()->currency_id) }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white max-w-[10rem]">
                            @php
                                $paymentMethods = $order->payments->pluck('payment_method')->unique()->filter();
                            @endphp
                            @if($paymentMethods->isNotEmpty())
                                {{ $paymentMethods->map(fn ($method) => \Illuminate\Support\Facades\Lang::has('modules.order.' . $method) ? __('modules.order.' . $method) : ucwords(str_replace('_', ' ', $method)))->join(', ') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs font-medium tabular-nums text-gray-900 dark:text-white text-right">
                            {{ currency_format($order->total_tax_amount ?? 0, restaurant()->currency_id) }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">
                            @if($order->order_type && \Illuminate\Support\Facades\Lang::has('modules.order.' . $order->order_type))
                                @lang('modules.order.' . $order->order_type)
                            @else
                                {{ $order->order_type ? ucwords(str_replace('_', ' ', $order->order_type)) : '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">
                            @lang('messages.noRecordFound')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @script
    <script>
        const datepickerEl1 = document.getElementById('datepicker-range-start');

        datepickerEl1.addEventListener('changeDate', (event) => {
            $wire.dispatch('setStartDate', { start: datepickerEl1.value });
        });

        const datepickerEl2 = document.getElementById('datepicker-range-end');

        datepickerEl2.addEventListener('changeDate', (event) => {
            $wire.dispatch('setEndDate', { end: datepickerEl2.value });
        });
    </script>
    @endscript
</div>
