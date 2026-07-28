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

    protected static string|UnitEnum|null $navigationGroup = 'محرك التقييم و Quiz البشرة';

    protected static ?string $navigationLabel = 'نتائج الاختبارات (Assessments Log)';

    protected static ?string $pluralModelLabel = 'نتائج الاختبارات';

    protected static ?string $modelLabel = 'نتيجة اختبار';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('بيانات نتيجة الاختبار')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('المستخدم / العميل')
                            ->relationship('user', 'name')
                            ->disabled(),

                        Forms\Components\Select::make('skin_type_id')
                            ->label('نوع البشرة المحدد')
                            ->relationship('skinType', 'name_ar')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('العميل')
                    ->searchable()
                    ->default('زائر غير مسجل'),

                Tables\Columns\TextColumn::make('skinType.name_ar')
                    ->label('نوع البشرة الناتج')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ إجراء الاختبار')
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
