<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Models\Assessment;
use App\Models\Product;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-assessments';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Assessments Log' : 'نتائج الاختبارات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Assessments Log' : 'نتائج الاختبارات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Assessment' : 'نتيجة اختبار';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section 1: Customer & Resulting Skin Type
                Components\Section::make('👤 بيانات المستخدم ونتيجة نوع البشرة')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('اسم العميل / المستخدم')
                            ->default(fn ($record) => $record?->user?->name ?? 'زائر غير مسجل')
                            ->formatStateUsing(fn ($state, $record) => $record?->user?->name ?? 'زائر غير مسجل')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_phone')
                            ->label('رقم الهاتف')
                            ->default(fn ($record) => $record?->user?->phone ?? '—')
                            ->formatStateUsing(fn ($state, $record) => $record?->user?->phone ?? '—')
                            ->disabled(),

                        Forms\Components\TextInput::make('skin_type_name')
                            ->label('نوع البشرة الناتج')
                            ->default(fn ($record) => $record?->skinType?->name_ar ?: ($record?->skinType?->name_en ?? 'غير محدد'))
                            ->formatStateUsing(fn ($state, $record) => $record?->skinType?->name_ar ?: ($record?->skinType?->name_en ?? 'غير محدد'))
                            ->disabled(),

                        Forms\Components\TextInput::make('assessment_date')
                            ->label('تاريخ إجراء الاختبار')
                            ->default(fn ($record) => $record?->created_at?->format('Y-m-d H:i') ?? '—')
                            ->formatStateUsing(fn ($state, $record) => $record?->created_at?->format('Y-m-d H:i') ?? '—')
                            ->disabled(),
                    ])->columns(4)->columnSpanFull(),

                // Section 2: Skin Concerns & Goals
                Components\Section::make('🎯 مشاكل البشرة وأهداف العناية المحددة')
                    ->schema([
                        Forms\Components\Textarea::make('concerns_list')
                            ->label('مشاكل البشرة المحددة بالاختبار')
                            ->default(function ($record) {
                                if (!$record) return '—';
                                return $record->concerns()->with('concern')->get()
                                    ->map(fn ($c) => $c->concern?->name_ar ?? $c->concern?->name_en)
                                    ->filter()->join(' • ') ?: 'لا توجد مشاكل محددة';
                            })
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record) return '—';
                                return $record->concerns()->with('concern')->get()
                                    ->map(fn ($c) => $c->concern?->name_ar ?? $c->concern?->name_en)
                                    ->filter()->join(' • ') ?: 'لا توجد مشاكل محددة';
                            })
                            ->rows(2)
                            ->disabled(),

                        Forms\Components\Textarea::make('goals_list')
                            ->label('أهداف العناية بالبشرة المحددة')
                            ->default(function ($record) {
                                if (!$record) return '—';
                                return $record->assessment_goals()->with('goal')->get()
                                    ->map(fn ($g) => $g->goal?->name_ar ?? $g->goal?->name_en)
                                    ->filter()->join(' • ') ?: 'لا توجد أهداف محددة';
                            })
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record) return '—';
                                return $record->assessment_goals()->with('goal')->get()
                                    ->map(fn ($g) => $g->goal?->name_ar ?? $g->goal?->name_en)
                                    ->filter()->join(' • ') ?: 'لا توجد أهداف محددة';
                            })
                            ->rows(2)
                            ->disabled(),
                    ])->columns(2)->columnSpanFull(),

                // Section 3: Quiz Answers (Questions & Options)
                Components\Section::make('❓ تفاصيل إجابات أسئلة التقييم (Quiz Answers)')
                    ->schema([
                        Forms\Components\Repeater::make('answers_detail')
                            ->label('إجابات الأسئلة')
                            ->default(function ($record) {
                                if (!$record) return [];
                                return $record->answers()->with(['question', 'answer'])->get()->map(function ($ans) {
                                    return [
                                        'question_text' => $ans->question?->question_ar ?: ($ans->question?->question_en ?? 'سؤال غير معنون'),
                                        'answer_text'   => $ans->answer?->option_text_ar ?: ($ans->answer?->option_text_en ?? 'إجابة غير معنونة'),
                                    ];
                                })->toArray();
                            })
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record) return [];
                                return $record->answers()->with(['question', 'answer'])->get()->map(function ($ans) {
                                    return [
                                        'question_text' => $ans->question?->question_ar ?: ($ans->question?->question_en ?? 'سؤال غير معنون'),
                                        'answer_text'   => $ans->answer?->option_text_ar ?: ($ans->answer?->option_text_en ?? 'إجابة غير معنونة'),
                                    ];
                                })->toArray();
                            })
                            ->schema([
                                Forms\Components\TextInput::make('question_text')
                                    ->label('السؤال')
                                    ->disabled()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('answer_text')
                                    ->label('الإجابة المختارة')
                                    ->disabled()
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // Section 4: Recommended Products & Routine
                Components\Section::make('🧴 المنتجات المقترحة والروتين العلاجي (Recommended Products)')
                    ->schema([
                        Forms\Components\Repeater::make('recommended_products_detail')
                            ->label('قائمة المنتجات المقترحة')
                            ->default(function ($record) {
                                if (!$record) return [];
                                $products = $record->getRecommendedProductsCollection();
                                return $products->map(function ($prod, $index) {
                                    return [
                                        'step'         => 'الخطوة ' . ($index + 1),
                                        'product_name' => $prod->name_ar ?: $prod->name_en,
                                        'price'        => $prod->price,
                                    ];
                                })->toArray();
                            })
                            ->formatStateUsing(function ($state, $record) {
                                if (!$record) return [];
                                $products = $record->getRecommendedProductsCollection();
                                return $products->map(function ($prod, $index) {
                                    return [
                                        'step'         => 'الخطوة ' . ($index + 1),
                                        'product_name' => $prod->name_ar ?: $prod->name_en,
                                        'price'        => $prod->price,
                                    ];
                                })->toArray();
                            })
                            ->schema([
                                Forms\Components\TextInput::make('step')
                                    ->label('الخطوة / المرحلة')
                                    ->disabled(),

                                Forms\Components\TextInput::make('product_name')
                                    ->label('اسم المنتج المقترح')
                                    ->disabled()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->prefix('EGP')
                                    ->disabled(),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'Customer' : 'العميل')
                    ->searchable()
                    ->default($isEn ? 'Unregistered Guest' : 'زائر غير مسجل'),

                Tables\Columns\TextColumn::make('skinType')
                    ->label($isEn ? 'Resulting Skin Type' : 'نوع البشرة الناتج')
                    ->formatStateUsing(fn ($record) => $isEn ? ($record->skinType?->name_en ?: $record->skinType?->name_ar) : ($record->skinType?->name_ar ?: $record->skinType?->name_en))
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('concerns_summary')
                    ->label($isEn ? 'Skin Concerns' : 'مشاكل البشرة')
                    ->state(function ($record) {
                        $concerns = $record->concerns()->with('concern')->get();
                        if ($concerns->isEmpty()) return '—';
                        return $concerns->map(fn ($c) => $c->concern?->name_ar ?? $c->concern?->name_en)->filter()->join(', ');
                    })
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('recommended_products_summary')
                    ->label($isEn ? 'Recommended Products' : 'المنتجات المقترحة')
                    ->state(function ($record) {
                        $prods = $record->getRecommendedProductsCollection();
                        $count = $prods->count();
                        return $count > 0 ? "{$count} منتجات مقترحة" : 'لا توجد منتجات مقترحة';
                    })
                    ->badge()
                    ->color(fn ($state) => str_contains($state, 'لا توجد') ? 'gray' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Assessment Date' : 'تاريخ إجراء الاختبار')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('skin_type_id')
                    ->label('نوع البشرة الناتج')
                    ->relationship('skinType', 'name_ar'),
            ])
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
            'index' => Pages\ListAssessments::route('/'),
            'view' => Pages\ViewAssessment::route('/{record}'),
        ];
    }
}
