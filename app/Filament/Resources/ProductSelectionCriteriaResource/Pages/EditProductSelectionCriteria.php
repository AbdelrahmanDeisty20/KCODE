<?php

namespace App\Filament\Resources\ProductSelectionCriteriaResource\Pages;

use App\Filament\Resources\ProductSelectionCriteriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductSelectionCriteria extends EditRecord
{
    protected static string $resource = ProductSelectionCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
