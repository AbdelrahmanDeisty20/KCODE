<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Models\Assessment;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Assessments Log' : 'نتائج الاختبارات';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Assessments Log' : 'نتائج الاختبارات';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Assessment' : 'نتيجة اختبار';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('بيانات نتيجة الاختبار')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(app()->getLocale() === 'en' ? 'User / Customer' : 'المستخدم / العميل')
                            ->relationship('user', 'name')
                            ->disabled(),

                        Forms\Components\Select::make('skin_type_id')
                            ->label(app()->getLocale() === 'en' ? 'Resulting Skin Type' : 'نوع البشرة المحدد')
                            ->relationship('skinType', app()->getLocale() === 'en' ? 'name_en' : 'name_ar')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isEn = app()->getLocale() === 'en';

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label($isEn ? 'Customer' : 'العميل')
                    ->searchable()
                    ->default($isEn ? 'Unregistered Guest' : 'زائر غير مسجل'),

                Tables\Columns\TextColumn::make('skinType')
                    ->label($isEn ? 'Resulting Skin Type' : 'نوع البشرة الناتج')
                    ->formatStateUsing(fn ($record) => $isEn ? ($record->skinType?->name_en ?: $record->skinType?->name_ar) : ($record->skinType?->name_ar ?: $record->skinType?->name_en))
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isEn ? 'Assessment Date' : 'تاريخ إجراء الاختبار')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
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
            'index' => Pages\ListAssessments::route('/'),
        ];
    }
}
