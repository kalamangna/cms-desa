<?php

namespace App\Filament\Resources\BudgetRealizations\Pages;

use App\Filament\Resources\BudgetRealizations\BudgetRealizationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgetRealizations extends ListRecords
{
    protected static string $resource = BudgetRealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
