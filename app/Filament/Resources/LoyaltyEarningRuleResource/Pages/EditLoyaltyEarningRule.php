<?php

namespace App\Filament\Resources\LoyaltyEarningRuleResource\Pages;

use App\Filament\Resources\LoyaltyEarningRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyEarningRule extends EditRecord
{
    protected static string $resource = LoyaltyEarningRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
