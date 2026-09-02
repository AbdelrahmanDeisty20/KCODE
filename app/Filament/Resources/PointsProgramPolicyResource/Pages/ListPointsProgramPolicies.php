<?php

namespace App\Filament\Resources\PointsProgramPolicyResource\Pages;

use App\Filament\Resources\PointsProgramPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointsProgramPolicies extends ListRecords
{
    protected static string $resource = PointsProgramPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة بنود سياسة النقاط'),
        ];
    }
}
