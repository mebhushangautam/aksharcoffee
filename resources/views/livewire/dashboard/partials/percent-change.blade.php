@if ($percentChange === null)
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
        @lang('modules.dashboard.noPreviousPeriodData')
    </p>
@else
    <p @class([
        'text-xs mt-1 flex items-center gap-1',
        $positiveColor ?? 'text-emerald-600' => $percentChange > 0,
        'text-red-600 dark:text-red-400' => $percentChange < 0,
        'text-gray-500 dark:text-gray-400' => $percentChange == 0,
    ])>
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            @if ($percentChange > 0)
                <path clip-rule="evenodd" fill-rule="evenodd"
                    d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z">
                </path>
            @endif
            @if ($percentChange < 0)
                <path clip-rule="evenodd" fill-rule="evenodd"
                    d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z">
                </path>
            @endif
        </svg>
        {{ round($percentChange, 2) }}% {{ $comparisonLabel }}
    </p>
@endif
