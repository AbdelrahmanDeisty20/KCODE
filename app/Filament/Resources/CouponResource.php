<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Sales & Orders' : 'إدارة المبيعات والطلبات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Discount Coupons' : 'كوبونات الخصم';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Discount Coupons' : 'كوبونات الخصم';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Discount Coupon' : 'كوبون خصم';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل الكوبون')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('كود الكوبون')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('title_ar')
                            ->label('العنوان بالعربية'),

                        Forms\Components\TextInput::make('title_en')
                            ->label('العنوان بالإنجليزية'),

                        Forms\Components\Select::make('discount_type')
                            ->label('نوع الخصم')
                            ->options([
                                'fixed' => 'مبلغ ثابت',
                                'percentage' => 'نسبة مئوية (%)',
                            ])
                            ->default('percentage')
                            ->required(),

                        Forms\Components\TextInput::make('discount_value')
                            ->label('قيمة الخصم')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('min_order_amount')
                            ->label('الحد الأدنى لقيمة الطلب')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\TextInput::make('max_discount_amount')
                            ->label('الحد الأقصى للخصم')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('تاريخ البداية'),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('تاريخ النهاية'),

                        Forms\Components\TextInput::make('usage_limit')
                            ->label('الحد الأقصى للاستخدام العام')
                            ->numeric(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('تفعيل الكوبون')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label($isEn ? 'Code' : 'الكود')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_type')
                    ->label($isEn ? 'Type' : 'النوع')
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? ($isEn ? 'Percentage %' : 'نسبة %') : ($isEn ? 'Fixed Amount' : 'مبلغ ثابت')),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label($isEn ? 'Value' : 'القيمة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_count')
                    ->label($isEn ? 'Times Used' : 'عدد مرات الاستخدام')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label($isEn ? 'Active' : 'تفعيل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label($isEn ? 'End Date' : 'تاريخ الانتهاء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->actions([
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
