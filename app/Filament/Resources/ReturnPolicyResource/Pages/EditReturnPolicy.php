<?php

namespace App\Filament\Resources\ReturnPolicyResource\Pages;

use App\Filament\Resources\ReturnPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReturnPolicy extends EditRecord
{
    protected static string $resource = ReturnPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
