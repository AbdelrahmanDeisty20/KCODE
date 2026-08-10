<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatbotMessageResource\Pages;
use App\Models\ChatbotMessage;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatbotMessageResource extends Resource
{
    protected static ?string $model = ChatbotMessage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'AI Chatbot' : 'المستشار الذكي';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Chat Histories & Logs' : 'سجل محادثات العملاء';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Chat Histories' : 'سجل محادثات العملاء';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Chat Log' : 'محادثة';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل المحادثة')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('اسم المستخدم')
                            ->getStateUsing(fn ($record) => $record?->user?->name ?? 'زائر (Guest)')
                            ->disabled(),

                        Forms\Components\TextInput::make('created_at')
                            ->label('تاريخ وتوقيت المحادثة')
                            ->disabled(),

                        Forms\Components\Textarea::make('prompt')
                            ->label('سؤال المستخدم')
                            ->rows(3)
                            ->disabled(),

                        Forms\Components\Textarea::make('reply')
                            ->label('رد المستشار الذكي (AI Reply)')
                            ->rows(6)
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'User' : 'المستخدم')
                    ->default($isEn ? 'Guest' : 'زائر')
                    ->searchable(),

                Tables\Columns\TextColumn::make('prompt')
                    ->label($isEn ? 'Question' : 'سؤال المستخدم')
                    ->searchable()
                    ->limit(60),

                Tables\Columns\TextColumn::make('reply')
                    ->label($isEn ? 'AI Response' : 'رد المستشار الذكي')
                    ->limit(60),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Date' : 'التاريخ والوقت')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\ViewAction::make(),
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
            'index' => Pages\ListChatbotMessages::route('/'),
            'view' => Pages\ViewChatbotMessage::route('/{record}'),
        ];
    }
}
