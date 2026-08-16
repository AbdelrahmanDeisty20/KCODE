<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogAuthorResource\Pages;
use App\Helpers\FilamentExportHelper;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class BlogAuthorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-blog-authors';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Users & Permissions' : 'إدارة المستخدمين والصلاحيات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Blog Authors' : 'كُتّاب المقالات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Blog Authors' : 'كُتّاب المقالات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Blog Author' : 'كاتب مقالات';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($query) {
                $query->where('type', 'blog_author')
                    ->orWhereHas('roles', fn ($q) => $q->whereIn('name', ['blog_author', 'Blog Author', 'writer', 'Writer']))
                    ->orWhereHas('blogs');
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('📝 بيانات كاتب المقالات')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الكاتب بالكامل')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف'),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة السر')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),

                        Forms\Components\FileUpload::make('image')
                            ->label('صورة الكاتب (Avatar)')
                            ->image()
                            ->directory('users')
                            ->formatStateUsing(fn ($state) => $state ? (str_starts_with($state, 'users/') ? $state : 'users/' . ltrim($state, '/')) : null),

                        Forms\Components\Textarea::make('quote')
                            ->label('السيرة الذاتية / نبذة عن الكاتب')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        $exportHeaders = ['المعرف', 'اسم الكاتب', 'البريد الإلكتروني', 'رقم الهاتف', 'عدد المقالات المنشورة', 'تاريخ الإنضمام'];
        $exportRowCallback = fn ($record) => [
            $record->id,
            $record->name,
            $record->email,
            $record->phone ?? '—',
            $record->blogs_count ?? $record->blogs()->count(),
            $record->created_at?->format('Y-m-d H:i') ?? '',
        ];

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label($isEn ? 'Avatar' : 'الصورة')
                    ->state(fn ($record) => $record->image_path)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label($isEn ? 'Author Name' : 'اسم الكاتب')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label($isEn ? 'Email' : 'البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label($isEn ? 'Phone' : 'الهاتف')
                    ->default('—'),

                Tables\Columns\TextColumn::make('blogs_count')
                    ->label($isEn ? 'Articles Count' : 'عدد المقالات')
                    ->counts('blogs')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Joined At' : 'تاريخ الإنضمام')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                FilamentExportHelper::makeImportHeaderAction(
                    'blog_authors',
                    function (array $row) {
                        $email = $row['email'] ?? null;
                        if ($email) {
                            User::updateOrCreate(
                                ['email' => $email],
                                [
                                    'name'     => $row['name'] ?? ($row['full_name'] ?? 'كاتب جديد'),
                                    'phone'    => $row['phone'] ?? null,
                                    'type'     => 'blog_author',
                                    'password' => Hash::make($row['password'] ?? '12345678'),
                                ]
                            );
                        }
                    }
                ),
                FilamentExportHelper::makeExportHeaderAction(
                    'blog_authors',
                    $exportHeaders,
                    $exportRowCallback,
                    User::class
                ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    FilamentExportHelper::makeExportBulkAction(
                        'selected_blog_authors',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogAuthors::route('/'),
            'create' => Pages\CreateBlogAuthor::route('/create'),
            'view'   => Pages\ViewBlogAuthor::route('/{record}'),
            'edit'   => Pages\EditBlogAuthor::route('/{record}/edit'),
        ];
    }
}
