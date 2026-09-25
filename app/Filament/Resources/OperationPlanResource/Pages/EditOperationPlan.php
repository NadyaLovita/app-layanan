<?php

namespace App\Filament\Resources\OperationPlanResource\Pages;

use App\Filament\Resources\OperationPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperationPlan extends EditRecord
{
    protected static string $resource = OperationPlanResource::class;

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
