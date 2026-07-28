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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-face-smile';

    protected static string|UnitEnum|null $navigationGroup = 'محرك التقييم و Quiz البشرة';

    protected static ?string $navigationLabel = 'أنواع البشرة (Skin Types)';

    protected static ?string $pluralModelLabel = 'أنواع البشرة';

    protected static ?string $modelLabel = 'نوع بشرة';

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
                            ->directory('skin_types'),

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
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة'),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم (عربي)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label('الاسم (إنجليزي)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'active' ? 'نشط' : 'غير نشط'),
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
