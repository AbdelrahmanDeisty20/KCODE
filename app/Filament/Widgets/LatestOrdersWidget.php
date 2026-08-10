<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function getHeading(): ?string
    {
        return app()->getLocale() === 'en' ? 'Latest Store Orders' : 'أحدث طلبات المتجر';
    }

    public function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->query(
                Order::query()->latest()->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label($isEn ? 'Order Number' : 'رقم الطلب')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'Customer' : 'العميل')
                    ->default(fn ($record) => $record->user_name ?? ($isEn ? 'Guest Customer' : 'عميل زائر'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('order_status')
                    ->label($isEn ? 'Order Status' : 'حالة الطلب')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
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
                    ->money('EGP')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Date' : 'التاريخ والتوقيت')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ]);
    }
}
