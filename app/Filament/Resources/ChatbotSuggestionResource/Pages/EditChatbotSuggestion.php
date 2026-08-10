<?php

namespace App\Filament\Resources\ChatbotSuggestionResource\Pages;

use App\Filament\Resources\ChatbotSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatbotSuggestion extends EditRecord
{
    protected static string $resource = ChatbotSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
