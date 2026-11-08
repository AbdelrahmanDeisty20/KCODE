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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-bell';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Notifications & Messages' : 'إدارة الإشعارات والرسائل';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'App Notifications' : 'إشعارات التطبيق';
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
                Components\Section::make('🎯 قنوات الإرسال وتحديد المستلمين')
                    ->description('حدد وسيلة الإرسال (تطبيق / بريد إلكتروني) والجمهور المستهدف بدقة')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\CheckboxList::make('channels')
                            ->label('قنوات الإرسال')
                            ->options([
                                'app'   => '📱 إشعار فوري داخل التطبيق والموقع (Push & In-App Notification)',
                                'email' => '📧 رسائل بريد إلكتروني (Email Broadcast)',
                            ])
                            ->default(['app'])
                            ->required()
                            ->columns(2)
                            ->columnSpanFull()
                            ->helperText('يمكنك اختيار الإرسال للتطبيق والموقع فقط، أو للإيميل فقط، أو للقناتين معاً.'),

                        Forms\Components\Radio::make('target_audience')
                            ->label('الجمهور المستهدف')
                            ->options([
                                'all_users'     => '👥 جميع المستخدمين والعملاء المسجلين (All Users)',
                                'specific_user' => '👤 مستخدم / عميل محدد (Specific User)',
                                'newsletter'    => '💌 مشتركو النشرة البريدية فقط (Newsletter Subscribers)',
                                'all_everyone'  => '🌐 الجميع (المستخدمين المسجلين + مشتركي النشرة البريدية)',
                            ])
                            ->default('all_users')
                            ->reactive()
                            ->columns(2)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('user_id')
                            ->label('اختر العميل المستهدف 👤')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('target_audience') === 'specific_user')
                            ->required(fn ($get) => $get('target_audience') === 'specific_user')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('📝 محتوى وبيانات الإشعار')
                    ->description('العنوان والرسالة والروابط التفاعلية الموجهة للعملاء')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label('عنوان الإشعار (بالعربية)')
                            ->placeholder('مثال: عرض خاص 20% خصم لفترة محدودة 🔥')
                            ->required(),

                        Forms\Components\TextInput::make('title_en')
                            ->label('Notification Title (English)')
                            ->placeholder('e.g. Special 20% Discount Offer 🔥'),

                        Forms\Components\Textarea::make('message_ar')
                            ->label('نص الإشعار والرسالة (بالعربية)')
                            ->placeholder('اكتب تفاصيل الإشعار أو نص الرسالة هنا...')
                            ->rows(3)
                            ->required(),

                        Forms\Components\Textarea::make('message_en')
                            ->label('Notification Body (English)')
                            ->placeholder('Type notification details here...')
                            ->rows(3),

                        Forms\Components\TextInput::make('action_url')
                            ->label('رابط التوجيه / الزر (اختياري)')
                            ->placeholder('مثال: /offers أو https://kcodeskin.com/products/123')
                            ->helperText('في حال إرسال إيميل أو إشعار، سيظهر زر تفاعلي ينقل العميل لهذا الرابط')
                            ->columnSpan(1),

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
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_read')
                            ->label('تم القراءة (Is Read)')
                            ->default(false)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $exportHeaders = ['المعرف', 'الجمهور', 'المستلم', 'العنوان بالعربي', 'نص الإشعار بالعربي', 'النوع', 'مقروء', 'تاريخ الإرسال'];
        $exportRowCallback = fn ($record) => [
            $record->id,
            $record->target_audience ?? 'all_users',
            $record->user_id ? ($record->user?->name ?? 'مستخدم') : 'جميع المستخدمين',
            $record->title_ar,
            $record->message_ar,
            $record->type,
            $record->is_read ? 'نعم' : 'لا',
            $record->created_at?->format('Y-m-d H:i') ?? '',
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('target_audience')
                    ->label('الجمهور المستهدف')
                    ->formatStateUsing(function ($state, $record) {
                        return match ($state) {
                            'all_users'     => '👥 كل المسجلين',
                            'specific_user' => '👤 ' . ($record->user?->name ?? 'مستخدم محدد'),
                            'newsletter'    => '💌 مشتركو النشرة',
                            'all_everyone'  => '🌐 الجميع (الكل)',
                            default         => $record->user_id ? ('👤 ' . ($record->user?->name ?? 'مستخدم')) : '👥 كل المسجلين',
                        };
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        return match ($state) {
                            'all_users'     => 'info',
                            'specific_user' => 'gray',
                            'newsletter'    => 'warning',
                            'all_everyone'  => 'success',
                            default         => $record->user_id ? 'gray' : 'info',
                        };
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('channels')
                    ->label('قنوات الإرسال')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '📱 تطبيق وموقع';
                        }
                        $labels = [];
                        if (in_array('app', (array) $state)) {
                            $labels[] = '📱 تطبيق';
                        }
                        if (in_array('email', (array) $state)) {
                            $labels[] = '📧 إيميل';
                        }
                        return implode(' + ', $labels);
                    })
                    ->badge()
                    ->color('secondary'),

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
                Tables\Filters\SelectFilter::make('target_audience')
                    ->label('الجمهور المستهدف')
                    ->options([
                        'all_users'     => '👥 جميع المستخدمين',
                        'specific_user' => '👤 مستخدم محدد',
                        'newsletter'    => '💌 مشتركو النشرة',
                        'all_everyone'  => '🌐 الجميع (الكل)',
                    ]),

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
                    ->label('عرض التفاصيل')
                    ->modalWidth('3xl')
                    ->modalHeading('عرض تفاصيل الإشعار 📣'),

                Actions\Action::make('toggle_read')
                    ->label(fn ($record) => $record->is_read ? 'تحديد كغير مقروء' : 'تحديد كمقروء')
                    ->icon(fn ($record) => $record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->color(fn ($record) => $record->is_read ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_read' => !$record->is_read]);
                    }),

                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'app_notifications',
                    $exportHeaders,
                    $exportRowCallback,
                    AppNotification::class
                ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('mark_as_read')
                        ->label('تحديد المقروء للكل')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true])),

                    Actions\DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'selected_app_notifications',
                        $exportHeaders,
                        $exportRowCallback
                    ),
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
