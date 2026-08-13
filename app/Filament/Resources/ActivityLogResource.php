<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-activity-logs';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Users & Permissions' : 'إدارة المستخدمين والصلاحيات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Activity Logs' : 'سجلات العمليات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Activity Logs' : 'سجلات العمليات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Activity Log' : 'سجل عملية';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('تفاصيل عملية السجل')
                    ->schema([
                        Forms\Components\TextInput::make('user_name')
                            ->label('اسم المشرف / المستخدم')
                            ->disabled(),

                        Forms\Components\TextInput::make('event')
                            ->label('نوع العملية')
                            ->disabled(),

                        Forms\Components\TextInput::make('subject_type')
                            ->label('نوع السجل / الموديل')
                            ->disabled(),

                        Forms\Components\TextInput::make('subject_id')
                            ->label('معرف السجل (ID)')
                            ->disabled(),

                        Forms\Components\Textarea::make('description')
                            ->label('وصف العملية')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('عنوان الـ IP')
                            ->disabled(),

                        Forms\Components\TextInput::make('created_at')
                            ->label('تاريخ ووقت العملية')
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('متصفح / جهاز المستخدم')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('old_values')
                            ->label('القيم القديمة (قبل التعديل)')
                            ->disabled()
                            ->columnSpan(1),

                        Forms\Components\KeyValue::make('new_values')
                            ->label('القيم الجديدة (بعد التعديل)')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('المشرف / العميل')
                    ->searchable()
                    ->sortable()
                    ->default('زائر / نظام')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('event')
                    ->label('نوع العملية')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'إضافة (Create)',
                        'updated' => 'تعديل (Update)',
                        'deleted' => 'حذف (Delete)',
                        'login'   => 'تسجيل دخول (Login)',
                        'notification_sent' => 'إرسال إشعار',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'login'   => 'primary',
                        'notification_sent' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('نوع السجل')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Product' => 'منتج',
                        'Order' => 'طلب مبيعات',
                        'Coupon' => 'كوبون خصم',
                        'User' => 'مستخدم',
                        'AppNotification' => 'إشعار push',
                        'NewsletterSubscription' => 'نشرة بريدية',
                        'Assessment' => 'اختبار بشرة',
                        default => $state ?? '—',
                    })
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف التفصيلي للعملية')
                    ->searchable()
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('عنوان الـ IP')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ ووقت العملية')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('تصفية بنوع العملية')
                    ->options([
                        'created' => 'إضافة (Create)',
                        'updated' => 'تعديل (Update)',
                        'deleted' => 'حذف (Delete)',
                        'login'   => 'تسجيل دخول (Login)',
                        'notification_sent' => 'إرسال إشعار',
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
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
