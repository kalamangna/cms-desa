<?php

namespace App\Filament\Resources\Publications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class PublicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul Publikasi')
                    ->placeholder('Contoh: Kecamatan Dalam Angka 2026')
                    ->helperText('Nama resmi buku publikasi statistik.')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        Select::make('type')->label('Tipe Publikasi')
                            ->placeholder('Pilih Tipe')
                            ->helperText('Kelompok tipe buku data.')
                            ->options([
                                'Desa Dalam Angka' => 'Desa Dalam Angka',
                                'Profil Statistik Desa' => 'Profil Statistik Desa',
                                'Infografis' => 'Infografis',
                            ])
                            ->required(),
                        TextInput::make('year')->label('Tahun Diterbitkan')
                            ->placeholder('Contoh: 2026')
                            ->helperText('Tahun rilis buku.')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                    ])
                    ->columnSpanFull(),
                FileUpload::make('cover')
                    ->label('Sampul (Cover)')
                    ->helperText('Unggah gambar sampul buku (Otomatis dioptimalkan, maks 2MB).')
                    ->image()
                    ->imageResizeTargetWidth(600)
                    ->maxSize(2048)
                    ->nullable()
                    ->directory('publications/covers')
                    ->columnSpanFull(),
                FileUpload::make('pdf_file')->label('File Buku PDF')
                    ->helperText('Unggah berkas dokumen PDF publikasi.')
                    ->directory('publications/pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
