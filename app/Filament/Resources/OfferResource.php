<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Models\Offer;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Products & Catalog' : 'الكتالوج والمنتجات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Offers & Discounts' : 'عروض المنتجات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Offers & Discounts' : 'عروض المنتجات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Offer' : 'عرض';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل العرض')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('المنتج')
                            ->relationship('product', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('نسبة الخصم (%)')
                            ->numeric()
                            ->required(),

                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('تاريخ بداية العرض'),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('تاريخ نهاية العرض'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('تفعيل العرض')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product')
                    ->label($isEn ? 'Product' : 'المنتج')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->product?->name_en ?: $record->product?->name_ar) : ($record->product?->name_ar ?: $record->product?->name_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label($isEn ? 'Discount %' : 'نسبة الخصم %')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label($isEn ? 'Active' : 'نشط')
                    ->boolean(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label($isEn ? 'Start Date' : 'من')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label($isEn ? 'End Date' : 'إلى')
                    ->dateTime('d/m/Y H:i')
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
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
