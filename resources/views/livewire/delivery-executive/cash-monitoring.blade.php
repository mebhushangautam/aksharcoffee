<div class="cash-monitoring  mx-auto px-3 sm:px-4 lg:px-6 pb-6 space-y-3 text-sm">
    <div class="p-3 sm:p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="mb-3">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight">@lang('menu.deliveryExecutiveCodMonitoring')</h1>
            <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-400 leading-relaxed">@lang('modules.delivery.codMonitoringDescription')</p>
        </div>

        @if ($tableMissing)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-700/40 dark:bg-amber-900/10 dark:text-amber-200 leading-relaxed">
                @lang('modules.delivery.codMonitoringMigrationMessage')
            </div>
        @else
            {{-- Compact overview: same idea as tax report "Today's Tax Summary" inner mini-cards --}}
            <div class="mb-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200/80 dark:border-blue-800">
                <h2 class="text-xs font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @lang('menu.codMonitoring')
                </h2>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="bg-white dark:bg-gray-800 p-2.5 rounded-md border border-gray-100 dark:border-gray-700 shadow-sm min-w-0">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-0.5 line-clamp-2 leading-snug">@lang('modules.delivery.dueToCollect')</p>
                        <p class="text-sm font-bold tabular-nums text-amber-600 dark:text-amber-400 sm:text-base leading-tight">{{ currency_format($totals['due_collection_total'], restaurant()->currency_id) }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ trans_choice('modules.delivery.ordersCountLabel', $totals['due_collection_orders'], ['count' => $totals['due_collection_orders']]) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-2.5 rounded-md border border-gray-100 dark:border-gray-700 shadow-sm min-w-0">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-0.5 line-clamp-2 leading-snug">@lang('modules.delivery.readyForSettlement')</p>
                        <p class="text-sm font-bold tabular-nums text-blue-600 dark:text-blue-400 sm:text-base leading-tight">{{ currency_format($totals['ready_settlement_total'], restaurant()->currency_id) }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ trans_choice('modules.delivery.ordersCountLabel', $totals['ready_settlement_orders'], ['count' => $totals['ready_settlement_orders']]) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-2.5 rounded-md border border-gray-100 dark:border-gray-700 shadow-sm min-w-0">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-0.5 line-clamp-2 leading-snug">@lang('modules.delivery.submittedForApproval')</p>
                        <p class="text-sm font-bold tabular-nums text-violet-600 dark:text-violet-400 sm:text-base leading-tight">{{ currency_format($totals['submitted_settlement_total'], restaurant()->currency_id) }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ trans_choice('modules.delivery.ordersCountLabel', $totals['submitted_settlement_orders'], ['count' => $totals['submitted_settlement_orders']]) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-2.5 rounded-md border border-gray-100 dark:border-gray-700 shadow-sm min-w-0">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-0.5 line-clamp-2 leading-snug">@lang('modules.delivery.totalSettled')</p>
                        <p class="text-sm font-bold tabular-nums text-emerald-600 dark:text-emerald-400 sm:text-base leading-tight">{{ currency_format($totals['settled_total'], restaurant()->currency_id) }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ trans_choice('modules.delivery.ordersCountLabel', $totals['settled_orders'], ['count' => $totals['settled_orders']]) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-2.5 rounded-md border border-gray-100 dark:border-gray-700 shadow-sm min-w-0">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-0.5 line-clamp-2 leading-snug">@lang('modules.delivery.collectedToday')</p>
                        <p class="text-sm font-bold tabular-nums text-cyan-600 dark:text-cyan-400 sm:text-base leading-tight">{{ currency_format($totals['collected_today_total'], restaurant()->currency_id) }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ trans_choice('modules.delivery.ordersCountLabel', $totals['collected_today_orders'], ['count' => $totals['collected_today_orders']]) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-2.5 rounded-md border border-gray-100 dark:border-gray-700 shadow-sm min-w-0">
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 mb-0.5 line-clamp-2 leading-snug">@lang('modules.delivery.totalCodOrders')</p>
                        <p class="text-sm font-bold tabular-nums text-rose-600 dark:text-rose-400 sm:text-base leading-tight">{{ currency_format($totals['total_cod_amount'], restaurant()->currency_id) }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ trans_choice('modules.delivery.ordersCountLabel', $totals['total_cod_orders'], ['count' => $totals['total_cod_orders']]) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 rounded-lg bg-gray-50 p-3 dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600">
                <x-input
                    id="cash-monitor-search"
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    class="w-full sm:w-56 text-xs"
                    placeholder="{{ __('modules.delivery.searchCodCollections') }}"
                />
                @if ($activeTab !== 'summary')
                    <x-select wire:model.live="status" class="block w-full sm:w-fit min-w-[9rem] text-xs">
                        <option value="">@lang('modules.delivery.allStatuses')</option>
                        <option value="pending_collection">@lang('modules.delivery.pendingCollection')</option>
                        <option value="collected">@lang('modules.delivery.collected')</option>
                        <option value="submitted">@lang('modules.delivery.submitted')</option>
                        <option value="settled">@lang('modules.delivery.settled')</option>
                    </x-select>
                @endif
                <a href="{{ route('reports.cod') }}"
                    class="inline-flex items-center justify-center px-2 py-1.5 text-xs font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:ring-2 focus:ring-primary-300 w-full sm:w-auto dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600">
                    @lang('menu.codReport')
                </a>
                @if (in_array('Export Report', restaurant_modules()))
                    <a href="javascript:;" wire:click="exportReport"
                        class="inline-flex items-center justify-center px-2 py-1.5 text-xs font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:ring-2 focus:ring-primary-300 w-full sm:w-auto dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-1.5 -ml-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586z" clip-rule="evenodd"/></svg>
                        @lang('app.export')
                    </a>
                @endif
            </div>

            <div class="mt-3 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex flex-wrap gap-1">
                    <button type="button" wire:click="switchTab('settlements')" @class([
                        'border-b-2 px-2 py-2 text-xs sm:text-sm font-medium rounded-t-md',
                        'border-skin-base text-skin-base' => $activeTab === 'settlements',
                        'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'settlements',
                    ])>@lang('modules.delivery.settlementHistory')</button>
                    <button type="button" wire:click="switchTab('summary')" @class([
                        'border-b-2 px-2 py-2 text-xs sm:text-sm font-medium rounded-t-md',
                        'border-skin-base text-skin-base' => $activeTab === 'summary',
                        'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'summary',
                    ])>@lang('modules.delivery.executivePendingSummary')</button>
                    <button type="button" wire:click="switchTab('orders')" @class([
                        'border-b-2 px-2 py-2 text-xs sm:text-sm font-medium rounded-t-md',
                        'border-skin-base text-skin-base' => $activeTab === 'orders',
                        'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $activeTab !== 'orders',
                    ])>@lang('modules.delivery.orderWiseCodList')</button>
                </nav>
            </div>
        @endif
    </div>

    @if (!$tableMissing)
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

            @if ($activeTab === 'orders')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/80">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.order.orderNumber')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.customer.customer')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('menu.deliveryExecutive')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.delivery.dueAmount')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.delivery.collectedAmount')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('app.status')</th>
                                <th scope="col" class="px-3 py-2 text-right text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('app.dateTime')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700/80 dark:bg-gray-800">
                            @forelse ($collections as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">{{ $item->order?->show_formatted_order_number ?? '--' }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300 max-w-[10rem] truncate" title="{{ $item->order?->customer?->name ?? '' }}">{{ $item->order?->customer?->name ?? '--' }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300">{{ $item->deliveryExecutive?->name ?? '--' }}</td>
                                    <td class="px-3 py-2 text-xs tabular-nums text-gray-900 dark:text-white">{{ currency_format($item->expected_amount, restaurant()->currency_id) }}</td>
                                    <td class="px-3 py-2 text-xs tabular-nums text-gray-900 dark:text-white">{{ currency_format((float) ($item->collected_amount ?? 0), restaurant()->currency_id) }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            {{
                                                match ($item->status) {
                                                    'pending_collection' => __('modules.delivery.pendingCollection'),
                                                    'collected' => __('modules.delivery.collected'),
                                                    'submitted' => __('modules.delivery.submitted'),
                                                    'settled' => __('modules.delivery.settled'),
                                                    default => ucwords(str_replace('_', ' ', $item->status)),
                                                }
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        @include('common.date-time-display', ['date' => $item->recorded_at ?? $item->updated_at])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.noCodCollectionsFound')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($collections->hasPages())
                    <div class="border-t border-gray-200 px-3 py-3 dark:border-gray-700">
                        {{ $collections->links() }}
                    </div>
                @endif
            @elseif ($activeTab === 'settlements')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/80">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.delivery.settlementNumber')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('menu.deliveryExecutive')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.delivery.orderCount')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.delivery.submittedAmount')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('modules.delivery.submittedAt')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('app.note')</th>
                                <th scope="col" class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('app.status')</th>
                                <th scope="col" class="px-3 py-2 text-right text-[10px] font-medium uppercase tracking-wide text-gray-600 dark:text-gray-300">@lang('app.action')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-700/80 dark:bg-gray-800">
                            @forelse ($settlements as $settlement)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-3 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap">{{ $settlement->settlement_number ?? '--' }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300">{{ $settlement->deliveryExecutive?->name ?? '--' }}</td>
                                    <td class="px-3 py-2 text-xs tabular-nums text-gray-700 dark:text-gray-300">{{ $settlement->items->count() }}</td>
                                    <td class="px-3 py-2 text-xs tabular-nums text-gray-900 dark:text-white">{{ currency_format($settlement->submitted_amount, restaurant()->currency_id) }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        @include('common.date-time-display', ['date' => $settlement->submitted_at])
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300 max-w-[12rem] truncate" title="{{ $settlement->notes ?? '' }}">{{ $settlement->notes ?: '--' }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-300">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            {{
                                                match ($settlement->status) {
                                                    'submitted' => __('modules.delivery.submitted'),
                                                    'approved' => __('modules.delivery.settled'),
                                                    'rejected' => __('modules.delivery.rejected'),
                                                    default => ucwords(str_replace('_', ' ', $settlement->status)),
                                                }
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right text-xs">
                                        @if ($settlement->status === 'submitted')
                                            <div class="flex flex-wrap justify-end gap-1.5">
                                                <button type="button" wire:click="approveSettlement({{ $settlement->id }})" class="rounded-md bg-emerald-600 px-2.5 py-1 text-[11px] font-medium text-white hover:bg-emerald-700">
                                                    @lang('modules.delivery.approveSettlement')
                                                </button>
                                                <button type="button" wire:click="rejectSettlement({{ $settlement->id }})" class="rounded-md bg-red-600 px-2.5 py-1 text-[11px] font-medium text-white hover:bg-red-700">
                                                    @lang('modules.delivery.rejectSettlement')
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $settlement->approved_at?->timezone(timezone())->format(dateFormat() . ' ' . timeFormat()) ?? '--' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.noSettlementHistoryFound')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700/80">
                    @forelse ($summary as $executive)
                        <div class="flex items-center justify-between px-3 py-2.5 gap-3">
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $executive->deliveryExecutive?->name ?? '--' }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ ($executive->deliveryExecutive?->phone_code ? '+'. $executive->deliveryExecutive?->phone_code . ' ' : '') . ($executive->deliveryExecutive?->phone ?? '--') }}</div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs font-semibold tabular-nums text-gray-900 dark:text-white">{{ currency_format((float) ($executive->cod_pending_amount ?? 0), restaurant()->currency_id) }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ trans_choice('modules.delivery.pendingOrdersCount', (int) ($executive->cod_pending_orders ?? 0), ['count' => (int) ($executive->cod_pending_orders ?? 0)]) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">@lang('modules.delivery.noCodSummaryFound')</div>
                    @endforelse
                </div>
            @endif
        </div>
    @endif
</div>
