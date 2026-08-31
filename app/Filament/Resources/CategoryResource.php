<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-categories';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Products & Catalog' : 'إدارة المنتجات والكتالوج';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Main Categories' : 'الأقسام الرئيسية';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Main Categories' : 'الأقسام الرئيسية';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Main Category' : 'قسم رئيسي';
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
                Components\Section::make('بيانات القسم')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required(),

                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->required(),

                        Forms\Components\FileUpload::make('image')
                            ->label('صورة القسم')
                            ->image()
                            ->directory('categories')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        $exportHeaders = $isEn ? ['ID', 'Name (AR)', 'Name (EN)'] : ['المعرف', 'الاسم بالعربية', 'الاسم بالإنجليزية'];
        $exportRowCallback = fn ($record) => [
            $record->id,
            $record->name_ar,
            $record->name_en,
        ];

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label($isEn ? 'Image' : 'الصورة')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Category Name' : 'اسم القسم الرئيسي')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name_ar', 'LIKE', "%{$search}%")
                              ->orWhere('name_en', 'LIKE', "%{$search}%");
                        });
                    })
                    ->sortable(),

                \App\Helpers\FilamentImageHelper::makeImageFilenameColumn('image', 'اسم ملف الصورة', 'Image Filename'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Created At' : 'تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                \App\Helpers\FilamentImageHelper::makeUpdateImageAction('categories', 'image'),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentImageHelper::makeBulkUploadHeaderAction('categories', Category::class),
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'categories',
                    function (array $row) {
                        $nameAr = $row['name_ar'] ?? ($row['name'] ?? null);
                        if ($nameAr) {
                            Category::firstOrCreate(
                                ['name_ar' => $nameAr],
                                ['name_en' => $row['name_en'] ?? $nameAr]
                            );
                        }
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'categories',
                    $exportHeaders,
                    $exportRowCallback,
                    Category::class
                ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'selected_categories',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
