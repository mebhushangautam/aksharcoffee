<div class="bg-white rounded-xl border border-gray-100 p-4 dark:bg-gray-800 dark:border-gray-700">
    <p class="text-xs text-gray-400 mb-1.5">@lang('modules.dashboard.averageDailyEarning') ({{ now()->translatedFormat('M') }})</p>
    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ currency_format(round($orderCount, 2), restaurant()->currency_id) }}</p>
    @if ($percentChange !== null && $percentChange != 0)
        @include('livewire.dashboard.partials.percent-change', [
            'percentChange' => $percentChange,
            'comparisonLabel' => __('modules.dashboard.sincePreviousMonth'),
            'positiveColor' => 'text-amber-600',
        ])
    @elseif ($percentChange === null)
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            @lang('modules.dashboard.noPreviousPeriodData')
        </p>
    @endif
</div>
