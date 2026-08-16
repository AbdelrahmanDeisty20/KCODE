<?php

namespace App\Filament\Resources\BlogAuthorResource\Pages;

use App\Filament\Resources\BlogAuthorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogAuthor extends ViewRecord
{
    protected static string $resource = BlogAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
