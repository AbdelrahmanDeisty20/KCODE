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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?int $navigationSort = 4;

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
        $count = static::getModel()::count();
        return $count > 0 ? number_format($count) : null;
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
                        'created'           => 'إضافة (Create)',
                        'updated'           => 'تعديل (Update)',
                        'deleted'           => 'حذف (Delete)',
                        'login'             => 'تسجيل دخول 🟢',
                        'logout'            => 'تسجيل خروج ⚪',
                        'favourite_added'   => 'إضافة للمفضلة ❤️',
                        'favourite_removed' => 'إزالة من المفضلة 💔',
                        'cart_added'        => 'إضافة للسلة 🛒',
                        'cart_updated'      => 'تعديل السلة 🔄',
                        'cart_removed'      => 'حذف من السلة 🗑️',
                        'cart_cleared'      => 'إفراغ السلة 🧹',
                        'address_added'     => 'إضافة عنوان 📍',
                        'address_updated'   => 'تعديل عنوان ✏️',
                        'address_deleted'   => 'حذف عنوان 🗑️',
                        'product_view'      => 'مشاهدة منتج 👁️',
                        'site_visit'        => 'زيارة للمتجر 🌐',
                        'offer_created'     => 'إضافة عرض 🏷️',
                        'offer_updated'     => 'تعديل عرض ✏️',
                        'offer_expired'     => 'انتهاء عرض ⏳',
                        'offer_deleted'     => 'حذف عرض 🗑️',
                        'coupon_applied'    => 'تطبيق كوبون 🎁',
                        'quiz_completed'    => 'تحليل البشرة 🧬',
                        'search'            => 'بحث بالمتجر 🔍',
                        'notification_sent' => 'إرسال إشعار 🔔',
                        default             => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created', 'login', 'address_added', 'coupon_applied', 'offer_created' => 'success',
                        'updated', 'cart_updated', 'address_updated', 'offer_updated', 'product_view' => 'info',
                        'deleted', 'favourite_removed', 'cart_removed', 'cart_cleared', 'address_deleted', 'offer_expired', 'offer_deleted' => 'danger',
                        'favourite_added'   => 'danger',
                        'cart_added'        => 'warning',
                        'notification_sent' => 'warning',
                        'site_visit', 'quiz_completed' => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('نوع السجل')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'Product'                => 'منتج',
                        'Order'                  => 'طلب مبيعات',
                        'Cart'                   => 'سلة التسوق',
                        'Favourite'              => 'المفضلة',
                        'Address'                => 'عنوان شحن',
                        'Offer'                  => 'عرض ترويجي',
                        'Coupon'                 => 'كوبون خصم',
                        'User'                   => 'مستخدم / عميل',
                        'Assessment'             => 'تحليل بشرة',
                        'Review'                 => 'تقييم منتج',
                        'Search'                 => 'بحث',
                        'Site'                   => 'المتجر',
                        'Blog'                   => 'مقال مدونة',
                        'Category'               => 'قسم رئيسي',
                        'SubCategory'            => 'قسم فرعي',
                        'Brand'                  => 'علامة تجارية',
                        'AppNotification'        => 'إشعار push',
                        'NewsletterSubscription' => 'نشرة بريدية',
                        default                  => $state ?? '—',
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
                        'login'             => 'تسجيل دخول (Login)',
                        'logout'            => 'تسجيل خروج (Logout)',
                        'cart_added'        => 'إضافة للسلة (Cart)',
                        'cart_updated'      => 'تعديل السلة',
                        'cart_removed'      => 'حذف من السلة',
                        'favourite_added'   => 'إضافة للمفضلة (Wishlist)',
                        'favourite_removed' => 'إزالة من المفضلة',
                        'address_added'     => 'إضافة عنوان شحن',
                        'address_updated'   => 'تعديل عنوان شحن',
                        'address_deleted'   => 'حذف عنوان شحن',
                        'product_view'      => 'مشاهدة منتج (View)',
                        'site_visit'        => 'زيارة للمتجر (Visit)',
                        'quiz_completed'    => 'تحليل البشرة (Quiz)',
                        'coupon_applied'    => 'تطبيق كوبون خصم',
                        'offer_expired'     => 'انتهاء عرض ترويجي',
                        'offer_created'     => 'إضافة عرض ترويجي',
                        'search'            => 'بحث بالمتجر',
                        'created'           => 'إضافة سجل (Create)',
                        'updated'           => 'تعديل سجل (Update)',
                        'deleted'           => 'حذف سجل (Delete)',
                    ]),

                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('تصفية بنوع السجل')
                    ->options([
                        'User'       => 'مستخدم / عميل',
                        'Product'    => 'منتج',
                        'Cart'       => 'سلة التسوق',
                        'Favourite'  => 'المفضلة',
                        'Address'    => 'عنوان شحن',
                        'Order'      => 'طلب مبيعات',
                        'Offer'      => 'عرض ترويجي',
                        'Coupon'     => 'كوبون خصم',
                        'Assessment' => 'تحليل بشرة (Quiz)',
                        'Review'     => 'تقييم منتج',
                        'Search'     => 'بحث',
                        'Site'       => 'زيارة متجر',
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
