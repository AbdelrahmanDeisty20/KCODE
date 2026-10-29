<?php

namespace App\Filament\Resources\ReturnPolicyResource\Pages;

use App\Filament\Resources\ReturnPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReturnPolicys extends ListRecords
{
    protected static string $resource = ReturnPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة بند جديد'),
        ];
    }
}
