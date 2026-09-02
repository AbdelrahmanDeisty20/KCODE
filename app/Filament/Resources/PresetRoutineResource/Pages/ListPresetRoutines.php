<?php

namespace App\Filament\Resources\PresetRoutineResource\Pages;

use App\Filament\Resources\PresetRoutineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresetRoutines extends ListRecords
{
    protected static string $resource = PresetRoutineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة روتين جاهز جديد'),
        ];
    }
}
