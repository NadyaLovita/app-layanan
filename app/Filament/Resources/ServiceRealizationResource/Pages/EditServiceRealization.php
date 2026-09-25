<?php

namespace App\Filament\Resources\ServiceRealizationResource\Pages;

use App\Filament\Resources\ServiceRealizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceRealization extends EditRecord
{
    protected static string $resource = ServiceRealizationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
