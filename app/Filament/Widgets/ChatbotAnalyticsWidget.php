<?php

namespace App\Filament\Widgets;

use App\Models\ChatbotMessage;
use Filament\Widgets\ChartWidget;

class ChatbotAnalyticsWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): ?string
    {
        return app()->getLocale() === 'en'
            ? 'AI Chatbot Consultations (Last 7 Days)'
            : 'نشاط استشارات المستشار الذكي AI (آخر 7 أيام)';
    }

    protected function getData(): array
    {
        $isEn = app()->getLocale() === 'en';
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->translatedFormat('d M');
            $data[] = ChatbotMessage::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => $isEn ? 'AI Queries Count' : 'عدد الاستشارات الذكية',
                    'data'  => $data,
                    'fill'  => 'start',
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#ffffff',
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
