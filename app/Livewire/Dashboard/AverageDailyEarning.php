<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AverageDailyEarning extends Component
{

    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $currentMonth = now()->format('Y-m');
        $daysInMonth = now()->format('d');

        $previousMonth = now()->subMonth()->format('Y-m');
        $daysInPreviousMonth = now()->subMonth()->daysInMonth;
    
        $totalEarnings = Order::where('status', 'paid')
            ->whereYear('date_time', now()->year)
            ->whereMonth('date_time', now()->month)
            ->sum('total');

        $totalPreviousEarnings = Order::where('status', 'paid')
            ->whereYear('date_time', now()->subMonth()->year)
            ->whereMonth('date_time', now()->subMonth()->month)
            ->sum('total');
    
        $this->orderCount = ($totalEarnings / $daysInMonth);

        $averageDailyPreviousEarnings = $totalPreviousEarnings / $daysInPreviousMonth;

        $this->percentChange = calculatePercentChange($this->orderCount, $averageDailyPreviousEarnings);
    }

    public function render()
    {
        return view('livewire.dashboard.average-daily-earning');
    }

}
