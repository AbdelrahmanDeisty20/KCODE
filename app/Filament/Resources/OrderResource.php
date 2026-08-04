<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Sales & Orders' : 'إدارة المبيعات والطلبات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Orders' : 'الطلبات والمبيعات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Orders' : 'الطلبات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Order' : 'طلب';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل الطلب')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('رقم الطلب')
                            ->disabled(),

                        Forms\Components\Select::make('user_id')
                            ->label('العميل')
                            ->relationship('user', 'name')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_phone')
                            ->label('رقم الهاتف'),

                        Forms\Components\Select::make('order_status')
                            ->label('حالة الطلب')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'processing' => 'جاري التحضير',
                                'shipped' => 'تم الشحن',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغي',
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'pending' => 'معلق',
                                'paid' => 'مدفوع',
                                'failed' => 'فشل الدفع',
                                'refunded' => 'مسترجع',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('طريقة الدفع'),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('المجموع الفرعي')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('خصم الكوبون')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\TextInput::make('shipping_fee')
                            ->label('مبلغ الشحن')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\TextInput::make('total')
                            ->label('الإجمالي النهائي')
                            ->numeric()
                            ->prefix('EGP')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات الطلب')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('العميل')
                    ->searchable()
                    ->default(fn ($record) => $record->user_name ?? 'زائر'),

                Tables\Columns\TextColumn::make('user_phone')
                    ->label('الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order_status')
                    ->label('حالة الطلب')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'processing' => 'جاري التحضير',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'معلق',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل الدفع',
                        'refunded' => 'مسترجع',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'processing' => 'جاري التحضير',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'pending' => 'معلق',
                        'paid' => 'مدفوع',
                        'failed' => 'فشل الدفع',
                        'refunded' => 'مسترجع',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
