<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CareGuideResource\Pages;
use App\Models\CareGuide;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CareGuideResource extends Resource
{
    protected static ?string $model = CareGuide::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-book-open';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Content & Static Pages' : 'المحتوى والصفحات التعريفية';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Care Guide' : 'دليل العناية';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Care Guides' : 'أدلة العناية';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Care Guide' : 'دليل العناية';
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
                Components\Section::make('بيانات دليل العناية الرئيسي')
                    ->description('العناوين والنصوص التعريفية الخاصة بنافذة دليل العناية المنبثقة.')
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label('العنوان الرئيسي (عربي)')
                            ->required()
                            ->maxLength(255)
                            ->default('دليل العناية'),

                        Forms\Components\TextInput::make('title_en')
                            ->label('العنوان الرئيسي (إنجليزي)')
                            ->maxLength(255)
                            ->default('Care Guide'),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('المقدمة / الوصف الرئيسي (عربي)')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description_en')
                            ->label('المقدمة / الوصف الرئيسي (إنجليزي)')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('note_ar')
                            ->label('الملاحظة الختامية / التنبيه (عربي)')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('note_en')
                            ->label('الملاحظة الختامية / التنبيه (إنجليزي)')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مُفعل')
                            ->default(true),
                    ])->columns(2),

                Components\Section::make('أزرار الإجراءات والتحويل')
                    ->description('تخصيص نصوص وأكشن أزرار الدليل (تحليل البشرة والروتينات).')
                    ->schema([
                        Forms\Components\TextInput::make('button_quiz_text_ar')
                            ->label('نص زر الاختبار (عربي)')
                            ->default('ابدأ تحليل بشرتك'),

                        Forms\Components\TextInput::make('button_quiz_text_en')
                            ->label('نص زر الاختبار (إنجليزي)')
                            ->default('Start Skin Analysis'),

                        Forms\Components\TextInput::make('button_quiz_action')
                            ->label('وجهة / أكشن زر الاختبار')
                            ->default('quiz'),

                        Forms\Components\TextInput::make('button_routines_text_ar')
                            ->label('نص زر الروتينات (عربي)')
                            ->default('اكتشف الروتينات'),

                        Forms\Components\TextInput::make('button_routines_text_en')
                            ->label('نص زر الروتينات (إنجليزي)')
                            ->default('Discover Routines'),

                        Forms\Components\TextInput::make('button_routines_action')
                            ->label('وجهة / أكشن زر الروتينات')
                            ->default('routines'),
                    ])->columns(3)->collapsed(),

                Components\Section::make('بنود وخطوات دليل العناية')
                    ->description('البنود التوضيحية داخل نافذة دليل العناية (مثل: نفهم تركيبته، نوضّح لمن يناسب، نرشدك لاستخدامه).')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('title_ar')
                                    ->label('عنوان البند (عربي)')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('title_en')
                                    ->label('عنوان البند (إنجليزي)')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('icon')
                                    ->label('رمز الأيقونة (مثل: beaker, flask, user-check, sparkles)')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Textarea::make('description_ar')
                                    ->label('شرح البند (عربي)')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('description_en')
                                    ->label('شرح البند (إنجليزي)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('مُفعل')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->addActionLabel('إضافة بند جديد للدليل')
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label($isEn ? 'Title' : 'العنوان')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->title_en ?: $record->title_ar) : ($record->title_ar ?: $record->title_en))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('عدد البنود')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مُفعل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime()
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
            'index'  => Pages\ListCareGuides::route('/'),
            'create' => Pages\CreateCareGuide::route('/create'),
            'edit'   => Pages\EditCareGuide::route('/{record}/edit'),
        ];
    }
}
