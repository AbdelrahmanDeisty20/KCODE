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

    protected static string|UnitEnum|null $navigationGroup = 'التسويق والعروض';

    protected static ?string $navigationLabel = 'كوبونات الخصم';

    protected static ?string $pluralModelLabel = 'كوبونات الخصم';

    protected static ?string $modelLabel = 'كوبون خصم';

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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_type')
                    ->label('النوع')
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'نسبة %' : 'مبلغ ثابت'),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label('القيمة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('عدد مرات الاستخدام')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
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
