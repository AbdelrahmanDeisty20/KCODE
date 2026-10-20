<?php

namespace App\Filament\Resources\CareGuideResource\Pages;

use App\Filament\Resources\CareGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCareGuides extends ListRecords
{
    protected static string $resource = CareGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
