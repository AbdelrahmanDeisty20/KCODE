<?php

namespace App\Filament\Resources\RoutineGoalResource\Pages;

use App\Filament\Resources\RoutineGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoutineGoals extends ListRecords
{
    protected static string $resource = RoutineGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة هدف روتين جديد'),
        ];
    }
}
