<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Content & Static Pages' : 'المحتوى والصفحات التعريفية';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'FAQs' : 'الأسئلة الشائعة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'FAQs' : 'الأسئلة الشائعة';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'FAQ' : 'سؤال شائع';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل السؤال والجواب')
                    ->schema([
                        Forms\Components\TextInput::make('question_ar')
                            ->label('السؤال (عربي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('question_en')
                            ->label('السؤال (إنجليزي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('answer_ar')
                            ->label('الإجابة (عربي)')
                            ->required()
                            ->rows(3),

                        Forms\Components\Textarea::make('answer_en')
                            ->label('الإجابة (إنجليزي)')
                            ->required()
                            ->rows(3),

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
                    ->label($isEn ? 'Question' : 'السؤال')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->question_en ?: $record->question_ar) : ($record->question_ar ?: $record->question_en))
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('answer')
                    ->label($isEn ? 'Answer' : 'الإجابة')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->answer_en ?: $record->answer_ar) : ($record->answer_ar ?: $record->answer_en))
                    ->limit(50),

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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
