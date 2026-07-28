<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'برنامج الولاء والإعدادات';

    protected static ?string $navigationLabel = 'إعدادات النظام العامة';

    protected static ?string $pluralModelLabel = 'إعدادات النظام';

    protected static ?string $modelLabel = 'إعداد';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('بيانات الإعداد')
                    ->schema([
                        Forms\Components\TextInput::make('key_en')
                            ->label('مفتاح الإعداد (Key EN)')
                            ->required(),

                        Forms\Components\TextInput::make('key_ar')
                            ->label('مفتاح الإعداد (Key AR)')
                            ->required(),

                        Forms\Components\Textarea::make('value_ar')
                            ->label('القيمة (عربي)'),

                        Forms\Components\Textarea::make('value_en')
                            ->label('القيمة (إنجليزي)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key_en')
                    ->label('المفتاح (EN)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('key_ar')
                    ->label('المفتاح (AR)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value_ar')
                    ->label('القيمة (عربي)')
                    ->limit(50),

                Tables\Columns\TextColumn::make('value_en')
                    ->label('القيمة (إنجليزي)')
                    ->limit(50),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
