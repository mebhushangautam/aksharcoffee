<div class="grid">
    <div class="p-4">
        <div class="grid w-full grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @if (user_can('Show Order'))
                @include('livewire.dashboard.today-order-count', [
                    'orderCount' => $todayOrderCount,
                    'percentChange' => $todayOrderPercentChange,
                ])
            @endif

            @if (user_can('Show Reports'))
                @include('livewire.dashboard.today-earnings', [
                    'orderCount' => $todayEarningsTotal,
                    'percentChange' => $todayEarningsPercentChange,
                ])
            @endif

            @if (user_can('Show Customer'))
                @include('livewire.dashboard.today-customer-count', [
                    'orderCount' => $todayCustomerCount,
                    'percentChange' => $todayCustomerPercentChange,
                ])
            @endif

            @if (user_can('Show Reports'))
                @include('livewire.dashboard.average-daily-earning', [
                    'orderCount' => $averageDailyEarning,
                    'percentChange' => $averageDailyPercentChange,
                ])
            @endif
        </div>
    </div>

    <div class="grid w-full grid-cols-1 lg:grid-cols-[3fr_2fr] gap-4 p-4">
        @if (user_can('Show Reports'))
            <div class="grid w-full min-w-0 col-span-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 p-5 dark:border-gray-700">
                @include('livewire.dashboard.weekly-sales-chart', [
                    'salesData' => $salesData,
                    'monthlyEarnings' => $monthlyEarnings,
                    'percentChange' => $chartPercentChange,
                ])
            </div>
        @endif

        @if (user_can('Show Order'))
            <div class="min-w-0 w-full">
                @include('livewire.dashboard.today-order-list', [
                    'waiterOrders' => $waiterOrders,
                    'orders' => $orders,
                ])
            </div>
        @endif
    </div>

    @if (user_can('Show Reports'))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 p-4">
            @include('livewire.dashboard.today-payment-method-earnings', ['paymentMethods' => $paymentMethods])

            @include('livewire.dashboard.today-menu-item-earnings', ['menuItems' => $menuItems])

            @include('livewire.dashboard.today-table-earnings', ['orders' => $tableEarningsOrders])
        </div>
    @endif
</div>
