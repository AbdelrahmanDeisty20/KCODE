<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Sales & Orders' : 'إدارة المبيعات والطلبات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Discount Coupons' : 'كوبونات الخصم';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Discount Coupons' : 'كوبونات الخصم';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Discount Coupon' : 'كوبون خصم';
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
                Components\Section::make('🎫 بيانات وكود الكوبون')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('كود الكوبون')
                            ->default(fn () => 'KCODE-' . strtoupper(Str::random(6)))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->suffixAction(
                                Actions\Action::make('generate_code')
                                    ->label('توليد كود عشوائي')
                                    ->icon('heroicon-o-arrow-path')
                                    ->action(function ($set) {
                                        $set('code', 'KCODE-' . strtoupper(Str::random(6)));
                                    })
                            ),

                        Forms\Components\TextInput::make('title_ar')
                            ->label('العنوان بالعربية')
                            ->placeholder('مثال: خصم بمناسبة الافتتاح'),

                        Forms\Components\TextInput::make('title_en')
                            ->label('العنوان بالإنجليزية')
                            ->placeholder('Example: Opening Discount'),
                    ])->columns(3),

                Components\Section::make('👥 تخصيص المستهدفين بالخصم')
                    ->schema([
                        Forms\Components\Radio::make('target_type')
                            ->label('نطاق تخصيص الكوبون')
                            ->options([
                                'general'  => '🌐 عام (لكل المستخدمين / جميع العملاء)',
                                'specific' => '🎯 مخصص لمستخدمين محددين (توليد كوبون مستقل لكل مستخدم)',
                            ])
                            ->default('general')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('target_user_ids')
                            ->label('اختر المستخدمين المستهدفين')
                            ->options(function () {
                                return User::all()->mapWithKeys(function ($user) {
                                    $label = $user->name . ($user->email ? " ({$user->email})" : '');
                                    return [$user->id => $label];
                                });
                            })
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('target_type') === 'specific')
                            ->required(fn ($get) => $get('target_type') === 'specific')
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('💰 تفاصيل وشروط الخصم')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('نوع الخصم')
                            ->options([
                                'fixed'      => 'مبلغ ثابت (EGP)',
                                'percentage' => 'نسبة مئوية (%)',
                            ])
                            ->default('percentage')
                            ->required(),

                        Forms\Components\TextInput::make('discount_value')
                            ->label('قيمة الخصم')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('min_order_amount')
                            ->label('الحد الأدنى لقيمة الطلب')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\TextInput::make('max_discount_amount')
                            ->label('الحد الأقصى للخصم (لـ %) ')
                            ->numeric()
                            ->prefix('EGP'),

                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('تاريخ البداية'),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('تاريخ النهاية'),

                        Forms\Components\TextInput::make('usage_limit')
                            ->label('الحد الأقصى لاستخدام الكوبون')
                            ->numeric(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('تفعيل الكوبون')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label($isEn ? 'Code' : 'كود الكوبون')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'Target User' : 'المستهدف')
                    ->default(fn ($record) => $record->is_general || is_null($record->user_id) ? '🌐 عام للجميع' : ($record->user?->name ?? 'مستخدم خاص'))
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => str_contains($state, 'عام'),
                        'info'    => fn ($state) => !str_contains($state, 'عام'),
                    ]),

                Tables\Columns\TextColumn::make('discount_type')
                    ->label($isEn ? 'Type' : 'نوع الخصم')
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'نسبة %' : 'مبلغ ثابت'),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label($isEn ? 'Value' : 'القيمة')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage' ? "{$state}%" : "{$state} EGP"),

                Tables\Columns\TextColumn::make('used_count')
                    ->label($isEn ? 'Times Used' : 'عدد الاستخدامات')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label($isEn ? 'Active' : 'مفعل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label($isEn ? 'End Date' : 'تاريخ الانتهاء')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->placeholder('دائم'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_general')
                    ->label('نطاق الكوبون')
                    ->placeholder('جميع الكوبونات')
                    ->trueLabel('كوبونات عامة')
                    ->falseLabel('كوبونات مخصصة'),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
