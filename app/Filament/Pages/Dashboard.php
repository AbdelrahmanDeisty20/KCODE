<?php

namespace App\Filament\Pages;

use App\Support\StaticCache;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-home';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Dashboard' : 'لوحة التحكم';
    }

    protected function getHeaderActions(): array
    {
        $isEn = app()->getLocale() === 'en';

        return [
            Action::make('flushStaticCache')
                ->label($isEn ? 'Refresh Cache' : 'تحديث الكاش')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () use ($isEn) {
                    StaticCache::flushAll();

                    Notification::make()
                        ->title($isEn ? 'Cache refreshed successfully' : 'تم تحديث كاش البيانات بنجاح')
                        ->body($isEn
                            ? 'All cached static data (settings, FAQs, policies, categories, brands, etc.) has been cleared.'
                            : 'تم مسح وتجديد كاش جميع البيانات الثابتة (الإعدادات، الأسئلة الشائعة، السياسات، الأقسام، الماركات وغيرها).')
                        ->success()
                        ->send();
                }),
        ];
    }
}
