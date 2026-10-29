<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltySettingResource\Pages;
use App\Models\LoyaltySetting;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoyaltySettingResource extends Resource
{
    protected static ?string $model = LoyaltySetting::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-cog-6-tooth';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Program' : 'إدارة برنامج الولاء';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Settings' : 'إعدادات الولاء العامة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Settings' : 'إعدادات برنامج الولاء';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Setting' : 'إعداد برنامج الولاء';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('المفتاح البرمجي (Key)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('key_ar')
                            ->label('اسم الإعداد (بالعربية)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('key_en')
                            ->label('اسم الإعداد (بالإنجليزية)')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('value_ar')
                            ->label('القيمة (بالعربية)')
                            ->rows(3),

                        Forms\Components\Textarea::make('value_en')
                            ->label('القيمة (بالإنجليزية)')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('key')->label('المفتاح')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('key_ar')->label('الاسم (عربي)')->searchable(),
                Tables\Columns\TextColumn::make('value_ar')->label('القيمة (عربي)')->limit(50),
                Tables\Columns\TextColumn::make('updated_at')->label('آخر تعديل')->dateTime()->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoyaltySettings::route('/'),
            'create' => Pages\CreateLoyaltySetting::route('/create'),
            'edit' => Pages\EditLoyaltySetting::route('/{record}/edit'),
        ];
    }
}
