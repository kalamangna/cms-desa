<?php

namespace App\Filament\Resources\BudgetRealizations\Pages;

use App\Filament\Resources\BudgetRealizations\BudgetRealizationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBudgetRealization extends EditRecord
{
    protected static string $resource = BudgetRealizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
