<div class="px-4">
    <div class="mb-3">
        <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">@lang('menu.deliveryDashboard')</h2>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.dashboardDescription')</p>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-xl border border-amber-200/70 bg-amber-50/50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="text-[11px] uppercase tracking-wide text-amber-800/90 dark:text-gray-300">@lang('modules.delivery.dueToCollect')</div>
            <div class="mt-0.5 text-[1.65rem] leading-tight font-semibold text-gray-900 dark:text-white">
                {{ currency_format($dueCollectionTotal, restaurant()->currency_id) }}
            </div>
            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                {{ trans_choice('modules.delivery.ordersCountLabel', $dueCollectionOrders, ['count' => $dueCollectionOrders]) }}
            </div>
        </div>

        <div class="rounded-xl border border-blue-200/70 bg-blue-50/50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="text-[11px] uppercase tracking-wide text-blue-800/90 dark:text-gray-300">@lang('modules.delivery.readyForSettlement')</div>
            <div class="mt-0.5 text-[1.65rem] leading-tight font-semibold text-gray-900 dark:text-white">
                {{ currency_format($readySettlementTotal, restaurant()->currency_id) }}
            </div>
            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                {{ trans_choice('modules.delivery.ordersCountLabel', $readySettlementOrders, ['count' => $readySettlementOrders]) }}
            </div>
        </div>

        <div class="rounded-xl border border-violet-200/70 bg-violet-50/50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="text-[11px] uppercase tracking-wide text-violet-800/90 dark:text-gray-300">@lang('modules.delivery.submittedForApproval')</div>
            <div class="mt-0.5 text-[1.65rem] leading-tight font-semibold text-gray-900 dark:text-white">
                {{ currency_format($submittedSettlementTotal, restaurant()->currency_id) }}
            </div>
            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                {{ trans_choice('modules.delivery.ordersCountLabel', $submittedSettlementOrders, ['count' => $submittedSettlementOrders]) }}
            </div>
        </div>

        <div class="rounded-xl border border-emerald-200/70 bg-emerald-50/50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="text-[11px] uppercase tracking-wide text-emerald-800/90 dark:text-gray-300">@lang('modules.delivery.totalSettled')</div>
            <div class="mt-0.5 text-[1.65rem] leading-tight font-semibold text-gray-900 dark:text-white">
                {{ currency_format($settledTotal, restaurant()->currency_id) }}
            </div>
            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                {{ trans_choice('modules.delivery.ordersCountLabel', $settledOrders, ['count' => $settledOrders]) }}
            </div>
        </div>

        <div class="rounded-xl border border-cyan-200/70 bg-cyan-50/50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="text-[11px] uppercase tracking-wide text-cyan-800/90 dark:text-gray-300">@lang('modules.delivery.collectedToday')</div>
            <div class="mt-0.5 text-[1.65rem] leading-tight font-semibold text-gray-900 dark:text-white">
                {{ currency_format($collectedTodayTotal, restaurant()->currency_id) }}
            </div>
            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                {{ trans_choice('modules.delivery.ordersCountLabel', $collectedTodayOrders, ['count' => $collectedTodayOrders]) }}
            </div>
        </div>

        <div class="rounded-xl border border-rose-200/70 bg-rose-50/50 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="text-[11px] uppercase tracking-wide text-rose-800/90 dark:text-gray-300">@lang('modules.delivery.totalCodOrders')</div>
            <div class="mt-0.5 text-[1.65rem] leading-tight font-semibold text-gray-900 dark:text-white">
                {{ currency_format($totalCodAmount, restaurant()->currency_id) }}
            </div>
            <div class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                {{ trans_choice('modules.delivery.ordersCountLabel', $totalCodOrders, ['count' => $totalCodOrders]) }}
            </div>
        </div>
    </div>

    <div class="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-2">
        <div class="rounded-xl border border-gray-200/80 bg-white p-3.5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">@lang('modules.delivery.orderWiseCodList')</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.dashboardPendingCollectionsDescription')</p>
            </div>

            @if ($pendingCollections->count())
                <div class="space-y-2.5">
                    @foreach ($pendingCollections as $collection)
                        <div class="rounded-lg border border-gray-200/90 p-2.5 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $collection->order?->show_formatted_order_number ?? '--' }}</div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $collection->order?->customer?->name ?? '--' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ currency_format((float) ($collection->expected_amount ?? 0), restaurant()->currency_id) }}
                                    </div>
                                    <div class="mt-1 text-xs text-amber-700 dark:text-amber-300">@lang('modules.delivery.pendingCollection')</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.noCodCollectionsFound')</p>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200/80 bg-white p-3.5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">@lang('modules.delivery.settlementHistory')</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.dashboardRecentSettlementsDescription')</p>
            </div>

            @if ($recentSettlements->count())
                <div class="space-y-2.5">
                    @foreach ($recentSettlements as $settlement)
                        <div class="rounded-lg border border-gray-200/90 p-2.5 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-2">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $settlement->settlement_number ?? '--' }}</div>
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    {{
                                        match ($settlement->status) {
                                            'submitted' => __('modules.delivery.submitted'),
                                            'approved' => __('modules.delivery.settled'),
                                            'rejected' => __('modules.delivery.rejected'),
                                            default => ucwords(str_replace('_', ' ', $settlement->status)),
                                        }
                                    }}
                                </span>
                            </div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                {{ currency_format((float) $settlement->submitted_amount, restaurant()->currency_id) }}
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $settlement->submitted_at?->timezone(timezone())->format(dateFormat() . ' ' . timeFormat()) ?? '--' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.noSettlementHistoryFound')</p>
            @endif
        </div>
    </div>
</div>
