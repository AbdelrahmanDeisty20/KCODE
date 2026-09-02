<?php

namespace App\Filament\Resources\PointsProgramPolicyResource\Pages;

use App\Filament\Resources\PointsProgramPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointsProgramPolicy extends EditRecord
{
    protected static string $resource = PointsProgramPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
