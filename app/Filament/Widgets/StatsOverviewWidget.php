<?php

namespace App\Filament\Widgets;

use App\Models\Assessment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('type', 'user')->count();
        $totalQuizDone = Assessment::count();

        return [
            Stat::make('إجمالي المبيعات', 'EGP ' . number_format($totalRevenue, 2))
                ->description('إجمالي الإيرادات المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('إجمالي الطلبات', number_format($totalOrders))
                ->description('عدد طلبات المتجر')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('المنتجات المتاحة', number_format($totalProducts))
                ->description('عدد المنتجات في الكتالوج')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('العملاء المسجلين', number_format($totalUsers))
                ->description('إجمالي حسابات العملاء')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('اختبارات البشرة (Quiz)', number_format($totalQuizDone))
                ->description('عدد الاختبارات المكتملة')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('secondary'),
        ];
    }
}
