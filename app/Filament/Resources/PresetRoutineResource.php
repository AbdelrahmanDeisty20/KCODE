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

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';

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
