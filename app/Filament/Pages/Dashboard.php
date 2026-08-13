<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'icon-dashboard';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Dashboard' : 'لوحة التحكم';
    }
}
