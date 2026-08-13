<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-file-lines';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Content & Static Pages' : 'المحتوى والصفحات التعريفية';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Static Pages' : 'الصفحات الثابتة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Static Pages' : 'الصفحات الثابتة';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Static Page' : 'صفحة ثابتة';
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
                Components\Section::make('بيانات الصفحة الثابتة')
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->label('نوع الصفحة (Type)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('key_en')
                            ->label('المفتاح (Key EN)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('key_ar')
                            ->label('المفتاح (Key AR)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('value_ar')
                            ->label('المحتوى (عربي)'),

                        Forms\Components\Textarea::make('value_en')
                            ->label('المحتوى (إنجليزي)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label($isEn ? 'Page Type' : 'نوع الصفحة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->label($isEn ? 'Key' : 'المفتاح')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->key_en ?: $record->key_ar) : ($record->key_ar ?: $record->key_en))
                    ->searchable(),

                Tables\Columns\TextColumn::make('value')
                    ->label($isEn ? 'Content' : 'المحتوى')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->value_en ?: $record->value_ar) : ($record->value_ar ?: $record->value_en))
                    ->limit(60),
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
            'index' => Pages\ListPagesRecord::route('/'),
            'create' => Pages\CreatePageRecord::route('/create'),
            'edit' => Pages\EditPageRecord::route('/{record}/edit'),
        ];
    }
}
