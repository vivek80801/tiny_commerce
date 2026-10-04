<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserChart extends ChartWidget
{
    protected ?string $heading = 'User Chart';

    protected string|int|array $columnSpan = 'full';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $startDate = User::oldest()
            ->value('created_at');

        if ($activeFilter === 'week') {
            $startDate = now()->startOfWeek();
        }

        if ($activeFilter === 'month') {
            $startDate = now()->startOfMonth();
        }

        if ($activeFilter === 'year') {
            $startDate = now()->startOfYear();
        }

        $data = Trend::model(User::class)
            ->between(
                start: $startDate,
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'User Chart',
                    'data' => $data->map(
                        fn (TrendValue $value) => $value
                            ->aggregate
                    ),
                ],
            ],
            'labels' => $data->map(
                fn (TrendValue $value) => $value
                    ->date
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
