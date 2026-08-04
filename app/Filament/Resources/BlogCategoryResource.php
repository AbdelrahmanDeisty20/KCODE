<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.blog_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.blog_categories');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.navigation.blog_categories');
    }

    public static function getModelLabel(): string
    {
        return __('admin.navigation.blog_categories');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('بيانات قسم المقالات')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم القسم (عربي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم القسم (إنجليزي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر (Slug)')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('image')
                            ->label('صورة القسم')
                            ->image()
                            ->directory('blog-categories')
                            ->disk('public'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label($isEn ? 'Image' : 'الصورة')
                    ->disk('public'),

                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Category Name' : 'اسم القسم')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->name_en ?: $record->name_ar) : ($record->name_ar ?: $record->name_en))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label($isEn ? 'Slug' : 'الرابط المختصر (Slug)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Created At' : 'تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
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
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
