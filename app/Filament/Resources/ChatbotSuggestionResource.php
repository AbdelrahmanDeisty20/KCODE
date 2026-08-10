<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatbotSuggestionResource\Pages;
use App\Models\ChatbotSuggestion;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatbotSuggestionResource extends Resource
{
    protected static ?string $model = ChatbotSuggestion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'AI Chatbot' : 'المستشار الذكي';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Chatbot Suggestions' : 'الأسئلة المقترحة للشات بوت';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Chatbot Suggestions' : 'الأسئلة المقترحة للشات بوت';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Suggestion' : 'سؤال مقترح';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل السؤال المقترح')
                    ->schema([
                        Forms\Components\TextInput::make('question_ar')
                            ->label('السؤال المقترح (عربي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('question_en')
                            ->label('السؤال المقترح (إنجليزي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مُفعل')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label($isEn ? 'Question' : 'السؤال المقترح')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->question_en ?: $record->question_ar) : ($record->question_ar ?: $record->question_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label($isEn ? 'Active' : 'مُفعل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label($isEn ? 'Order' : 'الترتيب')
                    ->sortable(),
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
            'index' => Pages\ListChatbotSuggestions::route('/'),
            'create' => Pages\CreateChatbotSuggestion::route('/create'),
            'edit' => Pages\EditChatbotSuggestion::route('/{record}/edit'),
        ];
    }
}
