<?php

namespace App\Filament\Widgets;

use App\Models\Assessment;
use App\Models\ChatbotMessage;
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
        $totalChatbotQueries = ChatbotMessage::count();

        // 7-day trend arrays for sparkline charts
        $ordersTrend = [];
        $revenueTrend = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $ordersTrend[] = Order::whereDate('created_at', $date)->count();
            $revenueTrend[] = (float) Order::where('payment_status', 'paid')->whereDate('created_at', $date)->sum('total');
        }

        $currency = $isEn ? 'EGP ' : ' ج.م ';

        return [
            Stat::make(
                $isEn ? 'Total Sales' : 'إجمالي المبيعات',
                $currency . number_format($totalRevenue, 2)
            )
                ->description($isEn ? 'Total Paid Revenue' : 'إجمالي الإيرادات المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($revenueTrend)
                ->color('success'),

            Stat::make(
                $isEn ? 'Total Orders' : 'إجمالي الطلبات',
                number_format($totalOrders)
            )
                ->description($isEn ? 'Store Orders Count' : 'عدد طلبات المتجر')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart($ordersTrend)
                ->color('primary'),

            Stat::make(
                $isEn ? 'AI Skincare Consultations' : 'استشارات المستشار الذكي (AI)',
                number_format($totalChatbotQueries)
            )
                ->description($isEn ? 'AI Conversations Count' : 'مجموع محادثات الذكاء الاصطناعي')
                ->descriptionIcon('heroicon-m-sparkles')
                ->chart([3, 5, 8, 12, 15, 20, max(1, $totalChatbotQueries)])
                ->color('warning'),

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
                ->color('success'),
        ];
    }
}
