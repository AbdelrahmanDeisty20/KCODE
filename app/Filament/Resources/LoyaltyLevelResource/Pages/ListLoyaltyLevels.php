<?php

namespace App\Filament\Resources\LoyaltyLevelResource\Pages;

use App\Filament\Resources\LoyaltyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyLevels extends ListRecords
{
    protected static string $resource = LoyaltyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة مستوى جديد'),
        ];
    }
}
