<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnPolicyResource\Pages;
use App\Models\ReturnPolicy;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReturnPolicyResource extends Resource
{
    protected static ?string $model = ReturnPolicy::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-arrow-path-rounded-square';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Settings & Policies' : 'إدارة الإعدادات والسياسات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Return Policy' : 'سياسة الاستبدال والاسترجاع';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Return Policy' : 'سياسة الاستبدال والاسترجاع';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Return Policy' : 'سياسة الاستبدال والاسترجاع';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان السياسة (بالعربية)')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('عنوان السياسة (بالإنجليزية)')
                            ->required(),

                        Forms\Components\RichEditor::make('content_ar')
                            ->label('محتوى السياسة (بالعربية)')
                            ->required(),

                        Forms\Components\RichEditor::make('content_en')
                            ->label('محتوى السياسة (بالإنجليزية)')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشطة / مفعّلة')
                            ->default(true),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title_ar')->label('العنوان (عربي)')->searchable(),
                Tables\Columns\TextColumn::make('title_en')->label('العنوان (إنجليزي)')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّلة')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('آخر تحديث')->dateTime()->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnPolicys::route('/'),
            'create' => Pages\CreateReturnPolicy::route('/create'),
            'edit' => Pages\EditReturnPolicy::route('/{record}/edit'),
        ];
    }
}
