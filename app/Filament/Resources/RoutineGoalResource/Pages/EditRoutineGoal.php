<?php

namespace App\Filament\Resources\RoutineGoalResource\Pages;

use App\Filament\Resources\RoutineGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoutineGoal extends EditRecord
{
    protected static string $resource = RoutineGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
