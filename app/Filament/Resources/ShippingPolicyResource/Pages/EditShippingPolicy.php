<?php

namespace App\Filament\Resources\ShippingPolicyResource\Pages;

use App\Filament\Resources\ShippingPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippingPolicy extends EditRecord
{
    protected static string $resource = ShippingPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
