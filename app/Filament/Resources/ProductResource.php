<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-products';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Products & Catalog' : 'الكتالوج والمنتجات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Products' : 'المنتجات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Products' : 'المنتجات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product' : 'منتج';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Tabs::make('تفاصيل المنتج')
                    ->tabs([
                        Components\Tabs\Tab::make('البيانات الأساسية')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label('رمز المنتج (SKU)')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('name_ar')
                                    ->label('الاسم بالعربية')
                                    ->required(),

                                Forms\Components\TextInput::make('name_en')
                                    ->label('الاسم بالإنجليزية')
                                    ->required(),

                                Forms\Components\TextInput::make('short_name_ar')
                                    ->label('الاسم المختصر بالعربية'),

                                Forms\Components\TextInput::make('short_name_en')
                                    ->label('الاسم المختصر بالإنجليزية'),

                                Forms\Components\Select::make('category_id')
                                    ->label('القسم الرئيسي')
                                    ->relationship('category', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('sub_category_id')
                                    ->label('القسم الفرعي')
                                    ->relationship('subCategory', 'name_ar')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('brand_id')
                                    ->label('العلامة التجارية')
                                    ->relationship('brand', 'name_ar')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->numeric()
                                    ->prefix('EGP')
                                    ->required(),

                                Forms\Components\TextInput::make('stock')
                                    ->label('المخزون')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),

                                Forms\Components\Toggle::make('is_best_seller')
                                    ->label('الأكثر مبيعاً')
                                    ->default(false),

                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options([
                                        'active' => 'نشط',
                                        'inactive' => 'غير نشط',
                                        'draft' => 'مسودة',
                                    ])
                                    ->default('active')
                                    ->required(),

                                Forms\Components\FileUpload::make('image')
                                    ->label('صورة المنتج')
                                    ->image()
                                    ->directory('products'),
                            ])->columns(2),

                        Components\Tabs\Tab::make('الوصف والاستخدام')
                            ->schema([
                                Forms\Components\Textarea::make('description_ar')
                                    ->label('الوصف بالعربية')
                                    ->rows(4),

                                Forms\Components\Textarea::make('description_en')
                                    ->label('الوصف بالإنجليزية')
                                    ->rows(4),

                                Forms\Components\Textarea::make('ingredients_ar')
                                    ->label('المكونات بالعربية')
                                    ->rows(3),

                                Forms\Components\Textarea::make('ingredients_en')
                                    ->label('المكونات بالإنجليزية')
                                    ->rows(3),

                                Forms\Components\Textarea::make('how_to_use_ar')
                                    ->label('طريقة الاستخدام بالعربية')
                                    ->rows(3),

                                Forms\Components\Textarea::make('how_to_use_en')
                                    ->label('طريقة الاستخدام بالإنجليزية')
                                    ->rows(3),
                            ])->columns(2),

                        Components\Tabs\Tab::make('تفاصيل KCODE الطبية والروتين')
                            ->schema([
                                Forms\Components\TextInput::make('texture_ar')
                                    ->label('الملمس / القوام (عربي)'),

                                Forms\Components\TextInput::make('texture_en')
                                    ->label('الملمس / القوام (إنجليزي)'),

                                Forms\Components\Textarea::make('why_kcode_ar')
                                    ->label('لماذا اختار KCODE هذا المنتج؟ (عربي)'),

                                Forms\Components\TextInput::make('usage_frequency_ar')
                                    ->label('معدل الاستخدام (عربي)'),

                                Forms\Components\TextInput::make('active_strength_level')
                                    ->label('مستوى كفاءة المواد الفعالة'),

                                Forms\Components\Textarea::make('safety_notes_ar')
                                    ->label('ملاحظات الأمان والتحذيرات'),
                            ])->columns(2),

                        Components\Tabs\Tab::make('SEO ومحركات البحث')
                            ->schema([
                                Forms\Components\TextInput::make('final_url_slug')
                                    ->label('رابط الصفحة (Slug)'),

                                Forms\Components\TextInput::make('seo_meta_title_ar')
                                    ->label('عنوان SEO (عربي)'),

                                Forms\Components\Textarea::make('meta_description_ar')
                                    ->label('وصف Meta (عربي)'),

                                Forms\Components\Textarea::make('meta_description_en')
                                    ->label('وصف Meta (إنجليزي)'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label($isEn ? 'Image' : 'الصورة')
                    ->getStateUsing(fn ($record) => $record->image),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Product Name' : 'اسم المنتج')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label($isEn ? 'Category' : 'القسم')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->category?->name_en ?: $record->category?->name_ar) : ($record->category?->name_ar ?: $record->category?->name_en))
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand')
                    ->label($isEn ? 'Brand' : 'العلامة التجارية')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->brand?->name_en ?: $record->brand?->name_ar) : ($record->brand?->name_ar ?: $record->brand?->name_en))
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label($isEn ? 'Price' : 'السعر')
                    ->money('OMR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label($isEn ? 'Stock' : 'المخزون')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_best_seller')
                    ->label($isEn ? 'Best Seller' : 'الأكثر مبيعاً')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Created At' : 'تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label($isEn ? 'Category' : 'القسم')
                    ->relationship('category', $isEn ? 'name_en' : 'name_ar'),

                Tables\Filters\TernaryFilter::make('is_best_seller')
                    ->label('الأكثر مبيعاً'),
            ])
            ->actions([
                Actions\ViewAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
