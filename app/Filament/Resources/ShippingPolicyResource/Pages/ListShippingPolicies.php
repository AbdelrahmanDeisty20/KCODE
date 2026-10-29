<?php

namespace App\Filament\Resources\ShippingPolicyResource\Pages;

use App\Filament\Resources\ShippingPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingPolicies extends ListRecords
{
    protected static string $resource = ShippingPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة سياسة شحن'),
        ];
    }
}
