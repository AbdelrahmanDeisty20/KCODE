<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubCategoryResource\Pages;
use App\Models\SubCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use BackedEnum;
use UnitEnum;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-sub-categories';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Products & Catalog' : 'الكتالوج والمنتجات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Sub Categories' : 'الأقسام الفرعية';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Sub Categories' : 'الأقسام الفرعية';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Sub Category' : 'قسم فرعي';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('بيانات القسم الفرعي')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('القسم الرئيسي')
                            ->relationship('category', 'name_ar')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required(),
                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->label($isEn ? 'Main Category' : 'القسم الرئيسي')
                    ->getStateUsing(fn($record) => $isEn ? ($record->category?->name_en ?: $record->category?->name_ar) : ($record->category?->name_ar ?: $record->category?->name_en))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Sub Category Name' : 'اسم القسم الفرعي')
                    ->getStateUsing(fn($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Created At' : 'تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label($isEn ? 'Main Category' : 'القسم الرئيسي')
                    ->relationship('category', $isEn ? 'name_en' : 'name_ar'),
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
            'index' => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'edit' => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
            'edit' => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
