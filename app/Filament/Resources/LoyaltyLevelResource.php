<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyLevelResource\Pages;
use App\Models\LoyaltyLevel;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class LoyaltyLevelResource extends Resource
{
    protected static ?string $model = LoyaltyLevel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Program & Settings' : 'برنامج الولاء والإعدادات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Levels' : 'مستويات برنامج الولاء';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Levels' : 'مستويات برنامج الولاء';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Loyalty Level' : 'مستوى ولاء';
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
                Components\Section::make('تفاصيل المستوى')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم المستوى بالعربية')
                            ->required(),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم المستوى بالإنجليزية')
                            ->required(),

                        Forms\Components\TextInput::make('min_points')
                            ->label('الحد الأدنى للنقاط')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('max_points')
                            ->label('الحد الأقصى للنقاط')
                            ->numeric(),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف (عربي)'),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف (إنجليزي)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('تفعيل المستوى')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Loyalty Level' : 'مستوى الولاء')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_points')
                    ->label($isEn ? 'Min Points' : 'أقل نقاط')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_points')
                    ->label($isEn ? 'Max Points' : 'أقصى نقاط')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label($isEn ? 'Active' : 'نشط')
                    ->boolean(),
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
            'index' => Pages\ListLoyaltyLevels::route('/'),
            'create' => Pages\CreateLoyaltyLevel::route('/create'),
            'edit' => Pages\EditLoyaltyLevel::route('/{record}/edit'),
        ];
    }
}
