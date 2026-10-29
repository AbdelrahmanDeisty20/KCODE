<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-globe-alt';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Shipping & Locations' : 'إدارة الشحن والمواقع';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Countries' : 'الدول';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Countries' : 'الدول';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Country' : 'دولة';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم الدولة (بالعربية)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم الدولة (بالإنجليزية)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعّلة للشحن والطلب')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name_ar')->label('الدولة (عربي)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name_en')->label('الدولة (إنجليزي)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('states_count')
                    ->label('عدد المناطق/المحافظات')
                    ->counts('states'),
                Tables\Columns\TextColumn::make('cities_count')
                    ->label('عدد المدن')
                    ->counts('cities'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّلة')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('حالة التفعيل'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
