<?php

namespace App\Filament\Resources\Datasets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class DatasetForm
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
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('year')
                    ->numeric()
                    ->required(),
                TextInput::make('source'),
                FileUpload::make('file_csv')
                    ->label('CSV File')
                    ->directory('datasets/csv')
                    ->acceptedFileTypes(['text/csv', 'text/plain']),
                FileUpload::make('file_xlsx')
                    ->label('XLSX File')
                    ->directory('datasets/xlsx')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
                FileUpload::make('file_pdf')
                    ->label('PDF File')
                    ->directory('datasets/pdf')
                    ->acceptedFileTypes(['application/pdf']),
            ]);
    }
}
