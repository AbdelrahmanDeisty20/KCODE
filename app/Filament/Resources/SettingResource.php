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

    protected static string|BackedEnum|null $navigationIcon = 'fas-sliders';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.settings_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.navigation.settings');
    }

    public static function getModelLabel(): string
    {
        return __('admin.navigation.settings');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('بيانات الإعداد')
                    ->schema([
                        Forms\Components\TextInput::make('key_en')
                            ->label('مفتاح الإعداد (Key EN)')
                            ->disabled()
                            ->required(),

                        Forms\Components\TextInput::make('key_ar')
                            ->label('مفتاح الإعداد (Key AR)')
                            ->disabled()
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
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label($isEn ? 'Setting Key' : 'مفتاح الإعداد')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->key_en ?: $record->key_ar) : ($record->key_ar ?: $record->key_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label($isEn ? 'Value' : 'القيمة')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->value_en ?: $record->value_ar) : ($record->value_ar ?: $record->value_en))
                    ->limit(60)
                    ->searchable(),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit'  => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
