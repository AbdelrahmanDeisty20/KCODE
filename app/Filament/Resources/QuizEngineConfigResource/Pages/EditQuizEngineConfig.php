<?php

namespace App\Filament\Resources\QuizEngineConfigResource\Pages;

use App\Filament\Resources\QuizEngineConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuizEngineConfig extends EditRecord
{
    protected static string $resource = QuizEngineConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
