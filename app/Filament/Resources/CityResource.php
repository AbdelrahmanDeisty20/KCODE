<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-map-pin';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Shipping & Locations' : 'إدارة الشحن والمواقع';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Cities & Shipping Fees' : 'المدن ورسوم الشحن';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Cities & Shipping Fees' : 'المدن ورسوم الشحن';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'City' : 'مدينة';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->label('الدولة')
                            ->relationship('country', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('state_id')
                            ->label('المحافظة / المنطقة')
                            ->relationship('state', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم المدينة (بالعربية)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم المدينة (بالإنجليزية)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('shipping_fee')
                            ->label('رسوم الشحن (ر.ع.)')
                            ->numeric()
                            ->step(0.100)
                            ->default(2.000)
                            ->suffix('ر.ع.')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعّلة للشحن والتوصيل')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name_ar')->label('المدينة (عربي)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name_en')->label('المدينة (إنجليزي)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('state.name_ar')->label('المحافظة')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('country.name_ar')->label('الدولة')->sortable(),
                Tables\Columns\TextColumn::make('shipping_fee')
                    ->label('رسوم الشحن')
                    ->money('OMR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّلة')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state_id')
                    ->label('المحافظة')
                    ->relationship('state', 'name_ar'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('حالة التفعيل'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
