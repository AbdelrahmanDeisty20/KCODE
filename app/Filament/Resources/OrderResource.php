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

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = static::getModel()::whereIn('status', ['pending', 'processing'])->count();
        return $pendingCount > 0 ? (string) $pendingCount : (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::whereIn('status', ['pending', 'processing'])->count();
        return $pendingCount > 0 ? 'warning' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section 1: Customer & Order Overview (Full Width)
                Components\Section::make('👤 بيانات الطلب والعميل')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('رقم الطلب')
                            ->disabled(),

                        Forms\Components\Select::make('order_status')
                            ->label('حالة الطلب')
                            ->options([
                                'pending'   => 'قيد الانتظار',
                                'accepted'  => 'مقبول',
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

                        Forms\Components\TextInput::make('user_name')
                            ->label('اسم صاحب الطلب')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_phone')
                            ->label('رقم الهاتف للتواصل'),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('طريقة الدفع'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // Section 2: Order Items (Full Width Repeater)
                Components\Section::make('🛍️ المنتجات المطلوبة والكميات (Order Items)')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('عناصر الطلب')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('اسم المنتج')
                                    ->disabled()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->disabled(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('سعر القطعة')
                                    ->prefix('EGP')
                                    ->disabled(),

                                Forms\Components\TextInput::make('total_price')
                                    ->label('الإجمالي')
                                    ->prefix('EGP')
                                    ->disabled(),
                            ])
                            ->columns(5)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Section 3: Delivery Address Details (Full Width with Country, State, City)
                Components\Section::make('🚚 بيانات التوصيل والعنوان التفصيلي')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('اسم المستلم')
                            ->default(fn ($record) => $record?->shipping_address['user_name'] ?? $record?->address?->user?->name ?? $record?->user_name ?? '—')
                            ->formatStateUsing(fn ($state, $record) => $record?->shipping_address['user_name'] ?? $record?->address?->user?->name ?? $record?->user_name ?? '—')
                            ->disabled(),

                        Forms\Components\TextInput::make('recipient_phone')
                            ->label('هاتف المستلم')
                            ->default(fn ($record) => $record?->shipping_address['user_phone'] ?? $record?->address?->phone ?? $record?->user_phone ?? '—')
                            ->formatStateUsing(fn ($state, $record) => $record?->shipping_address['user_phone'] ?? $record?->address?->phone ?? $record?->user_phone ?? '—')
                            ->disabled(),

                        Forms\Components\TextInput::make('recipient_country')
                            ->label('الدولة')
                            ->default(function ($record) {
                                return $record?->shipping_address['country'] 
                                    ?? $record?->address?->country?->name_ar 
                                    ?? $record?->address?->country?->name_en 
                                    ?? 'مصر (Egypt)';
                            })
                            ->formatStateUsing(function ($state, $record) {
                                return $record?->shipping_address['country'] 
                                    ?? $record?->address?->country?->name_ar 
                                    ?? $record?->address?->country?->name_en 
                                    ?? 'مصر (Egypt)';
                            })
                            ->disabled(),

                        Forms\Components\TextInput::make('recipient_state')
                            ->label('المحافظة')
                            ->default(function ($record) {
                                return $record?->shipping_address['state'] 
                                    ?? $record?->address?->state?->name_ar 
                                    ?? $record?->address?->state?->name_en 
                                    ?? '—';
                            })
                            ->formatStateUsing(function ($state, $record) {
                                return $record?->shipping_address['state'] 
                                    ?? $record?->address?->state?->name_ar 
                                    ?? $record?->address?->state?->name_en 
                                    ?? '—';
                            })
                            ->disabled(),

                        Forms\Components\TextInput::make('recipient_city')
                            ->label('المدينة')
                            ->default(function ($record) {
                                return $record?->shipping_address['city'] 
                                    ?? $record?->address?->city?->name_ar 
                                    ?? $record?->address?->city?->name_en 
                                    ?? '—';
                            })
                            ->formatStateUsing(function ($state, $record) {
                                return $record?->shipping_address['city'] 
                                    ?? $record?->address?->city?->name_ar 
                                    ?? $record?->address?->city?->name_en 
                                    ?? '—';
                            })
                            ->disabled(),

                        Forms\Components\TextInput::make('recipient_address')
                            ->label('العنوان التفصيلي')
                            ->default(function ($record) {
                                return $record?->shipping_address['address'] ?? $record?->address?->address ?? '—';
                            })
                            ->formatStateUsing(function ($state, $record) {
                                return $record?->shipping_address['address'] ?? $record?->address?->address ?? '—';
                            })
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // Section 4: Financial Summary (Full Width)
                Components\Section::make('💳 الملخص المالي والإجماليات')
                    ->schema([
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
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

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
                        'info'    => 'accepted',
                        'success' => 'delivered',
                        'danger'  => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'   => 'قيد الانتظار',
                        'accepted'  => 'مقبول',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغي',
                        default     => $state,
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
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending'   => 'قيد الانتظار',
                        'accepted'  => 'مقبول',
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
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
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
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
