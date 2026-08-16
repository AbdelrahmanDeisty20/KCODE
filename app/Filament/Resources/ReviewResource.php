<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Helpers\FilamentExportHelper;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = 'icon-reviews';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Products & Reviews' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product Reviews' : 'تقييمات وآراء المنتجات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product Reviews' : 'تقييمات المنتجات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Review' : 'تقييم';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('⭐ تفاصيل تقييم المنتج')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('المنتج')
                            ->options(Product::pluck('name_ar', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label('العميل / صاحب التقييم')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('rating')
                            ->label('التقييم (النجوم)')
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (5/5 ممتاز)',
                                4 => '⭐⭐⭐⭐ (4/5 جيد جداً)',
                                3 => '⭐⭐⭐ (3/5 متوسط)',
                                2 => '⭐⭐ (2/5 مقبول)',
                                1 => '⭐ (1/5 سيء)',
                            ])
                            ->default(5)
                            ->required(),

                        Forms\Components\Textarea::make('comment')
                            ->label('تعليق ورأي العميل')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        $exportHeaders = ['المعرف', 'اسم المنتج', 'اسم العميل', 'التقييم', 'التعليق', 'التاريخ'];
        $exportRowCallback = fn ($record) => [
            $record->id,
            $record->product?->name_ar ?? '—',
            $record->user?->name ?? 'عميل',
            $record->rating . '/5',
            $record->comment ?? '',
            $record->created_at?->format('Y-m-d H:i') ?? '',
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name_ar')
                    ->label($isEn ? 'Product' : 'المنتج')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),

                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'Customer' : 'العميل')
                    ->searchable()
                    ->sortable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('rating')
                    ->label($isEn ? 'Rating' : 'التقييم')
                    ->state(fn ($record) => str_repeat('⭐', $record->rating) . " ({$record->rating}/5)")
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->rating >= 4 => 'success',
                        $record->rating == 3 => 'warning',
                        default              => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label($isEn ? 'Comment' : 'الرأي / التعليق')
                    ->limit(50)
                    ->searchable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Date' : 'التاريخ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('التقييم بالنجوم')
                    ->options([
                        5 => '5 نجوم ⭐⭐⭐⭐⭐',
                        4 => '4 نجوم ⭐⭐⭐⭐',
                        3 => '3 نجوم ⭐⭐⭐',
                        2 => 'نجمتان ⭐⭐',
                        1 => 'نجمة واحدة ⭐',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                FilamentExportHelper::makeImportHeaderAction(
                    'product_reviews',
                    function (array $row) {
                        $productId = $row['product_id'] ?? null;
                        $userId = $row['user_id'] ?? 1;
                        if ($productId) {
                            Review::create([
                                'product_id' => $productId,
                                'user_id'    => $userId,
                                'rating'     => $row['rating'] ?? 5,
                                'comment'    => $row['comment'] ?? null,
                            ]);
                        }
                    }
                ),
                FilamentExportHelper::makeExportHeaderAction(
                    'product_reviews',
                    $exportHeaders,
                    $exportRowCallback,
                    Review::class
                ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    FilamentExportHelper::makeExportBulkAction(
                        'selected_product_reviews',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view'   => Pages\ViewReview::route('/{record}'),
            'edit'   => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
