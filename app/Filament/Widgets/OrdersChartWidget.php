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
            ? 'Orders Analytics Chart (Last 7 Days)'
            : 'مخطط أداء تحليل الطلبات (آخر 7 أيام)';
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
                    'label' => $isEn ? 'Daily Orders Count' : 'عدد الطلبات اليومية',
                    'data'  => $data,
                    'fill'  => 'start',
                    'borderColor' => '#c25975',
                    'backgroundColor' => 'rgba(194, 89, 117, 0.15)',
                    'pointBackgroundColor' => '#c25975',
                    'pointBorderColor' => '#ffffff',
                    'pointHoverBackgroundColor' => '#ffffff',
                    'pointHoverBorderColor' => '#c25975',
                    'tension' => 0.45,
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
