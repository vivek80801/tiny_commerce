<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Category;

class ProdcutStatWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Total Products", Product::count()),
            Stat::make("Total Categories", Category::count()),
        ];
    }
}
