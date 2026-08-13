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
use Illuminate\Database\Eloquent\Collection;

class AppNotificationResource extends Resource
{
    protected static ?string $model = AppNotification::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-paper-plane';

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
                Components\Section::make('📣 تفاصيل وإعدادات الإشعار')
                    ->description('عرض وإنشاء بيانات الإشعار اللحظي')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('المستلم / المستخدم المستهدف')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('📣 جميع المستخدمين والأجهزة (إرسال عام لكل التوكينات)'),

                        Forms\Components\Toggle::make('is_read')
                            ->label('تم القراءة (Is Read)')
                            ->default(false),

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
                                'general'        => 'عام (General)',
                                'promotion'      => 'عرض ترويجي (Promotion)',
                                'order'          => 'تحديث طلب (Order Update)',
                                'general_coupon' => 'كوبون عام (General Coupon)',
                                'private_coupon' => 'كوبون خاص (Private Coupon)',
                                'blog'           => 'مقالة مدونة (Blog)',
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
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->colors([
                        'primary'   => 'general',
                        'success'   => 'promotion',
                        'warning'   => 'order',
                        'info'      => 'general_coupon',
                        'gray'      => 'private_coupon',
                    ]),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('مقروء')
                    ->boolean()
                    ->sortable(),

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
                        'general'        => 'عام',
                        'promotion'      => 'عرض ترويجي',
                        'order'          => 'تحديث طلب',
                        'general_coupon' => 'كوبون عام',
                        'private_coupon' => 'كوبون خاص',
                    ]),

                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('حالة القراءة')
                    ->placeholder('الكل')
                    ->trueLabel('المقروءة فقط')
                    ->falseLabel('غير المقروءة فقط'),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->label('عرض التفاصيل'),

                Tables\Actions\Action::make('toggle_read')
                    ->label(fn ($record) => $record->is_read ? 'تحديد كغير مقروء' : 'تحديد كمقروء')
                    ->icon(fn ($record) => $record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->color(fn ($record) => $record->is_read ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_read' => !$record->is_read]);
                    }),

                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->label('تحديد المحددة كمقروءة')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true])),

                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAppNotifications::route('/'),
            'create' => Pages\CreateAppNotification::route('/create'),
        ];
    }
}
