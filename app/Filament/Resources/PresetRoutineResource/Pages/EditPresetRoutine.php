<?php

namespace App\Filament\Resources\PresetRoutineResource\Pages;

use App\Filament\Resources\PresetRoutineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPresetRoutine extends EditRecord
{
    protected static string $resource = PresetRoutineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
