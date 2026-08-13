<?php

namespace App\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Cadangan extends BaseBackups
{
    protected static ?string $slug = 'backups';

    protected string $view = 'filament.pages.cadangan';

    public function getTitle(): string|Htmlable
    {
        return 'Cadangan';
    }
}
