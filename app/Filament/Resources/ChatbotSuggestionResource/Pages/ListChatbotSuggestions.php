<?php

namespace App\Filament\Resources\ChatbotSuggestionResource\Pages;

use App\Filament\Resources\ChatbotSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatbotSuggestions extends ListRecords
{
    protected static string $resource = ChatbotSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
