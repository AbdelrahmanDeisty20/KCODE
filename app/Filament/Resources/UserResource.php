<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-users';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Users & Permissions' : 'إدارة المستخدمين والصلاحيات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Users & Customers' : 'المستخدمون والعملاء';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Users & Customers' : 'المستخدمون والعملاء';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'User' : 'مستخدم';
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
                Components\Section::make('👤 بيانات الملف الشخصي والحساب')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم بالكامل')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف'),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('تاريخ الميلاد'),

                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة الشخصية')
                            ->image()
                            ->directory('users')
                            ->formatStateUsing(fn ($state) => $state ? (str_starts_with($state, 'users/') ? $state : 'users/' . ltrim($state, '/')) : null),
                    ])->columns(2),

                Components\Section::make('🔐 الصلاحيات والأدوار والنظام')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('نوع الحساب / الصلاحية')
                            ->options([
                                'admin' => 'مدير النظام (Admin)',
                                'user' => 'عميل (Customer)',
                            ])
                            ->default('user')
                            ->required(),

                        Forms\Components\Select::make('roles')
                            ->label('أدوار المستخدم (Roles)')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة السر')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->state(fn ($record) => $record->image_path)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->colors([
                        'danger' => 'admin',
                        'primary' => 'user',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'مدير',
                        'user' => 'عميل',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('الأدوار')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع الحساب')
                    ->options([
                        'admin' => 'مدير',
                        'user' => 'عميل',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
