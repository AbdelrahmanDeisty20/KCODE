<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresetRoutineResource\Pages;
use App\Models\PresetRoutine;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PresetRoutineResource extends Resource
{
    protected static ?string $model = PresetRoutine::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-sparkles';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Routine Engine & Quiz' : 'إدارة الروتينات والـ Quiz';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Preset Routines' : 'الروتينات الجاهزة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Preset Routines' : 'الروتينات الجاهزة';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Preset Routine' : 'روتين جاهز';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان الروتين (بالعربية)')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('عنوان الروتين (بالإنجليزية)')
                            ->required(),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('وصف الروتين (بالعربية)')
                            ->rows(3),

                        Forms\Components\Textarea::make('description_en')
                            ->label('وصف الروتين (بالإنجليزية)')
                            ->rows(3),

                        Forms\Components\TextInput::make('badge_ar')
                            ->label('الشارة العلوية (بالعربية)')
                            ->placeholder('مثال: البداية الأبسط للعناية اليومية'),

                        Forms\Components\TextInput::make('badge_en')
                            ->label('الشارة العلوية (بالإنجليزية)'),

                        Forms\Components\TextInput::make('skin_type_ar')
                            ->label('وسم نوع البشرة (بالعربية)')
                            ->placeholder('مثال: مناسب لـ: الدهنية والمختلطة'),

                        Forms\Components\TextInput::make('skin_type_en')
                            ->label('وسم نوع البشرة (بالإنجليزية)'),

                        Forms\Components\TextInput::make('goal_ar')
                            ->label('وسم الهدف (بالعربية)')
                            ->placeholder('مثال: السيطرة على الدهون واللمعان'),

                        Forms\Components\TextInput::make('goal_en')
                            ->label('وسم الهدف (بالإنجليزية)'),

                        Forms\Components\Select::make('skin_type_id')
                            ->label('نوع البشرة المرتبط')
                            ->relationship('skinType', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('goal_id')
                            ->label('هدف الروتين المرتبط')
                            ->relationship('goal', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Toggle::make('is_for_men')
                            ->label('مخصص للرجال (Men Routine)')
                            ->default(false),

                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط (Active)',
                                'inactive' => 'غير نشط (Inactive)',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Components\Section::make('مميزات الروتين (البطاقة والتفاصيل)')
                    ->schema([
                        Forms\Components\TagsInput::make('benefits_json')
                            ->label('نقاط مميزات الروتين (تظهر في كارت القائمة - 4 نقاط)')
                            ->placeholder('اكتب ميزة ثم اضغط Enter')
                            ->columnSpan(2),

                        Forms\Components\TagsInput::make('what_it_offers_json')
                            ->label('ما الذي يقدمه هذا الروتين لبشرتك؟ (الشبكة في صفحة الروتين - 6 نقاط)')
                            ->placeholder('اكتب فائدة ثم اضغط Enter')
                            ->columnSpan(2),
                    ])->columns(2),

                Components\Section::make('تبويبات صفحة الروتين (طريقة الاستخدام والمنتجات المكملة)')
                    ->schema([
                        Forms\Components\Textarea::make('how_to_use_ar')
                            ->label('طريقة الاستخدام (بالعربية)')
                            ->rows(4)
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('how_to_use_en')
                            ->label('طريقة الاستخدام (بالإنجليزية)')
                            ->rows(4)
                            ->columnSpan(2),

                        Forms\Components\Repeater::make('complementary_products_json')
                            ->label('منتجات مكملة لهذا الروتين')
                            ->schema([
                                Forms\Components\TextInput::make('step')->label('الخطوة (مثال: خطوة مكملة)'),
                                Forms\Components\TextInput::make('name')->label('اسم المنتج'),
                                Forms\Components\TextInput::make('note')->label('ملاحظة سريعة'),
                                Forms\Components\TextInput::make('why')->label('لماذا تحتاجه؟'),
                            ])
                            ->columns(2)
                            ->columnSpan(2)
                            ->createItemButtonLabel('إضافة منتج مكمل'),
                    ])->columns(2)->collapsed(),

                Components\Section::make('منتجات وخطوات الروتين الجاهز')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('المنتج')
                                    ->relationship('product', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('display_order')
                                    ->label('ترتيب الخطوة')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('step_name_ar')
                                    ->label('اسم الخطوة (بالعربية)')
                                    ->placeholder('مثال: غسول تنظيف'),

                                Forms\Components\TextInput::make('step_name_en')
                                    ->label('اسم الخطوة (بالإنجليزية)'),

                                Forms\Components\Toggle::make('morning')
                                    ->label('استخدام صباحي (AM)')
                                    ->default(true),

                                Forms\Components\Toggle::make('night')
                                    ->label('استخدام مسائي (PM)')
                                    ->default(true),

                                Forms\Components\TextInput::make('use_time_ar')
                                    ->label('توقيت الاستخدام')
                                    ->placeholder('صباحاً ومساءً'),

                                Forms\Components\TextInput::make('frequency_ar')
                                    ->label('التكرار (بالعربية)')
                                    ->placeholder('التكرار يومياً.')
                                    ->default('التكرار يومياً.'),

                                Forms\Components\TextInput::make('frequency_en')
                                    ->label('التكرار (بالإنجليزية)')
                                    ->placeholder('Daily frequency.'),

                                Forms\Components\Textarea::make('reason_ar')
                                    ->label('لماذا اخترناه لك؟ (بالعربية)')
                                    ->placeholder('سيروم نياسيناميد 15% لتقليل اللمعان وتحسين مظهر المسام وتفاوت اللون.')
                                    ->rows(2)
                                    ->columnSpan(3),

                                Forms\Components\Textarea::make('application_ar')
                                    ->label('التطبيق - طريقة الاستخدام (بالعربية)')
                                    ->placeholder('كوّن رغوة بكمية مناسبة بين كفّين مبلّلتين، دلّك الوجه بلطف، ثم اشطفه بالماء الفاتر.')
                                    ->rows(2)
                                    ->columnSpan(3),

                                Forms\Components\Textarea::make('starter_guide_ar')
                                    ->label('طريقة البدء (بالعربية - للمقشرات والمكونات النشطة)')
                                    ->placeholder('ابدأ مرة أو مرتين أسبوعياً. اختر وقتاً واحداً في يوم الاستخدام: صباحاً أو في ليلة دون تقشير.')
                                    ->rows(2)
                                    ->columnSpan(3),

                                Forms\Components\Textarea::make('reason_en')
                                    ->label('لماذا اخترناه لك؟ (بالإنجليزية)')
                                    ->rows(2)
                                    ->columnSpan(3),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->createItemButtonLabel('إضافة خطوة للروتين'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title_ar')->label('العنوان (عربي)')->searchable(),
                Tables\Columns\TextColumn::make('badge_ar')->label('الشارة')->searchable(),
                Tables\Columns\TextColumn::make('skin_type_ar')->label('وسم البشرة'),
                Tables\Columns\IconColumn::make('is_for_men')
                    ->label('مخصص للرجال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('عدد الخطوات')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('status')->label('الحالة'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_for_men')
                    ->label('الفئة')
                    ->options([
                        '0' => 'روتينات عامة / نسائية',
                        '1' => 'روتيناترجال',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresetRoutines::route('/'),
            'create' => Pages\CreatePresetRoutine::route('/create'),
            'edit' => Pages\EditPresetRoutine::route('/{record}/edit'),
        ];
    }
}
