<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoutineGoalResource\Pages;
use App\Models\RoutineGoal;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoutineGoalResource extends Resource
{
    protected static ?string $model = RoutineGoal::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Routine Engine & Quiz' : 'إدارة الروتينات والـ Quiz';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Routine Goals' : 'أهداف الروتين';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Routine Goals' : 'أهداف الروتين';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Routine Goal' : 'هدف روتين';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required(),

                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->required(),

                        Forms\Components\FileUpload::make('image')
                            ->label('صورة الهدف')
                            ->image()
                            ->directory('routine-goals')
                            ->visibility('public')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\ImageColumn::make('image_path')->label('الصورة'),
                Tables\Columns\TextColumn::make('name_ar')->label('الاسم (عربي)')->searchable(),
                Tables\Columns\TextColumn::make('name_en')->label('الاسم (إنجليزي)')->searchable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('عدد المنتجات المرتبطة')
                    ->counts('products'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutineGoals::route('/'),
            'create' => Pages\CreateRoutineGoal::route('/create'),
            'edit' => Pages\EditRoutineGoal::route('/{record}/edit'),
        ];
    }
}
