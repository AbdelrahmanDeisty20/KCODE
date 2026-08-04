<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConcernResource\Pages;
use App\Models\Concern;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ConcernResource extends Resource
{
    protected static ?string $model = Concern::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Skin Concerns' : 'مشاكل البشرة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Skin Concerns' : 'مشاكل البشرة';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Skin Concern' : 'مشكلة بشرة';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل مشكلة البشرة')
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
                            ->directory('concerns'),

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

                Tables\Columns\TextColumn::make('name_ar')
                    ->label($isEn ? 'Name (Arabic)' : 'الاسم (عربي)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label($isEn ? 'Name (English)' : 'الاسم (إنجليزي)')
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
            'index' => Pages\ListConcerns::route('/'),
            'create' => Pages\CreateConcern::route('/create'),
            'edit' => Pages\EditConcern::route('/{record}/edit'),
        ];
    }
}
