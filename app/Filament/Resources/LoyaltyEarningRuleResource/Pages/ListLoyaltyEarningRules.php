<?php

namespace App\Filament\Resources\LoyaltyEarningRuleResource\Pages;

use App\Filament\Resources\LoyaltyEarningRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyEarningRules extends ListRecords
{
    protected static string $resource = LoyaltyEarningRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة قاعدة كسب نقاط'),
        ];
    }
}
