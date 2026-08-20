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
                                    ->label('الاسم المختصر بالعربية')
                                    ->required(),

                                Forms\Components\TextInput::make('short_name_en')
                                    ->label('الاسم المختصر بالإنجليزية')
                                    ->required(),

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
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('brand_id')
                                    ->label('العلامة التجارية')
                                    ->relationship('brand', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

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
                                    ->directory('products')
                                    ->required()
                                    ->formatStateUsing(fn ($state) => $state ? (str_starts_with($state, 'products/') ? $state : 'products/' . ltrim($state, '/')) : null),
                            ])->columns(2),

                        Components\Tabs\Tab::make('الوصف والاستخدام')
                            ->schema([
                                Forms\Components\Textarea::make('description_ar')
                                    ->label('الوصف بالعربية')
                                    ->rows(4)
                                    ->required(),

                                Forms\Components\Textarea::make('description_en')
                                    ->label('الوصف بالإنجليزية')
                                    ->rows(4)
                                    ->required(),

                                Forms\Components\Textarea::make('ingredients_ar')
                                    ->label('المكونات بالعربية')
                                    ->rows(3)
                                    ->required(),

                                Forms\Components\Textarea::make('ingredients_en')
                                    ->label('المكونات بالإنجليزية')
                                    ->rows(3)
                                    ->required(),

                                Forms\Components\Textarea::make('how_to_use_ar')
                                    ->label('طريقة الاستخدام بالعربية')
                                    ->rows(3)
                                    ->required(),

                                Forms\Components\Textarea::make('how_to_use_en')
                                    ->label('طريقة الاستخدام بالإنجليزية')
                                    ->rows(3)
                                    ->required(),
                            ])->columns(2),

                        Components\Tabs\Tab::make('تفاصيل KCODE الطبية والروتين')
                            ->schema([
                                Forms\Components\TextInput::make('texture_ar')
                                    ->label('الملمس / القوام (عربي)')
                                    ->required(),

                                Forms\Components\TextInput::make('texture_en')
                                    ->label('الملمس / القوام (إنجليزي)')
                                    ->required(),

                                Forms\Components\Textarea::make('why_kcode_ar')
                                    ->label('لماذا اختار KCODE هذا المنتج؟ (عربي)')
                                    ->required(),

                                Forms\Components\TextInput::make('usage_frequency_ar')
                                    ->label('معدل الاستخدام (عربي)')
                                    ->required(),

                                Forms\Components\Select::make('active_strength_level')
                                    ->label('مستوى كفاءة المواد الفعالة')
                                    ->options([
                                        'Low' => 'منخفض (Low)',
                                        'Medium' => 'متوسط (Medium)',
                                        'High' => 'مرتفع (High)',
                                    ])
                                    ->required(),

                                Forms\Components\Textarea::make('safety_notes_ar')
                                    ->label('ملاحظات الأمان والتحذيرات')
                                    ->required(),
                            ])->columns(2),

                        Components\Tabs\Tab::make('SEO ومحركات البحث')
                            ->schema([
                                Forms\Components\TextInput::make('final_url_slug')
                                    ->label('رابط الصفحة (Slug)')
                                    ->required(),

                                Forms\Components\TextInput::make('seo_meta_title_ar')
                                    ->label('عنوان SEO (عربي)')
                                    ->required(),

                                Forms\Components\Textarea::make('meta_description_ar')
                                    ->label('وصف Meta (عربي)')
                                    ->required(),

                                Forms\Components\Textarea::make('meta_description_en')
                                    ->label('وصف Meta (إنجليزي)')
                                    ->required(),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        $exportHeaders = $isEn ? [
            'SKU', 'Name (AR)', 'Name (EN)', 'Category', 'Brand', 'Price', 'Stock', 'Status'
        ] : [
            'رمز SKU', 'الاسم بالعربية', 'الاسم بالإنجليزية', 'القسم', 'العلامة التجارية', 'السعر', 'المخزون', 'الحالة'
        ];

        $exportRowCallback = fn ($record) => [
            $record->sku,
            $record->name_ar,
            $record->name_en,
            $record->category?->name_ar ?? '',
            $record->brand?->name_ar ?? '',
            $record->price,
            $record->stock,
            $record->status,
        ];

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label($isEn ? 'Image' : 'الصورة')
                    ->state(fn ($record) => $record->image_path)
                    ->square(),

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
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'products',
                    function (array $row) {
                        $catName = $row['category_name'] ?? ($row['category'] ?? '');
                        $category = $catName ? \App\Models\Category::firstOrCreate(['name_ar' => $catName], ['name_en' => $catName]) : null;
                        
                        $brandName = $row['brand_name'] ?? ($row['brand'] ?? '');
                        $brand = $brandName ? \App\Models\Brand::firstOrCreate(['name_ar' => $brandName], ['name_en' => $brandName]) : null;

                        $updateData = array_filter([
                            'name_ar'               => $row['name_ar'] ?? ($row['name'] ?? null),
                            'name_en'               => $row['name_en'] ?? null,
                            'price'                 => isset($row['price']) && $row['price'] !== '' ? (float) $row['price'] : null,
                            'stock'                 => isset($row['stock']) && $row['stock'] !== '' ? (int) $row['stock'] : null,
                            'image'                 => $row['image'] ?? null,
                            'category_id'           => $category?->id,
                            'brand_id'              => $brand?->id,
                            'description_ar'        => $row['description_ar'] ?? null,
                            'description_en'        => $row['description_en'] ?? null,
                            'short_name_ar'         => $row['short_name_ar'] ?? null,
                            'short_name_en'         => $row['short_name_en'] ?? null,
                            'ingredients_ar'        => $row['ingredients_ar'] ?? null,
                            'ingredients_en'        => $row['ingredients_en'] ?? null,
                            'how_to_use_ar'         => $row['how_to_use_ar'] ?? null,
                            'how_to_use_en'         => $row['how_to_use_en'] ?? null,
                            'final_url_slug'        => $row['final_url_slug'] ?? null,
                            'seo_meta_title_ar'     => $row['seo_meta_title_ar'] ?? null,
                            'meta_description_ar'   => $row['meta_description_ar'] ?? null,
                            'meta_description_en'   => $row['meta_description_en'] ?? null,
                            'primary_keyword_ar'    => $row['primary_keyword_ar'] ?? null,
                            'secondary_keywords_ar' => $row['secondary_keywords_ar'] ?? null,
                            'image_alt_ar'          => $row['image_alt_ar'] ?? null,
                            'keywords'              => $row['keywords'] ?? null,
                        ], fn($val) => !is_null($val));

                        if (!isset($updateData['name_ar']) || empty($updateData['name_ar'])) {
                            $updateData['name_ar'] = 'منتج جديد';
                        }
                        if (!isset($updateData['name_en']) || empty($updateData['name_en'])) {
                            $updateData['name_en'] = $updateData['name_ar'];
                        }
                        if (!isset($updateData['price'])) {
                            $updateData['price'] = 0;
                        }

                        \App\Models\Product::updateOrCreate(
                            ['sku' => !empty($row['sku']) ? $row['sku'] : ('SKU-' . uniqid())],
                            $updateData
                        );
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'products',
                    $exportHeaders,
                    $exportRowCallback,
                    \App\Models\Product::class
                ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'selected_products',
                        $exportHeaders,
                        $exportRowCallback
                    ),
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
