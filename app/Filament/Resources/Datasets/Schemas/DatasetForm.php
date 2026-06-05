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
                TextInput::make('title')->label('Judul')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')->label('Deskripsi')
                    ->columnSpanFull(),
                TextInput::make('year')->label('Tahun')
                    ->numeric()
                    ->required(),
                TextInput::make('source')->label('Sumber'),
                FileUpload::make('file_csv')->label('File CSV')
                    ->directory('datasets/csv')
                    ->acceptedFileTypes(['text/csv', 'text/plain']),
                FileUpload::make('file_xlsx')->label('File XLSX')
                    ->directory('datasets/xlsx')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
                FileUpload::make('file_pdf')->label('File PDF')
                    ->directory('datasets/pdf')
                    ->acceptedFileTypes(['application/pdf']),
            ]);
    }
}
