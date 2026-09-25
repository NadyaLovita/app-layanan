<?php

namespace App\Filament\Resources\ServiceRealizationResource\Pages;

use App\Filament\Resources\ServiceRealizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceRealization extends ViewRecord
{
    protected static string $resource = ServiceRealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
