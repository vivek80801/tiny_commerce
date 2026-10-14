<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrderChart extends ChartWidget
{
    protected ?string $heading = 'Order Chart';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'today';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $startDate = Order::count() > 0
            ? Order::oldest()
                ->value('created_at')
            : now();

        $endDate = now();

        if ($activeFilter === 'week') {
            $startDate = now()->startOfWeek();
        }

        if ($activeFilter === 'month') {
            $startDate = now()->startOfMonth();
        }

        if ($activeFilter === 'year') {
            $startDate = now()->startOfYear();
        }

        $data = Trend::model(Order::class)
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Order Chart',
                    'data' => $data->map(
                        fn (TrendValue $value) => $value
                            ->aggregate
                    ),
                ],
            ],
            'labels' => $data->map(
                fn (TrendValue $value) => $value->date
            ),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
