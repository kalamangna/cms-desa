<?php

namespace App\Filament\Resources\Families\Pages;

use App\Filament\Resources\Families\FamilyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditFamily extends EditRecord
{
    protected static string $resource = FamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['assistance_type']) && is_string($data['assistance_type'])) {
            if (empty($data['assistance_type']) || $data['assistance_type'] === 'Tidak Ada') {
                $data['assistance_type'] = [];
            } else {
                $data['assistance_type'] = array_map('trim', explode(',', $data['assistance_type']));
            }
        }
        return $data;
    }
}
