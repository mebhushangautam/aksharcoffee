<div
    class="flex h-full w-full flex-col rounded-lg border border-gray-200 bg-white p-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-3">
    <h3 class="text-[10px] font-medium uppercase leading-snug tracking-wide text-gray-500 dark:text-gray-400 sm:text-xs">
        @lang('modules.dashboard.totalPaidRestaurantCount')
    </h3>
    <div class="mt-2 flex min-w-0 flex-1 items-end">
        <span class="text-lg font-bold tabular-nums leading-none text-gray-900 dark:text-white sm:text-xl">{{ $orderCount }}</span>
    </div>
</div>
