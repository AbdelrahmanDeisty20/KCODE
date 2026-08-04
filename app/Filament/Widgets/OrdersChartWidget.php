<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): ?string
    {
        return app()->getLocale() === 'en'
            ? 'Orders Performance Chart (Last 7 Days)'
            : 'مخطط أداء الطلبات (آخر 7 أيام)';
    }

    protected function getData(): array
    {
        $isEn = app()->getLocale() === 'en';
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->translatedFormat('d M');
            $data[] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => $isEn ? 'Number of Orders' : 'عدد الطلبات',
                    'data'  => $data,
                    'fill'  => 'start',
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.18)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
