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
        $isEn = app()->getLocale() === 'en';

        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::where('type', 'user')->count();
        $totalQuizDone = Assessment::count();

        $currency = $isEn ? 'OMR ' : 'ر.ع ';

        return [
            Stat::make(
                $isEn ? 'Total Sales' : 'إجمالي المبيعات',
                $currency . number_format($totalRevenue, 2)
            )
                ->description($isEn ? 'Total Paid Revenue' : 'إجمالي الإيرادات المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(
                $isEn ? 'Total Orders' : 'إجمالي الطلبات',
                number_format($totalOrders)
            )
                ->description($isEn ? 'Store Orders Count' : 'عدد طلبات المتجر')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make(
                $isEn ? 'Available Products' : 'المنتجات المتاحة',
                number_format($totalProducts)
            )
                ->description($isEn ? 'Products in Catalog' : 'عدد المنتجات في الكتالوج')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make(
                $isEn ? 'Registered Customers' : 'العملاء المسجلين',
                number_format($totalUsers)
            )
                ->description($isEn ? 'Total Customer Accounts' : 'إجمالي حسابات العملاء')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make(
                $isEn ? 'Skin Quizzes' : 'اختبارات البشرة (Quiz)',
                number_format($totalQuizDone)
            )
                ->description($isEn ? 'Completed Quizzes Count' : 'عدد الاختبارات المكتملة')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('secondary'),
        ];
    }
}
