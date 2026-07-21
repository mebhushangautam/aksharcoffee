<?php

namespace App\Livewire\Dashboard;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Scopes\AvailableMenuItemScope;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RestaurantDashboard extends Component
{
    protected $listeners = ['refreshOrders' => '$refresh'];

    public function render()
    {
        return view('livewire.dashboard.restaurant-dashboard', $this->gatherViewData());
    }

    /**
     * @return array<string, mixed>
     */
    protected function gatherViewData(): array
    {
        $boundaries = getBusinessDayBoundaries(branch(), now());
        $startUTC = $boundaries['start']->setTimezone('UTC')->toDateTimeString();
        $endUTC = $boundaries['end']->setTimezone('UTC')->toDateTimeString();

        $yesterdayBoundaries = getBusinessDayBoundaries(branch(), now()->subDay());
        $yesterdayStartUTC = $yesterdayBoundaries['start']->setTimezone('UTC')->toDateTimeString();
        $yesterdayEndUTC = $yesterdayBoundaries['end']->setTimezone('UTC')->toDateTimeString();

        $isWaiter = user()->hasRole('Waiter_' . user()->restaurant_id);

        // --- Today order count stat ---
        $todayOrderQuery = Order::where('orders.date_time', '>=', $startUTC)
            ->where('orders.date_time', '<=', $endUTC)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft');
        if ($isWaiter) {
            $todayOrderQuery->where('waiter_id', user()->id);
        }
        $todayOrderCount = $todayOrderQuery->count();

        $yesterdayOrderQuery = Order::where('orders.date_time', '>=', $yesterdayStartUTC)
            ->where('orders.date_time', '<=', $yesterdayEndUTC)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft');
        if ($isWaiter) {
            $yesterdayOrderQuery->where('waiter_id', user()->id);
        }
        $yesterdayOrderCountForStat = $yesterdayOrderQuery->count();
        $todayOrderPercentChange = calculatePercentChange($todayOrderCount, $yesterdayOrderCountForStat);

        // --- Today earnings stat ---
        $todayEarningsTotal = Order::where('orders.date_time', '>=', $startUTC)
            ->where('orders.date_time', '<=', $endUTC)
            ->where('status', 'paid')
            ->sum('total');
        $yesterdayEarningsTotal = Order::where('orders.date_time', '>=', $yesterdayStartUTC)
            ->where('orders.date_time', '<=', $yesterdayEndUTC)
            ->where('status', 'paid')
            ->sum('total');
        $todayEarningsPercentChange = calculatePercentChange($todayEarningsTotal, $yesterdayEarningsTotal);

        // --- Today customer count stat ---
        $todayCustCount = Order::where('orders.date_time', '>=', $startUTC)
            ->where('orders.date_time', '<=', $endUTC)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->distinct()->count('customer_id');
        $yesterdayCustCount = Order::where('orders.date_time', '>=', $yesterdayStartUTC)
            ->where('orders.date_time', '<=', $yesterdayEndUTC)
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->distinct()->count('customer_id');
        $todayCustomerPercentChange = calculatePercentChange($todayCustCount, $yesterdayCustCount);

        // --- Average daily earning stat (same logic as AverageDailyEarning::mount) ---
        $daysInMonth = now()->format('d');
        $daysInPreviousMonth = now()->subMonth()->daysInMonth;
        $totalEarningsMonth = Order::where('status', 'paid')
            ->whereYear('date_time', now()->year)
            ->whereMonth('date_time', now()->month)
            ->sum('total');
        $totalPreviousMonth = Order::where('status', 'paid')
            ->whereYear('date_time', now()->subMonth()->year)
            ->whereMonth('date_time', now()->subMonth()->month)
            ->sum('total');
        $averageDailyEarning = ($totalEarningsMonth / $daysInMonth);
        $averageDailyPrevious = $totalPreviousMonth / $daysInPreviousMonth;
        $averageDailyPercentChange = calculatePercentChange($averageDailyEarning, $averageDailyPrevious);

        // --- Weekly / monthly chart ---
        $startOfMonth = now()->startOfMonth()->startOfDay()->toDateTimeString();
        $tillToday = now()->endOfDay()->toDateTimeString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->startOfDay()->toDateTimeString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->endOfDay()->toDateTimeString();

        $salesData = Order::select(
            DB::raw('DATE(date_time) as date'),
            DB::raw('SUM(total) as total_sales')
        )
            ->whereDate('orders.date_time', '>=', $startOfMonth)->whereDate('orders.date_time', '<=', $tillToday)
            ->where('status', 'paid')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $monthlyEarnings = Order::whereDate('orders.date_time', '>=', $startOfMonth)->whereDate('orders.date_time', '<=', $tillToday)
            ->where('status', 'paid')
            ->sum('total');
        $previousEarnings = Order::whereDate('orders.date_time', '>=', $startOfLastMonth)->whereDate('orders.date_time', '<=', $endOfLastMonth)
            ->where('status', 'paid')
            ->sum('total');
        $chartPercentChange = calculatePercentChange($monthlyEarnings, $previousEarnings);

        // --- Today order list ---
        $ordersQuery = Order::withCount('items')->with(['table.area', 'waiter', 'orderType', 'customer', 'kot'])
            ->where('status', '<>', 'canceled')
            ->where('status', '<>', 'draft')
            ->orderBy('id', 'desc')
            ->where('orders.date_time', '>=', $startUTC)
            ->where('orders.date_time', '<=', $endUTC);
        if ($isWaiter) {
            $ordersQuery->where('waiter_id', user()->id);
        }
        $todayOrdersList = $ordersQuery->get();

        // --- Payment methods ---
        $paymentMethods = Payment::join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.payment_method', '<>', 'due')
            ->where('orders.date_time', '>=', $startUTC)
            ->where('orders.date_time', '<=', $endUTC)
            ->select('payments.payment_method', DB::raw('SUM(payments.amount) as total_amount'))
            ->groupBy('payments.payment_method')
            ->get()->sortBy('total_amount', SORT_REGULAR, true);

        // --- Top menu items (preserve legacy component logic) ---
        $menuQuery = MenuItem::withoutGlobalScope(AvailableMenuItemScope::class)->with(['orders' => function ($q) use ($startUTC, $endUTC) {
            return $q->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', 'paid')
                ->where('orders.date_time', '>=', $startUTC)
                ->where('orders.date_time', '<=', $endUTC);
        }])->get();

        $menuQuery->map(function ($order) {
            $order['total'] = $order->orders->sum('amount');

            return $order;
        });

        $topMenuItems = $menuQuery->filter(function ($order) {
            return ($order->total > 0);
        })->sortBy('total', SORT_REGULAR, true)->splice(0, 5);

        // --- Top tables ---
        $topTableOrders = Order::select('table_id', DB::raw('SUM(total) as total_price'))
            ->with('table.area')
            ->whereNotNull('table_id')
            ->where('orders.date_time', '>=', $startUTC)
            ->where('orders.date_time', '<=', $endUTC)
            ->groupBy('table_id')
            ->where('status', 'paid')
            ->get()->sortBy('total_price', SORT_REGULAR, true)->splice(0, 5);

        return [
            'todayOrderCount' => $todayOrderCount,
            'todayOrderPercentChange' => $todayOrderPercentChange,
            'todayEarningsTotal' => $todayEarningsTotal,
            'todayEarningsPercentChange' => $todayEarningsPercentChange,
            'todayCustomerCount' => $todayCustCount,
            'todayCustomerPercentChange' => $todayCustomerPercentChange,
            'averageDailyEarning' => $averageDailyEarning,
            'averageDailyPercentChange' => $averageDailyPercentChange,
            'salesData' => $salesData,
            'monthlyEarnings' => $monthlyEarnings,
            'chartPercentChange' => $chartPercentChange,
            'waiterOrders' => $todayOrdersList,
            'orders' => $todayOrdersList,
            'paymentMethods' => $paymentMethods,
            'menuItems' => $topMenuItems,
            'tableEarningsOrders' => $topTableOrders,
        ];
    }
}
