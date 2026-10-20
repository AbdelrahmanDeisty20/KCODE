<?php

namespace App\Filament\Resources\CareGuideResource\Pages;

use App\Filament\Resources\CareGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCareGuide extends EditRecord
{
    protected static string $resource = CareGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
