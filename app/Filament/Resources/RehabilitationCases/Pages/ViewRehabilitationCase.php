<?php

namespace App\Filament\Resources\RehabilitationCases\Pages;

use App\Filament\Resources\RehabilitationCases\RehabilitationCaseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRehabilitationCase extends ViewRecord
{
    protected static string $resource = RehabilitationCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
