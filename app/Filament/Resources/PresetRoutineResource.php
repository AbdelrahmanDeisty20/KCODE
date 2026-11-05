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

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Routine Engine & Quiz' : 'محرك الروتينات والـ Quiz';
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
                Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('المعرف البرمجي (Slug)')
                            ->placeholder('مثال: acne, pores, daily, shaving')
                            ->helperText('يُستخدم للربط مع الفرونت والروابط المباشرة (#routines?routine=acne)')
                            ->required(),

                        Forms\Components\Toggle::make('is_for_men')
                            ->label('روتين مخصص للرجال (Men Routine)')
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
                            ->label('النقاط البارزة للروتين (Benefits) - تظهر في بطاقة الروتين')
                            ->placeholder('أضف فائدة واضغط Enter'),

                        Forms\Components\TagsInput::make('what_it_offers_json')
                            ->label('ما يقدمه الروتين بالتفصيل (What it offers) - تظهر في صفحة التفاصيل')
                            ->placeholder('أضف فائدة مفصلة واضغط Enter'),

                        Forms\Components\Textarea::make('how_to_use_ar')
                            ->label('إرشادات الاستخدام العامة (بالعربية)')
                            ->rows(3),

                        Forms\Components\Textarea::make('card_note_ar')
                            ->label('ملاحظة البطاقة (Card Note)')
                            ->placeholder('مثال: ترطيب صباحي عند الحاجة، قبل واقي الشمس.'),

                        Forms\Components\Textarea::make('audience_ar')
                            ->label('لمن هذا الروتين؟ (Audience)')
                            ->placeholder('مثال: كنت تبحث عن روتين يومي بسيط لتنظيف بشرتك وترطيبها...'),
                    ])->columns(2)->collapsed(),

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
                                    ->label('توقيت الاستخدام')
                                    ->placeholder('صباحاً ومساءً'),

                                Forms\Components\TextInput::make('frequency_ar')
                                    ->label('التكرار (بالعربية)')
                                    ->placeholder('التكرار يومياً.')
                                    ->default('التكرار يومياً.'),

                                Forms\Components\Textarea::make('reason_ar')
                                    ->label('لماذا اخترناه لك؟ (بالعربية)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('application_ar')
                                    ->label('طريقة التطبيق (Application)')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('starter_guide_ar')
                                    ->label('إرشاد البدء (للمركبات النشطة)')
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
