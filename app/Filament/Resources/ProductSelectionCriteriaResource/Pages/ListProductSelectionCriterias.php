<?php

namespace App\Filament\Resources\ProductSelectionCriteriaResource\Pages;

use App\Filament\Resources\ProductSelectionCriteriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductSelectionCriterias extends ListRecords
{
    protected static string $resource = ProductSelectionCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
