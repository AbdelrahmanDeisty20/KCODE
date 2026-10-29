<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Models\State;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-map';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Shipping & Locations' : 'إدارة الشحن والمواقع';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'States / Regions' : 'المحافظات والمناطق';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'States / Regions' : 'المحافظات والمناطق';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'State / Region' : 'محافظة / منطقة';
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

                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم المحافظة / المنطقة (بالعربية)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم المحافظة / المنطقة (بالإنجليزية)')
                            ->required()
                            ->maxLength(255),

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
                Tables\Columns\TextColumn::make('name_ar')->label('المحافظة (عربي)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name_en')->label('المحافظة (إنجليزي)')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('country.name_ar')->label('الدولة')->sortable(),
                Tables\Columns\TextColumn::make('cities_count')
                    ->label('عدد المدن')
                    ->counts('cities'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعّلة')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')
                    ->label('الدولة')
                    ->relationship('country', 'name_ar'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('حالة التفعيل'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
