<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkinTypeResource\Pages;
use App\Models\SkinType;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SkinTypeResource extends Resource
{
    protected static ?string $model = SkinType::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-skin-types';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Skin Types' : 'أنواع البشرة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Skin Types' : 'أنواع البشرة';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Skin Type' : 'نوع بشرة';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل نوع البشرة')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required(),

                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->required(),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف بالعربية'),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف بالإنجليزية'),

                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة')
                            ->image()
                            ->directory('skin_types')
                            ->formatStateUsing(fn ($state, $record) => $record?->getRawOriginal('image') ? (str_starts_with($record->getRawOriginal('image'), 'skin_types/') ? $record->getRawOriginal('image') : (filter_var($record->getRawOriginal('image'), FILTER_VALIDATE_URL) ? $record->getRawOriginal('image') : 'skin_types/' . ltrim($record->getRawOriginal('image'), '/'))) : null)
                            ->dehydrateStateUsing(fn ($state) => $state ? (filter_var($state, FILTER_VALIDATE_URL) ? $state : basename($state)) : null),

                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label($isEn ? 'Image' : 'الصورة'),

                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Skin Type Name' : 'اسم نوع البشرة')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label($isEn ? 'Status' : 'الحالة')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'active' ? ($isEn ? 'Active' : 'نشط') : ($isEn ? 'Inactive' : 'غير نشط')),
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
            'index' => Pages\ListSkinTypes::route('/'),
            'create' => Pages\CreateSkinType::route('/create'),
            'edit' => Pages\EditSkinType::route('/{record}/edit'),
        ];
    }
}
