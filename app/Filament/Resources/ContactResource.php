<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Customer Support' : 'دعم العملاء والتواصل';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Contact Messages' : 'رسائل التواصل';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Contact Messages' : 'رسائل التواصل';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Contact Message' : 'رسالة تواصل';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل رسالة التواصل')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان الرسالة')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('حالة الرسالة')
                            ->options([
                                'pending' => 'معلقة (قيد الانتظار)',
                                'read'    => 'تم الاطلاع (مقروءة)',
                                'replied' => 'تم الرد',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\Textarea::make('message')
                            ->label('نص الرسالة')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان الرسالة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'read'    => 'info',
                        'replied' => 'success',
                        default   => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'معلقة',
                        'read'    => 'مقروءة',
                        'replied' => 'تم الرد',
                        default   => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
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
            'index' => Pages\ListContacts::route('/'),
            'edit'  => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
