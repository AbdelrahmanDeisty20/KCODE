<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyRewardResource\Pages;
use App\Models\LoyaltyReward;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoyaltyRewardResource extends Resource
{
    protected static ?string $model = LoyaltyReward::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-gift';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Program & Settings' : 'برنامج الولاء والإعدادات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Rewards' : 'مكافآت برنامج الولاء';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Rewards' : 'مكافآت برنامج الولاء';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Reward' : 'مكافأة ولاء';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم المكافأة (بالعربية)')
                            ->required(),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم المكافأة (بالإنجليزية)')
                            ->required(),

                        Forms\Components\TextInput::make('points_required')
                            ->label('النقاط المطلوبة للاستبدال')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('نوع المكافأة')
                            ->options([
                                'coupon' => 'كوبون خصم (Coupon)',
                                'cashback' => 'استرجاع نقدي / رصيد (Cashback)',
                                'free_product' => 'منتج مجاني (Free Product)',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('value')
                            ->label('قيمة المكافأة (خصم، كود، أو معرف منتج)')
                            ->required(),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف (بالعربية)')
                            ->rows(3),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف (بالإنجليزية)')
                            ->rows(3),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعّلة ومتاحة للاستبدال')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name_ar')->label('المكافأة (عربي)')->searchable(),
                Tables\Columns\TextColumn::make('name_en')->label('المكافأة (إنجليزي)')->searchable(),
                Tables\Columns\TextColumn::make('points_required')->label('النقاط المطلوبة')->sortable()->badge()->color('warning'),
                Tables\Columns\TextColumn::make('type')->label('النوع')->badge(),
                Tables\Columns\TextColumn::make('value')->label('القيمة'),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّلة')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoyaltyRewards::route('/'),
            'create' => Pages\CreateLoyaltyReward::route('/create'),
            'edit' => Pages\EditLoyaltyReward::route('/{record}/edit'),
        ];
    }
}
