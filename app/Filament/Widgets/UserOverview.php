<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Total Users", User::where("is_admin", "=", false)->count()),
            Stat::make("Total Admin", User::where("is_admin", "=", true)->count()),
        ];
    }
}
