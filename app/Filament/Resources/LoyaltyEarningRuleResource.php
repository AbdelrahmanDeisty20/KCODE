<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyEarningRuleResource\Pages;
use App\Models\LoyaltyEarningRule;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoyaltyEarningRuleResource extends Resource
{
    protected static ?string $model = LoyaltyEarningRule::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-sparkles';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Program & Settings' : 'برنامج الولاء والإعدادات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Earning Rules' : 'قواعد كسب النقاط';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Earning Rules' : 'قواعد كسب النقاط';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Earning Rule' : 'قاعدة كسب نقاط';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم القاعدة (الحدث / المناسبة)')
                            ->placeholder('مثال: مضاعفة النقاط في نهاية الأسبوع، أو نقاط التسجيل')
                            ->required(),

                        Forms\Components\Select::make('target_type')
                            ->label('نطاق تطبيق القاعدة')
                            ->options([
                                'global' => 'عام (جميع المشتريات)',
                                'category' => 'قسم محدد',
                                'brand' => 'براند محدد',
                                'product' => 'منتج محدد',
                            ])
                            ->default('global')
                            ->required(),

                        Forms\Components\TextInput::make('multiplier')
                            ->label('مضاعف النقاط (Multiplier)')
                            ->numeric()
                            ->step(0.1)
                            ->default(1.00)
                            ->helperText('مثال: 2 يعني ضعف النقاط، 1.5 يعني نقطة ونصف'),

                        Forms\Components\TextInput::make('fixed_points')
                            ->label('نقاط ثابتة إضافية (Fixed Points)')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('تاريخ ووقت البداية'),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('تاريخ ووقت النهاية'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشطة ومفعّلة')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('اسم القاعدة')->searchable(),
                Tables\Columns\TextColumn::make('target_type')->label('النطاق')->badge(),
                Tables\Columns\TextColumn::make('multiplier')->label('المضاعف')->suffix('x'),
                Tables\Columns\TextColumn::make('fixed_points')->label('نقاط ثابتة')->badge()->color('success'),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّلة')->boolean(),
                Tables\Columns\TextColumn::make('starts_at')->label('البداية')->dateTime(),
                Tables\Columns\TextColumn::make('ends_at')->label('النهاية')->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoyaltyEarningRules::route('/'),
            'create' => Pages\CreateLoyaltyEarningRule::route('/create'),
            'edit' => Pages\EditLoyaltyEarningRule::route('/{record}/edit'),
        ];
    }
}
