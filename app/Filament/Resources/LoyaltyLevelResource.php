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

    protected static string|UnitEnum|null $navigationGroup = 'برنامج الولاء والإعدادات';

    protected static ?string $navigationLabel = 'مستويات برنامج الولاء';

    protected static ?string $pluralModelLabel = 'مستويات برنامج الولاء';

    protected static ?string $modelLabel = 'مستوى ولاء';

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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('المستوى (عربي)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label('المستوى (إنجليزي)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_points')
                    ->label('أقل نقاط')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_points')
                    ->label('أقصى نقاط')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
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
