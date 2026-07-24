<?php

namespace App\Filament\Resources\Datasets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class DatasetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dataset')
                    ->description('Metadata dan keterangan umum tentang dataset')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')->label('Judul')
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('year')->label('Tahun')
                                    ->length(4)
                                    ->required(),
                                TextInput::make('source')->label('Sumber'),
                            ]),
                        Textarea::make('description')->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Berkas Unduhan')
                    ->description('Unggah berkas dataset dalam format yang tersedia')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('file_csv')->label('File CSV')
                                    ->directory('datasets/csv')
                                    ->acceptedFileTypes(['text/csv', 'text/plain']),
                                FileUpload::make('file_xlsx')->label('File XLSX')
                                    ->directory('datasets/xlsx')
                                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),
                                FileUpload::make('file_pdf')->label('File PDF')
                                    ->directory('datasets/pdf')
                                    ->acceptedFileTypes(['application/pdf']),
                            ]),
                    ]),
            ]);
    }
}
