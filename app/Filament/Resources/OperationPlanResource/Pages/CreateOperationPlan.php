<?php

namespace App\Filament\Resources\OperationPlanResource\Pages;

use App\Filament\Resources\OperationPlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOperationPlan extends CreateRecord
{
    protected static string $resource = OperationPlanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
