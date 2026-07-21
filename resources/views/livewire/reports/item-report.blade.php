<div class="item-report  mx-auto px-3 sm:px-4 lg:px-6 pb-6 space-y-3 text-sm">
    <!-- Header Section -->
    <div class="p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="mb-3">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">@lang('menu.itemReport')</h1>
            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                @lang('modules.report.itemReportMessage')
                <strong>
                    ({{ $startDate === $endDate
                        ? __('modules.report.salesDataFor') . " $startDate, " . __('modules.report.timePeriod') . " " . \Carbon\Carbon::createFromFormat('H:i', $startTime)->format(timeFormat()) . " - " . \Carbon\Carbon::createFromFormat('H:i', $endTime)->format(timeFormat())
                        : __('modules.report.salesDataFrom') . " $startDate " . __('app.to') . " $endDate, " . __('modules.report.timePeriodEachDay') . " " . \Carbon\Carbon::createFromFormat('H:i', $startTime)->format(timeFormat()) . " - " . \Carbon\Carbon::createFromFormat('H:i', $endTime)->format(timeFormat()) }})
                </strong>
            </p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 gap-2 mb-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <!-- Sum Of Total Revenue -->
            <div class="p-3 bg-skin-base/10 rounded-lg shadow-sm dark:bg-skin-base/10 border border-skin-base/30 dark:border-skin-base/40">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-skin-base dark:text-skin-base">@lang('modules.report.sumOfTotalRevenue')</h3>
                    <div class="p-1.5 bg-skin-base/10 rounded-md dark:bg-skin-base/10">
                        <svg class="w-3.5 h-3.5 text-skin-base dark:text-skin-base" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 13.75c0 .97.75 1.75 1.67 1.75h1.88c.8 0 1.45-.68 1.45-1.53 0-.91-.4-1.24-.99-1.45l-3.01-1.05c-.59-.21-.99-.53-.99-1.45 0-.84.65-1.53 1.45-1.53h1.88c.92 0 1.67.78 1.67 1.75M12 7.5v9"/><path d="M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2m10 4V2h-4m-1 5 5-5"/></g></svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-skin-base dark:text-skin-base">
                    {{ currency_format($this->totalRevenue, restaurant()->currency_id) }}
                </p>
            </div>

            <!-- Total Quantity Sold Card -->
            <div class="p-3 bg-emerald-50 rounded-lg shadow-sm dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800">
                <div class="flex items-center justify-between mb-1.5">
                    <h3 class="text-xs font-medium text-emerald-600 dark:text-emerald-400">@lang('modules.report.totalQuantitySold')</h3>
                    <div class="p-1.5 bg-emerald-100 rounded-md dark:bg-emerald-900/50">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.22 17H9.8a2 2 0 0 1-2-1.55L5.2 4H3a1 1 0 0 1 0-2h2.2a2 2 0 0 1 2 1.55L9.8 15h8.42L20 7.76a1 1 0 0 1 2 .48l-1.81 7.25A2 2 0 0 1 18.22 17m-1.72 2a1.5 1.5 0 1 0 1.5 1.5 1.5 1.5 0 0 0-1.5-1.5m-5 0a1.5 1.5 0 1 0 1.5 1.5 1.5 1.5 0 0 0-1.5-1.5m3.21-9.29 4-4a1 1 0 1 0-1.42-1.42L14 7.59l-1.29-1.3a1 1 0 0 0-1.42 1.42l2 2a1 1 0 0 0 1.42 0" fill="currentColor"/></svg>
                    </div>
                </div>
                <p class="text-lg break-words font-bold tabular-nums text-gray-800 dark:text-gray-100">
                    {{ $this->totalQuantitySold }}
                </p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-[12rem]">
                    <x-input id="menu_name" class="block w-full pr-9 text-xs" type="text"
                        placeholder="{{ __('placeholders.searchMenuItems') }}" wire:model.live.debounce.500ms="searchTerm" />
                    @if($searchTerm)
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2.5" wire:click="$set('searchTerm', '')" aria-label="{{ __('app.clear') }}">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <a href="javascript:;" wire:click='exportReport'
                    class="inline-flex items-center justify-center shrink-0 px-2 py-1.5 text-xs font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:ring-2 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-1.5 -ml-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586z" clip-rule="evenodd"/></svg>
                    @lang('app.export')
                </a>
            </div>

            <div class="p-3 bg-gray-50 rounded-lg dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600 mb-0">
                <form class="w-full min-w-0" action="#" method="GET">
                    {{-- Single wrapping toolbar: presets + people filters + date range + time range (aligned like tax / COD reports) --}}
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-2">
                        <x-select id="dateRangeType" class="block w-full sm:w-fit min-w-[7.5rem] shrink-0" wire:model.defer="dateRangeType" wire:change="setDateRange">
                            <option value="today">@lang('app.today')</option>
                            <option value="currentWeek">@lang('app.currentWeek')</option>
                            <option value="lastWeek">@lang('app.lastWeek')</option>
                            <option value="last7Days">@lang('app.last7Days')</option>
                            <option value="currentMonth">@lang('app.currentMonth')</option>
                            <option value="lastMonth">@lang('app.lastMonth')</option>
                            <option value="currentYear">@lang('app.currentYear')</option>
                            <option value="lastYear">@lang('app.lastYear')</option>
                        </x-select>

                        <x-select wire:model.live="selectedHandler" wire:change="filterHandler" class="block w-full sm:w-fit min-w-[8.75rem] shrink-0">
                            <option value="">@lang('modules.report.allHandlers')</option>
                            @foreach($handlers ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-select>

                        <x-select wire:model.live="selectedWaiter" wire:change="filterWaiter" class="block w-full sm:w-fit min-w-[8.75rem] shrink-0">
                            <option value="">@lang('modules.report.allWaiters')</option>
                            @foreach($waiters ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </x-select>

                        <div id="item-report-date-range" class="flex flex-wrap items-center gap-2 min-w-0 w-full sm:w-auto">
                            <div class="relative w-full sm:w-40 min-w-0" wire:key="start-date-{{ $startDate }}">
                                <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-2.5 z-10">
                                    <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <x-datepicker wire:model.change='startDate' placeholder="@lang('app.selectStartDate')" class="pl-9 text-xs" />
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 whitespace-nowrap shrink-0 px-0.5">@lang('app.to')</span>
                            <div class="relative w-full sm:w-40 min-w-0" wire:key="end-date-{{ $endDate }}">
                                <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-2.5 z-10">
                                    <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </div>
                                <x-datepicker wire:model.live='endDate' placeholder="@lang('app.selectEndDate')" class="pl-9 text-xs" />
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 min-w-0 w-full lg:w-auto">
                            <div class="w-full sm:w-40 min-w-0">
                                <label for="start-time" class="sr-only">@lang('modules.reservation.timeStart'):</label>
                                <x-time-picker wire:model.live.debounce.500ms="startTime" value="{{ $startTime }}" />
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-300 whitespace-nowrap w-9 text-center shrink-0">@lang('app.to')</span>
                            <div class="w-full sm:w-40 min-w-0">
                                <label for="end-time" class="sr-only">@lang('modules.reservation.timeEnd'):</label>
                                <x-time-picker wire:model.live.debounce.500ms="endTime" value="{{ $endTime }}" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="w-full min-w-[36rem] border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="text-[10px] uppercase tracking-wide text-gray-600 bg-gray-50 dark:bg-gray-700/80 dark:text-gray-300">
                <tr>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('item_name')" class="flex items-center gap-0.5 text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'item_name' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.menu.itemName')
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path @class(['opacity-100' => $sortBy === 'item_name' && $sortDirection === 'asc', 'opacity-30' => !($sortBy === 'item_name' && $sortDirection === 'asc')]) fill="currentColor" d="M11 7h-6l3-4z"></path>
                                <path @class(['opacity-100' => $sortBy === 'item_name' && $sortDirection === 'desc', 'opacity-30' => !($sortBy === 'item_name' && $sortDirection === 'desc')]) fill="currentColor" d="M5 9h6l-3 4z"></path>
                            </svg>
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-left rtl:text-right">
                        <button type="button" wire:click="sortByToggle('category_name')" class="flex items-center gap-0.5 text-start hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'category_name' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.menu.categoryName')
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path @class(['opacity-100' => $sortBy === 'category_name' && $sortDirection === 'asc', 'opacity-30' => !($sortBy === 'category_name' && $sortDirection === 'asc')]) fill="currentColor" d="M11 7h-6l3-4z"></path>
                                <path @class(['opacity-100' => $sortBy === 'category_name' && $sortDirection === 'desc', 'opacity-30' => !($sortBy === 'category_name' && $sortDirection === 'desc')]) fill="currentColor" d="M5 9h6l-3 4z"></path>
                            </svg>
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        <button type="button" wire:click="sortByToggle('quantity_sold')" class="inline-flex items-center gap-0.5 justify-center mx-auto hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'quantity_sold' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.quantitySold')
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path @class(['opacity-100' => $sortBy === 'quantity_sold' && $sortDirection === 'asc', 'opacity-30' => !($sortBy === 'quantity_sold' && $sortDirection === 'asc')]) fill="currentColor" d="M11 7h-6l3-4z"></path>
                                <path @class(['opacity-100' => $sortBy === 'quantity_sold' && $sortDirection === 'desc', 'opacity-30' => !($sortBy === 'quantity_sold' && $sortDirection === 'desc')]) fill="currentColor" d="M5 9h6l-3 4z"></path>
                            </svg>
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 text-center">
                        <button type="button" wire:click="sortByToggle('price')" class="inline-flex items-center gap-0.5 justify-center mx-auto hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'price' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.sellingPrice')
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path @class(['opacity-100' => $sortBy === 'price' && $sortDirection === 'asc', 'opacity-30' => !($sortBy === 'price' && $sortDirection === 'asc')]) fill="currentColor" d="M11 7h-6l3-4z"></path>
                                <path @class(['opacity-100' => $sortBy === 'price' && $sortDirection === 'desc', 'opacity-30' => !($sortBy === 'price' && $sortDirection === 'desc')]) fill="currentColor" d="M5 9h6l-3 4z"></path>
                            </svg>
                        </button>
                    </th>
                    <th scope="col" class="px-3 py-2 ltr:text-end rtl:text-right">
                        <button type="button" wire:click="sortByToggle('total_revenue')" class="flex items-center gap-0.5 ltr:ml-auto rtl:mr-auto hover:text-gray-900 dark:hover:text-white transition-colors {{ $sortBy === 'total_revenue' ? 'font-bold text-gray-900 dark:text-white' : 'font-medium' }}">
                            @lang('modules.report.totalRevenue')
                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path @class(['opacity-100' => $sortBy === 'total_revenue' && $sortDirection === 'asc', 'opacity-30' => !($sortBy === 'total_revenue' && $sortDirection === 'asc')]) fill="currentColor" d="M11 7h-6l3-4z"></path>
                                <path @class(['opacity-100' => $sortBy === 'total_revenue' && $sortDirection === 'desc', 'opacity-30' => !($sortBy === 'total_revenue' && $sortDirection === 'desc')]) fill="currentColor" d="M5 9h6l-3 4z"></path>
                            </svg>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80 bg-white dark:bg-gray-800">
                @forelse ($this->menuItems as $item)
                    @if($item->variations_count > 0)
                        <!-- For items with variations, show each variation as a separate row -->
                        @foreach($item->variations as $variation)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-3 py-2">
                                    <div class="text-xs font-medium text-gray-900 dark:text-white">
                                        {{ $item->item_name }} <span class="text-gray-500 dark:text-gray-400 font-normal">({{ $variation->variation }})</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-xs font-medium text-gray-900 dark:text-white max-w-[10rem] truncate" title="{{ $item->category->category_name ?? '' }}">
                                    {{ $item->category->category_name ?? '' }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ $variation->quantity_sold }}</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($variation->price, restaurant()->currency_id) }}</span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($variation->total_revenue, restaurant()->currency_id) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- For items without variations, show a single row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-3 py-2">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">
                                    {{ $item->item_name }}
                                </div>
                            </td>
                            <td class="px-3 py-2 text-xs font-medium text-gray-900 dark:text-white max-w-[10rem] truncate" title="{{ $item->category->category_name ?? '' }}">
                                {{ $item->category->category_name ?? '' }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ $item->quantity_sold }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($item->price, restaurant()->currency_id) }}</span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <span class="text-xs font-medium tabular-nums text-gray-900 dark:text-white">{{ currency_format($item->total_revenue, restaurant()->currency_id) }}</span>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-sm text-center text-gray-500 dark:text-gray-400">
                            @lang('messages.noItemAdded')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
