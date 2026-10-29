<?php

namespace App\Filament\Resources\CouponPolicyResource\Pages;

use App\Filament\Resources\CouponPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCouponPolicy extends EditRecord
{
    protected static string $resource = CouponPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
