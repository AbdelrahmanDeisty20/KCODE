<?php

namespace App\Filament\Resources\LoyaltyLevelResource\Pages;

use App\Filament\Resources\LoyaltyLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyLevel extends EditRecord
{
    protected static string $resource = LoyaltyLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
