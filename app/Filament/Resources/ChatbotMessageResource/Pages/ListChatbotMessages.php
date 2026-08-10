<?php

namespace App\Filament\Resources\ChatbotMessageResource\Pages;

use App\Filament\Resources\ChatbotMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListChatbotMessages extends ListRecords
{
    protected static string $resource = ChatbotMessageResource::class;
}
