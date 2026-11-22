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

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Preset Routines' : 'الروتينات المجهزة';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Preset Routines' : 'الروتينات المجهزة';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Preset Routine' : 'روتين مجهز';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('البيانات الأساسية للروتين')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('المعرف البرمجي (Slug)')
                            ->placeholder('مثال: acne, pores, daily, shaving')
                            ->helperText('يُستخدم للربط مع الفرونت والروابط المباشرة (#routines?routine=acne)')
                            ->required(),

                        Forms\Components\Toggle::make('is_for_men')
                            ->label('روتين مخصص للرجال (Men Routine)')
                            ->live()
                            ->default(false),

                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان الروتين (بالعربية)')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('عنوان الروتين (بالإنجليزية)')
                            ->required(),

                        Forms\Components\TextInput::make('subtitle_ar')
                            ->label('العنوان الفرعي (بالعربية)')
                            ->placeholder('تنظيف وترطيب وحماية من الشمس بخطوات بسيطة.'),

                        Forms\Components\TextInput::make('subtitle_en')
                            ->label('العنوان الفرعي (بالإنجليزية)'),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('وصف الروتين (بالعربية)')
                            ->rows(3),

                        Forms\Components\Textarea::make('description_en')
                            ->label('وصف الروتين (بالإنجليزية)')
                            ->rows(3),

                        Forms\Components\FileUpload::make('cover_image')
                            ->label('صورة غلاف الروتين (Cover Image)')
                            ->disk('public')
                            ->directory('routines')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->helperText('صورة المنتجات المجمعة المعتمدة للروتين'),

                        Forms\Components\TextInput::make('badge_ar')
                            ->label('الشارة العلوية (بالعربية)')
                            ->placeholder('مثال: مختارة وفق مراجعة KCODE للمركبات'),

                        Forms\Components\TextInput::make('badge_en')
                            ->label('الشارة العلوية (بالإنجليزية)'),

                        Forms\Components\TextInput::make('skin_type_ar')
                            ->label('ملاءمة البشرة (بالعربية)')
                            ->placeholder('مثال: مناسب لـ: البشرة الدهنية والمختلطة'),

                        Forms\Components\TextInput::make('skin_type_en')
                            ->label('ملاءمة البشرة (بالإنجليزية)'),

                        Forms\Components\TextInput::make('goal_ar')
                            ->label('هدف الروتين (بالعربية)')
                            ->placeholder('مثال: العناية بالحبوب والبثور'),

                        Forms\Components\TextInput::make('goal_en')
                            ->label('هدف الروتين (بالإنجليزية)'),

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

                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط (Active)',
                                'inactive' => 'غير نشط (Inactive)',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Components\Section::make('مميزات ومحتوى الروتين')
                    ->schema([
                        Forms\Components\TagsInput::make('benefits_json')
                            ->label('النقاط البارزة للروتين (عربي - Benefits) - تظهر في بطاقة الروتين')
                            ->placeholder('أضف فائدة واضغط Enter'),

                        Forms\Components\TagsInput::make('benefits_en_json')
                            ->label('Routine Highlights (English - Benefits)')
                            ->placeholder('Add benefit and press Enter'),

                        Forms\Components\TagsInput::make('what_it_offers_json')
                            ->label('ما يقدمه الروتين بالتفصيل (عربي - What it offers) - صفحة التفاصيل')
                            ->placeholder('أضف فائدة مفصلة واضغط Enter'),

                        Forms\Components\TagsInput::make('what_it_offers_en_json')
                            ->label('What It Offers (English)')
                            ->placeholder('Add detailed benefit and press Enter'),

                        Forms\Components\Textarea::make('how_to_use_ar')
                            ->label('إرشادات الاستخدام العامة (بالعربية)')
                            ->rows(3),

                        Forms\Components\Textarea::make('how_to_use_en')
                            ->label('General How To Use (English)')
                            ->rows(3),

                        Forms\Components\Textarea::make('card_note_ar')
                            ->label('ملاحظة البطاقة (عربي - Card Note)')
                            ->placeholder('مثال: ترطيب صباحي عند الحاجة، قبل واقي الشمس.')
                            ->rows(2),

                        Forms\Components\Textarea::make('card_note_en')
                            ->label('Card Note (English)')
                            ->placeholder('Example: Apply morning moisturizer as needed before sunscreen.')
                            ->rows(2),

                        Forms\Components\Textarea::make('audience_ar')
                            ->label('لمن هذا الروتين؟ (عربي - Audience)')
                            ->placeholder('مثال: كنت تبحث عن روتين يومي بسيط لتنظيف بشرتك وترطيبها...')
                            ->rows(2),

                        Forms\Components\Textarea::make('audience_en')
                            ->label('Target Audience (English)')
                            ->placeholder('Example: If you are looking for a simple daily routine...')
                            ->rows(2),

                        Forms\Components\TextInput::make('timing_json.am')
                            ->label('توقيت الصباح (عربي - Timing AM)')
                            ->placeholder('غسول ثم واقي شمس'),

                        Forms\Components\TextInput::make('timing_json.am_en')
                            ->label('Morning Timing (English - Timing AM)')
                            ->placeholder('Cleanser then sunscreen'),

                        Forms\Components\TextInput::make('timing_json.pm')
                            ->label('توقيت المساء (عربي - Timing PM)')
                            ->placeholder('غسول ثم مرطب جل'),

                        Forms\Components\TextInput::make('timing_json.pm_en')
                            ->label('Evening Timing (English - Timing PM)')
                            ->placeholder('Cleanser then gel moisturizer'),

                        Forms\Components\TextInput::make('cover_json.alt')
                            ->label('النص البديل لصورة الغلاف (عربي - Cover Alt)'),

                        Forms\Components\TextInput::make('cover_json.alt_en')
                            ->label('Cover Image Alt (English)'),
                    ])->columns(2)->collapsed(),

                Components\Section::make('دليل الاستخدام التفصيلي (Usage Guide)')
                    ->schema([
                        Forms\Components\TextInput::make('usage_guide_json.am_title')
                            ->label('عنوان الصباح (عربي)')
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\TextInput::make('usage_guide_json.am_title_en')
                            ->label('Morning Title (English)')
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\Repeater::make('usage_guide_json.am_steps')
                            ->label('خطوات الصباح التفصيلية (AM Steps)')
                            ->schema([
                                Forms\Components\TextInput::make('step_number')->numeric()->label('الخطوة')->required(),
                                Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                                Forms\Components\TextInput::make('role')->label('الدور (عربي)'),
                                Forms\Components\TextInput::make('role_en')->label('Role (English)'),
                                Forms\Components\Textarea::make('application')->label('طريقة التطبيق (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('application_en')->label('Application (English)')->rows(2)->columnSpanFull(),
                                Forms\Components\TextInput::make('frequency')->label('التكرار (عربي)'),
                                Forms\Components\TextInput::make('frequency_en')->label('Frequency (English)'),
                                Forms\Components\Textarea::make('starter_guide')->label('إرشاد البدء (عربي)')->rows(2),
                                Forms\Components\Textarea::make('starter_guide_en')->label('Starter Guide (English)')->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull()
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\TextInput::make('usage_guide_json.pm_title')
                            ->label('عنوان المساء (عربي)')
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\TextInput::make('usage_guide_json.pm_title_en')
                            ->label('Evening Title (English)')
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\TextInput::make('usage_guide_json.pm_standard_label')
                            ->label('تسمية الليلة المعتادة (عربي)')
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\TextInput::make('usage_guide_json.pm_standard_label_en')
                            ->label('Regular Night Label (English)')
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\Repeater::make('usage_guide_json.pm_standard_steps')
                            ->label('خطوات المساء المعتادة (PM Standard Steps)')
                            ->schema([
                                Forms\Components\TextInput::make('step_number')->numeric()->label('الخطوة')->required(),
                                Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                                Forms\Components\TextInput::make('role')->label('الدور (عربي)'),
                                Forms\Components\TextInput::make('role_en')->label('Role (English)'),
                                Forms\Components\Textarea::make('application')->label('طريقة التطبيق (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('application_en')->label('Application (English)')->rows(2)->columnSpanFull(),
                                Forms\Components\TextInput::make('frequency')->label('التكرار (عربي)'),
                                Forms\Components\TextInput::make('frequency_en')->label('Frequency (English)'),
                                Forms\Components\Textarea::make('starter_guide')->label('إرشاد البدء (عربي)')->rows(2),
                                Forms\Components\Textarea::make('starter_guide_en')->label('Starter Guide (English)')->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull()
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\Repeater::make('usage_guide_json.pm_variants')
                            ->label('بدائل ليالي العناية الخاصة (PM Variants)')
                            ->schema([
                                Forms\Components\TextInput::make('id')->label('المعرّف (id)'),
                                Forms\Components\TextInput::make('label')->label('العنوان (عربي)'),
                                Forms\Components\TextInput::make('label_en')->label('Label (English)'),
                                Forms\Components\TextInput::make('badge')->label('الشارة (عربي)'),
                                Forms\Components\TextInput::make('badge_en')->label('Badge (English)'),
                                Forms\Components\Textarea::make('note')->label('ملاحظة (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('note_en')->label('Note (English)')->rows(2)->columnSpanFull(),
                                Forms\Components\Repeater::make('steps')
                                    ->label('خطوات الليلة المخصصة')
                                    ->schema([
                                        Forms\Components\TextInput::make('step_number')->numeric()->label('الخطوة')->required(),
                                        Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                                        Forms\Components\TextInput::make('role')->label('الدور (عربي)'),
                                        Forms\Components\TextInput::make('role_en')->label('Role (English)'),
                                        Forms\Components\Textarea::make('application')->label('طريقة التطبيق (عربي)')->rows(2)->columnSpanFull(),
                                        Forms\Components\Textarea::make('application_en')->label('Application (English)')->rows(2)->columnSpanFull(),
                                        Forms\Components\TextInput::make('frequency')->label('التكرار (عربي)'),
                                        Forms\Components\TextInput::make('frequency_en')->label('Frequency (English)'),
                                        Forms\Components\Textarea::make('starter_guide')->label('إرشاد البدء (عربي)')->rows(2),
                                        Forms\Components\Textarea::make('starter_guide_en')->label('Starter Guide (English)')->rows(2),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull()
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\Textarea::make('usage_guide_json.night_notes')
                            ->label('ملاحظات المساء (عربي)')
                            ->rows(2)
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\Textarea::make('usage_guide_json.night_notes_en')
                            ->label('Evening Notes (English)')
                            ->rows(2)
                            ->visible(fn ($get) => !(bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => !(bool) $get('is_for_men')),

                        Forms\Components\Repeater::make('usage_guide_json.morning_steps')
                            ->label('خطوات الصباح للرجال (Morning Steps)')
                            ->schema([
                                Forms\Components\TextInput::make('step_number')->numeric()->label('الخطوة')->required(),
                                Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                                Forms\Components\TextInput::make('role_ar')->label('الدور (عربي)'),
                                Forms\Components\TextInput::make('role_en')->label('Role (English)'),
                                Forms\Components\Textarea::make('instruction_ar')->label('التعليمات (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('instruction_en')->label('Instruction (English)')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull()
                            ->visible(fn ($get) => (bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => (bool) $get('is_for_men')),

                        Forms\Components\Repeater::make('usage_guide_json.evening_variants')
                            ->label('خطوات المساء للرجال (Evening Variants)')
                            ->schema([
                                Forms\Components\TextInput::make('id')->label('المعرّف (id)'),
                                Forms\Components\TextInput::make('label_ar')->label('التسمية (عربي)'),
                                Forms\Components\TextInput::make('label_en')->label('Label (English)'),
                                Forms\Components\Repeater::make('steps')
                                    ->label('خطوات المساء')
                                    ->schema([
                                        Forms\Components\TextInput::make('step_number')->numeric()->label('الخطوة')->required(),
                                        Forms\Components\TextInput::make('sku')->label('SKU')->required(),
                                        Forms\Components\TextInput::make('role_ar')->label('الدور (عربي)'),
                                        Forms\Components\TextInput::make('role_en')->label('Role (English)'),
                                        Forms\Components\Textarea::make('instruction_ar')->label('التعليمات (عربي)')->rows(2)->columnSpanFull(),
                                        Forms\Components\Textarea::make('instruction_en')->label('Instruction (English)')->rows(2)->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull()
                            ->visible(fn ($get) => (bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => (bool) $get('is_for_men')),

                        Forms\Components\TagsInput::make('usage_guide_json.notes_ar')
                            ->label('ملاحظات الروتين للرجال (عربي)')
                            ->placeholder('أضف ملاحظة واضغط Enter')
                            ->visible(fn ($get) => (bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => (bool) $get('is_for_men')),

                        Forms\Components\TagsInput::make('usage_guide_json.notes_en')
                            ->label('Men Routine Notes (English)')
                            ->placeholder('Add note and press Enter')
                            ->visible(fn ($get) => (bool) $get('is_for_men'))
                            ->dehydrated(fn ($get) => (bool) $get('is_for_men')),
                    ])->columns(2)->collapsed(),

                Components\Section::make('المنتجات المكملة للروتين (Complementary Products)')
                    ->schema([
                        Forms\Components\Repeater::make('complementary_products_json')
                            ->label('المنتجات المكملة والداعمة')
                            ->schema([
                                Forms\Components\TextInput::make('sku')->label('SKU المنتج')->required(),
                                Forms\Components\TextInput::make('name')->label('اسم المنتج (اختياري)'),
                                Forms\Components\TextInput::make('step')->label('الخطوة / التصنيف (عربي)'),
                                Forms\Components\TextInput::make('step_en')->label('Step / Category (English)'),
                                Forms\Components\Textarea::make('reason')->label('لماذا هذا المنتج؟ (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('reason_en')->label('Why Selected (English)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('application')->label('طريقة التطبيق (عربي)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('application_en')->label('Application Method (English)')->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('note')->label('ملاحظة (عربي)')->rows(2),
                                Forms\Components\Textarea::make('note_en')->label('Note (English)')->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull(),
                    ])->collapsed(),

                Components\Section::make('خطوات ومنتجات الروتين الأساسية (Routine Steps)')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('المنتج')
                                    ->relationship('product', 'name_en')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->sku}] {$record->name_en} ({$record->name_ar})")
                                    ->searchable(['name_en', 'name_ar', 'sku'])
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('display_order')
                                    ->label('ترتيب الخطوة')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('step_name_ar')
                                    ->label('اسم الخطوة (بالعربية)')
                                    ->placeholder('مثال: غسول، سيروم، مرطب')
                                    ->required(),

                                Forms\Components\TextInput::make('step_name_en')
                                    ->label('اسم الخطوة (بالإنجليزية)'),

                                Forms\Components\Toggle::make('morning')
                                    ->label('استخدام صباحي (AM)')
                                    ->default(true),

                                Forms\Components\Toggle::make('night')
                                    ->label('استخدام مسائي (PM)')
                                    ->default(true),

                                Forms\Components\TextInput::make('use_time_ar')
                                    ->label('توقيت الاستخدام (بالعربية)')
                                    ->placeholder('صباحاً ومساءً'),

                                Forms\Components\TextInput::make('use_time_en')
                                    ->label('Use Time (English)')
                                    ->placeholder('Morning & Evening'),

                                Forms\Components\TextInput::make('frequency_ar')
                                    ->label('التكرار (بالعربية)')
                                    ->placeholder('التكرار يومياً.')
                                    ->default('التكرار يومياً.'),

                                Forms\Components\TextInput::make('frequency_en')
                                    ->label('Frequency (English)')
                                    ->placeholder('Daily.')
                                    ->default('Daily.'),

                                Forms\Components\Textarea::make('reason_ar')
                                    ->label('لماذا اخترناه لك؟ (بالعربية)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('reason_en')
                                    ->label('Why We Chose It (English)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('application_ar')
                                    ->label('طريقة التطبيق (بالعربية)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('application_en')
                                    ->label('Application Method (English)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('starter_guide_ar')
                                    ->label('إرشاد البدء للمركبات النشطة (بالعربية)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('starter_guide_en')
                                    ->label('Active Starter Guide (English)')
                                    ->rows(2)
                                    ->columnSpanFull(),
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
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('الغلاف')
                    ->disk('public')
                    ->circular(false),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title_ar')
                    ->label('العنوان (عربي)')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_for_men')
                    ->label('للرجال')
                    ->boolean(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('المنتجات')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_for_men')
                    ->label('الفئة')
                    ->options([
                        '0' => 'روتينات عامة / نسائية',
                        '1' => 'روتينات رجالية',
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
