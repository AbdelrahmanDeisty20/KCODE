<?php

namespace App\Filament\Resources\CouponPolicyResource\Pages;

use App\Filament\Resources\CouponPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCouponPolicys extends ListRecords
{
    protected static string $resource = CouponPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة بند جديد'),
        ];
    }
}
