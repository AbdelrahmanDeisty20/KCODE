<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductSelectionCriteriaResource\Pages;
use App\Models\ProductSelectionCriteria;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductSelectionCriteriaResource extends Resource
{
    protected static ?string $model = ProductSelectionCriteria::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Content & Static Pages' : 'المحتوى والصفحات التعريفية';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product Selection Criteria' : 'معايير اختيار المنتجات (ميثاق الجودة)';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product Selection Criteria' : 'معايير اختيار المنتجات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product Selection Criterion' : 'معيار اختيار المنتج';
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
                Components\Section::make('بيانات معيار اختيار المنتج')
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label('العنوان (عربي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title_en')
                            ->label('العنوان (إنجليزي)')
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('نوع المعيار/العنصر')
                            ->options([
                                'modal_criteria'  => 'معيار في مودال منهجية الفحص والتقييم',
                                'accordion_item'  => 'بند في أجزاء "لماذا KCODE" (Accordion)',
                            ])
                            ->required()
                            ->default('modal_criteria'),

                        Forms\Components\TextInput::make('icon')
                            ->label('الأيقونة / الرمز (مثال: shield-check, flask, ban)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('badge_text_ar')
                            ->label('الشارة التوضيحية (عربي)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('badge_text_en')
                            ->label('الشارة التوضيحية (إنجليزي)')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف التفصيلي (عربي)')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description_en')
                            ->label('الوصف التفصيلي (إنجليزي)')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مُفعل')
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label($isEn ? 'Title' : 'العنوان')
                    ->getStateUsing(fn ($record) => $isEn ? ($record->title_en ?: $record->title_ar) : ($record->title_ar ?: $record->title_en))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'modal_criteria' => 'success',
                        'accordion_item' => 'info',
                        default          => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'modal_criteria' => 'معيار مودال',
                        'accordion_item' => 'بند أكورديون',
                        default          => $state,
                    }),

                Tables\Columns\TextColumn::make('icon')
                    ->label('الأيقونة'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('مُفعل')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
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
            'index'  => Pages\ListProductSelectionCriterias::route('/'),
            'create' => Pages\CreateProductSelectionCriteria::route('/create'),
            'edit'   => Pages\EditProductSelectionCriteria::route('/{record}/edit'),
        ];
    }
}
