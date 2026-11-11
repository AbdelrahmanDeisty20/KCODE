<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponPolicyResource\Pages;
use App\Models\CouponPolicy;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponPolicyResource extends Resource
{
    protected static ?string $model = CouponPolicy::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-ticket';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Settings & Policies' : 'إدارة الإعدادات والسياسات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Coupon Policy' : 'سياسة الكوبونات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Coupon Policy' : 'سياسة الكوبونات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Coupon Policy' : 'سياسة الكوبونات';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('معلومات السياسة')
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان السياسة (بالعربية)')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('عنوان السياسة (بالإنجليزية)')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشطة / مفعّلة')
                            ->default(true),
                    ])->columns(2),

                Components\Section::make('محتوى سياسة الكوبونات (بالعربية)')
                    ->schema([
                        Forms\Components\TextInput::make('content_ar.header_card.title')
                            ->label('عنوان بطاقة الترويج (عربي)'),
                        Forms\Components\TextInput::make('content_ar.header_card.subtitle')
                            ->label('وصف بطاقة الترويج (عربي)'),

                        Forms\Components\Repeater::make('content_ar.sections')
                            ->label('بنود سياسة الكوبونات')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('عنوان البند')
                                    ->required(),
                                Forms\Components\Textarea::make('paragraph')
                                    ->label('تفاصيل البند')
                                    ->rows(3)
                                    ->required(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('إضافة بند جديد (بالعربية)'),
                    ]),

                Components\Section::make('Coupon Policy Content (English)')
                    ->schema([
                        Forms\Components\TextInput::make('content_en.header_card.title')
                            ->label('Promo Card Title (English)'),
                        Forms\Components\TextInput::make('content_en.header_card.subtitle')
                            ->label('Promo Card Subtitle (English)'),

                        Forms\Components\Repeater::make('content_en.sections')
                            ->label('Coupon Policy Sections')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Section Title')
                                    ->required(),
                                Forms\Components\Textarea::make('paragraph')
                                    ->label('Section Details')
                                    ->rows(3)
                                    ->required(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Add New Section (English)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title_ar')->label('العنوان (عربي)')->searchable(),
                Tables\Columns\TextColumn::make('title_en')->label('العنوان (إنجليزي)')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('مفعّلة')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('آخر تحديث')->dateTime()->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouponPolicys::route('/'),
            'create' => Pages\CreateCouponPolicy::route('/create'),
            'edit' => Pages\EditCouponPolicy::route('/{record}/edit'),
        ];
    }
}
