<?php

namespace App\Filament\Resources\Officials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class OfficialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('position')
                    ->required(),
                FileUpload::make('photo')
                    ->image()
                    ->directory('officials'),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end'),
            ]);
    }
}
