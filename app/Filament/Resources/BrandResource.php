<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use BackedEnum;
use UnitEnum;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-brands';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Products & Catalog' : 'إدارة المنتجات والكتالوج';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Brands' : 'العلامات التجارية';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Brands' : 'العلامات التجارية';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Brand' : 'علامة تجارية';
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
                Components\Section::make('بيانات العلامة التجارية')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->nullable(),
                        Forms\Components\TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('شعار العلامة التجارية (Logo)')
                            ->image()
                            ->directory('brands')
                            ->nullable(),
                    ])
                    ->columns(2),
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
                    ->label($isEn ? 'Logo' : 'الشعار')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Brand Name' : 'اسم العلامة التجارية')
                    ->getStateUsing(fn($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name_ar', 'LIKE', "%{$search}%")
                              ->orWhere('name_en', 'LIKE', "%{$search}%");
                        });
                    })
                    ->sortable(),

                \App\Helpers\FilamentImageHelper::makeImageFilenameColumn('image', 'اسم ملف الشعار', 'Logo Filename'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Created At' : 'تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                \App\Helpers\FilamentImageHelper::makeUpdateImageAction('brands', 'image'),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentImageHelper::makeBulkUploadHeaderAction('brands', Brand::class),
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'brands',
                    function (array $row) {
                        $nameAr = $row['name_ar'] ?? ($row['name'] ?? null);
                        if ($nameAr) {
                            Brand::firstOrCreate(
                                ['name_ar' => $nameAr],
                                ['name_en' => $row['name_en'] ?? $nameAr]
                            );
                        }
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'brands',
                    $exportHeaders,
                    $exportRowCallback,
                    Brand::class
                ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'selected_brands',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
