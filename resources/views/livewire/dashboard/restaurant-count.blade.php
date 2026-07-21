<div
    class="flex h-full w-full flex-col rounded-lg border border-gray-200 bg-white p-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-3">
    <h3 class="text-[10px] font-medium uppercase leading-snug tracking-wide text-gray-500 dark:text-gray-400 sm:text-xs">
        @lang('modules.dashboard.todayRestaurantCount')
    </h3>
    <div class="mt-2 flex min-w-0 flex-1 items-end justify-between gap-2">
        <span class="text-lg font-bold tabular-nums leading-none text-gray-900 dark:text-white sm:text-xl">{{ $orderCount }}</span>
        <div class="flex max-w-[55%] shrink-0 flex-col items-end gap-0.5 text-right">
            @if ($percentChange === null)
                <p class="text-[10px] leading-tight text-gray-500 dark:text-gray-400 sm:text-xs">
                    @lang('modules.dashboard.noPreviousPeriodData')
                </p>
            @else
                <p class="inline-flex items-center gap-0.5 text-[10px] font-normal text-gray-500 dark:text-gray-400 sm:text-xs">
                    <span @class(['inline-flex items-center', 'text-green-500 dark:text-green-400' => ($percentChange > 0), 'text-red-600 dark:text-red-400' => ($percentChange < 0)])>
                        <svg class="h-3 w-3 shrink-0 sm:h-3.5 sm:w-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            @if ($percentChange > 0)
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z"></path>
                            @endif
                            @if ($percentChange < 0)
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z"></path>
                            @endif
                        </svg>
                        <span class="whitespace-nowrap tabular-nums">{{ round($percentChange, 2) }}%</span>
                    </span>
                </p>
            @endif
            <p class="text-[10px] leading-tight text-gray-500 dark:text-gray-400 sm:text-xs">
                @lang('modules.dashboard.sinceYesterday')
            </p>
        </div>
    </div>
</div>
