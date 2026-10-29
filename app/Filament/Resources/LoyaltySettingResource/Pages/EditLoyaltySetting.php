<?php

namespace App\Filament\Resources\LoyaltySettingResource\Pages;

use App\Filament\Resources\LoyaltySettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltySetting extends EditRecord
{
    protected static string $resource = LoyaltySettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
