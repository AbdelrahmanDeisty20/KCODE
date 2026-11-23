<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizEngineConfigResource\Pages;
use App\Models\QuizEngineConfig;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class QuizEngineConfigResource extends Resource
{
    protected static ?string $model = QuizEngineConfig::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-adjustments-horizontal';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Quiz Engine Configs' : 'إعدادات خوارزمية الكويز';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Quiz Engine Configs' : 'إعدادات خوارزمية الكويز';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Quiz Config' : 'إعداد خوارزمية';
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return !in_array($record->key, ['quiz_contract', 'approved_exfoliator_exclusions', 'scenario_role_overrides'], true);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('إعدادات محرك التحليل والترشيح (Quiz Engine Config)')
                    ->description('تُستخدم هذه الإعدادات لتعريف عقد الإجابات والاستثناءات المعتمدة، ويتحدّث الكاش تلقائيًا فور الحفظ.')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('المفتاح البرمجي (Key)')
                            ->disabled(fn (string $context): bool => $context === 'edit')
                            ->unique(ignoreRecord: true)
                            ->required(),

                        Forms\Components\Textarea::make('payload')
                            ->label('بيانات الإعداد (JSON)')
                            ->rows(16)
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                : (string) ($state ?? '{}'))
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? (json_decode($state, true) ?? []) : $state)
                            ->rule('json')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('المفتاح (Key)')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizEngineConfigs::route('/'),
            'create' => Pages\CreateQuizEngineConfig::route('/create'),
            'edit' => Pages\EditQuizEngineConfig::route('/{record}/edit'),
        ];
    }
}
