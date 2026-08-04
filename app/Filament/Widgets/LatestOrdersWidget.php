<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function getHeading(): ?string
    {
        return app()->getLocale() === 'en' ? 'Latest Orders' : 'أحدث الطلبات';
    }

    public function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->query(
                Order::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label($isEn ? 'Order Number' : 'رقم الطلب'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'Customer' : 'العميل')
                    ->default(fn ($record) => $record->user_name ?? ($isEn ? 'Guest' : 'زائر')),

                Tables\Columns\BadgeColumn::make('order_status')
                    ->label($isEn ? 'Order Status' : 'حالة الطلب')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => $isEn ? 'Pending' : 'قيد الانتظار',
                        'processing' => $isEn ? 'Processing' : 'جاري التحضير',
                        'shipped' => $isEn ? 'Shipped' : 'تم الشحن',
                        'delivered' => $isEn ? 'Delivered' : 'تم التسليم',
                        'cancelled' => $isEn ? 'Cancelled' : 'ملغي',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label($isEn ? 'Total' : 'الإجمالي')
                    ->money('OMR'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Date' : 'التاريخ')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}
