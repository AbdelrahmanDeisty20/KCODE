<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppNotificationResource\Pages;
use App\Models\AppNotification;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppNotificationResource extends Resource
{
    protected static ?string $model = AppNotification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Notifications & Messages' : 'إدارة الإشعارات والرسائل';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'App Notifications' : 'إشعارات التطبيق (Push)';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'App Notifications' : 'إشعارات التطبيق';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'App Notification' : 'إشعار';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('📣 إرسال إشعار جديد للمستخدمين')
                    ->description('قم بتحديد الجمهور الفئة المستهدفة ثم كتابة عنوان ونص الإشعار.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('المستلم / المستخدم المستهدف')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('📣 جميع المستخدمين والأجهزة (إرسال عام لكل التوكينات)')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان الإشعار (بالعربية)')
                            ->placeholder('مثال: عرض خاص 20% خصم لفترة محدودة 🔥')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('Notification Title (English)')
                            ->placeholder('e.g. Special 20% Discount Offer 🔥'),

                        Forms\Components\Textarea::make('message_ar')
                            ->label('نص الإشعار (بالعربية)')
                            ->placeholder('اكتب نص الإشعار هنا...')
                            ->rows(3)
                            ->required(),

                        Forms\Components\Textarea::make('message_en')
                            ->label('Notification Body (English)')
                            ->placeholder('Type notification details here...')
                            ->rows(3),

                        Forms\Components\Select::make('type')
                            ->label('نوع الإشعار')
                            ->options([
                                'general' => 'عام (General)',
                                'promotion' => 'عرض ترويجي (Promotion)',
                                'order' => 'تحديث طلب (Order Update)',
                            ])
                            ->default('general')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستلم')
                    ->default(fn ($record) => $record->user_id ? $record->user?->name : '📣 جميع المستخدمين')
                    ->searchable()
                    ->badge()
                    ->color(fn ($record) => $record->user_id ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('title_ar')
                    ->label('العنوان')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('message_ar')
                    ->label('نص الإشعار')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->colors([
                        'primary' => 'general',
                        'success' => 'promotion',
                        'warning' => 'order',
                    ]),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('مقروء')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع الإشعار')
                    ->options([
                        'general' => 'عام',
                        'promotion' => 'عرض ترويجي',
                        'order' => 'تحديث طلب',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
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
            'index' => Pages\ListAppNotifications::route('/'),
            'create' => Pages\CreateAppNotification::route('/create'),
        ];
    }
}
