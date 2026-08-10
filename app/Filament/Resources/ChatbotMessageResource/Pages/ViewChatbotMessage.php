<?php

namespace App\Filament\Resources\ChatbotMessageResource\Pages;

use App\Filament\Resources\ChatbotMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChatbotMessage extends ViewRecord
{
    protected static string $resource = ChatbotMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
