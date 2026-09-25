<?php

namespace App\Filament\Resources\ServiceRealizationResource\Pages;

use App\Enums\ConformityStatus;
use App\Filament\Resources\ServiceRealizationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRealization extends CreateRecord
{
    protected static string $resource = ServiceRealizationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        // Auto-set conformity_status for incidental activities
        if (($data['activity_type'] ?? '') === 'incidental') {
            $data['conformity_status'] = ConformityStatus::Unplanned->value;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
