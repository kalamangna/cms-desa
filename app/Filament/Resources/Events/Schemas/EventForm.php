<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('location'),
                DateTimePicker::make('start_at')
                    ->required(),
                DateTimePicker::make('end_at'),
                FileUpload::make('featured_image')
                    ->image()
                    ->directory('events'),
            ]);
    }
}
