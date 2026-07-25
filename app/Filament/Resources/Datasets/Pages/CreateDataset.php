<?php

namespace App\Filament\Resources\Datasets\Pages;

use App\Filament\Resources\Datasets\DatasetResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateDataset extends CreateRecord
{
    protected static string $resource = DatasetResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
