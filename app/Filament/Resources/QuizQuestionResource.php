<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizQuestionResource\Pages;
use App\Models\QuizQuestion;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class QuizQuestionResource extends Resource
{
    protected static ?string $model = QuizQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-quiz-questions';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Quiz Questions' : 'أسئلة الاختبار (Quiz)';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Quiz Questions' : 'أسئلة الاختبار (Quiz)';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Quiz Question' : 'سؤال';
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
                Components\Section::make('بيانات السؤال')
                    ->schema([
                        Forms\Components\TextInput::make('step_number')
                            ->label('رقم الخطوة / الترتيب')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Forms\Components\Select::make('selection_type')
                            ->label('نوع الاختيار')
                            ->options([
                                'single' => 'اختيار واحد',
                                'multiple' => 'اختيارات متعددة',
                            ])
                            ->default('single')
                            ->required(),

                        Forms\Components\TextInput::make('title_ar')
                            ->label('السؤال بالعربية')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('السؤال بالإنجليزية')
                            ->required(),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف التوضيحي (عربي)'),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف التوضيحي (إنجليزي)'),

                        Forms\Components\Toggle::make('is_optional')
                            ->label('سؤال اختياري')
                            ->default(false),
                    ])->columns(2),

                Components\Section::make('خيارات الإجابة')
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship('options')
                            ->schema([
                                Forms\Components\TextInput::make('option_text_ar')
                                    ->label('نص الخيار (عربي)')
                                    ->required(),

                                Forms\Components\TextInput::make('option_text_en')
                                    ->label('نص الخيار (إنجليزي)')
                                    ->required(),

                                Forms\Components\FileUpload::make('image')
                                    ->label('صورة الخيار')
                                    ->image()
                                    ->directory('quiz_options'),

                                Forms\Components\TextInput::make('order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(1),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('step_number')
                    ->label($isEn ? 'Step / Order' : 'الترتيب')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label($isEn ? 'Question (Arabic)' : 'السؤال (عربي)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title_en')
                    ->label($isEn ? 'Question (English)' : 'السؤال (إنجليزي)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('selection_type')
                    ->label($isEn ? 'Selection Type' : 'نوع الاختيار')
                    ->formatStateUsing(fn ($state) => $state === 'single' ? ($isEn ? 'Single' : 'مفرد') : ($isEn ? 'Multiple' : 'متعدد')),

                Tables\Columns\IconColumn::make('is_optional')
                    ->label($isEn ? 'Optional' : 'اختياري')
                    ->boolean(),

                Tables\Columns\TextColumn::make('options_count')
                    ->label($isEn ? 'Options Count' : 'عدد الخيارات')
                    ->counts('options'),
            ])
            ->defaultSort('step_number', 'asc')
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
            'index' => Pages\ListQuizQuestions::route('/'),
            'create' => Pages\CreateQuizQuestion::route('/create'),
            'edit' => Pages\EditQuizQuestion::route('/{record}/edit'),
        ];
    }
}
