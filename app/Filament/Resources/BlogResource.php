<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.blog_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.blogs');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.navigation.blogs');
    }

    public static function getModelLabel(): string
    {
        return __('admin.navigation.blogs');
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
                Components\Section::make('معلومات المقال الرئيسية')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('اسم المقال (عربي)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم المقال (إنجليزي)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان المقال (عربي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title_en')
                            ->label('عنوان المقال (إنجليزي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط المختصر (Slug)')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('قسم المقال')
                            ->relationship('category', 'name_ar')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('author_id')
                            ->label('كاتب المقال (Author)')
                            ->relationship('author', 'name')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->label('حالة المقال')
                            ->options([
                                'draft' => 'مسودة (Draft)',
                                'published' => 'منشور (Published)',
                            ])
                            ->default('published')
                            ->required(),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('مقال مميز (Featured)')
                            ->default(false),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('تاريخ النشر'),

                        Forms\Components\FileUpload::make('featured_image')
                            ->label('الصورة البارزة')
                            ->image()
                            ->directory('blogs')
                            ->disk('public'),
                    ])->columns(2),

                Components\Section::make('ملخص ومحتوى المقال')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt_ar')
                            ->label('ملخص المقال (عربي)')
                            ->rows(3)
                            ->required(),

                        Forms\Components\Textarea::make('excerpt_en')
                            ->label('ملخص المقال (إنجليزي)')
                            ->rows(3)
                            ->required(),

                        Forms\Components\RichEditor::make('content_ar')
                            ->label('محتوى المقال (عربي)')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content_en')
                            ->label('محتوى المقال (إنجليزي)')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label($isEn ? 'Image' : 'الصورة')
                    ->disk('public'),

                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Name' : 'الاسم')
                    ->getStateUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('title')
                    ->label($isEn ? 'Article Title' : 'عنوان المقال')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->title_en ?: $record->title_ar) : ($record->title_ar ?: $record->title_en))
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category')
                    ->label($isEn ? 'Category' : 'القسم')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->category?->name_en ?: $record->category?->name_ar) : ($record->category?->name_ar ?: $record->category?->name_en))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label($isEn ? 'Status' : 'الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => $isEn ? 'Published' : 'منشور',
                        'draft' => $isEn ? 'Draft' : 'مسودة',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label($isEn ? 'Featured' : 'مميز')
                    ->boolean(),

                Tables\Columns\TextColumn::make('views')
                    ->label('المشاهدات')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'published' => 'منشور',
                        'draft' => 'مسودة',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('المقالات المميزة'),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
