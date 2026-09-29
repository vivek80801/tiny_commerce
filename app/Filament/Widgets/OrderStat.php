<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count()),
            Stat::make('Order Today', Order::whereDate(
                'created_at',
                Carbon::today()
            )->count()),
            Stat::make('Order This Week', Order::whereBetween(
                'created_at',
                [
                    Carbon::getWeekStartsAt(),
                    Carbon::getWeekEndsAt(),
                ]
            )->count()),
        ];
    }
}
