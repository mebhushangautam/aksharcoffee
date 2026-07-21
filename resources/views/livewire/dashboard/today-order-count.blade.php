<div class="bg-white rounded-xl border border-gray-100 p-4 dark:bg-gray-800 dark:border-gray-700">
    <p class="text-xs text-gray-400 mb-1.5">@lang('modules.dashboard.todayOrderCount')</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $orderCount }}</p>
    @include('livewire.dashboard.partials.percent-change', [
        'percentChange' => $percentChange,
        'comparisonLabel' => __('modules.dashboard.sinceYesterday'),
    ])
</div>
