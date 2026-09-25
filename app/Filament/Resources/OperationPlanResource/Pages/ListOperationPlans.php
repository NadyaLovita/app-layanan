<?php

namespace App\Filament\Resources\OperationPlanResource\Pages;

use App\Filament\Resources\OperationPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperationPlans extends ListRecords
{
    protected static string $resource = OperationPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
