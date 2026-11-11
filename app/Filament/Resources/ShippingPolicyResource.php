<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingPolicyResource\Pages;
use App\Models\ShippingPolicy;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingPolicyResource extends Resource
{
    protected static ?string $model = ShippingPolicy::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-s-truck';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Settings & Policies' : 'إدارة الإعدادات والسياسات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Shipping Policy' : 'سياسة الشحن والتوصيل';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Shipping Policies' : 'سياسة الشحن والتوصيل';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Shipping Policy' : 'سياسة الشحن والتوصيل';
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

                Components\Section::make('محتوى سياسة الشحن (بالعربية)')
                    ->schema([
                        Forms\Components\Repeater::make('content_ar.cards')
                            ->label('البطاقات الترويجية / الميزات السريعة')
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label('رمز الأيقونة (location, clock, shield)')
                                    ->required(),
                                Forms\Components\TextInput::make('title')
                                    ->label('عنوان البطاقة')
                                    ->required(),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('الوصف الفرعي')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->addActionLabel('إضافة بطاقة جديدة (عربي)'),

                        Forms\Components\Repeater::make('content_ar.sections')
                            ->label('بنود سياسة الشحن والتوصيل')
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

                Components\Section::make('Shipping Policy Content (English)')
                    ->schema([
                        Forms\Components\Repeater::make('content_en.cards')
                            ->label('Highlight Feature Cards')
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label('Icon Key (location, clock, shield)')
                                    ->required(),
                                Forms\Components\TextInput::make('title')
                                    ->label('Card Title')
                                    ->required(),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('Subtitle / Details')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->addActionLabel('Add New Card (English)'),

                        Forms\Components\Repeater::make('content_en.sections')
                            ->label('Shipping Policy Sections')
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
            'index' => Pages\ListShippingPolicies::route('/'),
            'create' => Pages\CreateShippingPolicy::route('/create'),
            'edit' => Pages\EditShippingPolicy::route('/{record}/edit'),
        ];
    }
}
