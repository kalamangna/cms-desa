<?php

namespace App\Filament\Resources\Officials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class OfficialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('name')->label('Nama Lengkap')
                            ->placeholder('Contoh: Andi Muhammad S.Pd')
                            ->helperText('Nama lengkap aparatur beserta gelar resmi.')
                            ->required(),
                        TextInput::make('position')->label('Jabatan')
                            ->placeholder('Contoh: Kepala Desa atau Sekretaris Desa')
                            ->helperText('Nama jabatan resmi dalam struktur desa.')
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->label('Atasan Langsung')
                    ->placeholder('Pilih Atasan Langsung')
                    ->helperText('Atasan langsung dalam bagan struktur organisasi.')
                    ->relationship('parent', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->position})")
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        Select::make('level')
                            ->label('Tingkat Jabatan')
                            ->placeholder('Pilih Tingkat Jabatan')
                            ->helperText('Tingkat hierarki jabatan dalam struktur organisasi.')
                            ->options([
                                1 => 'Kepala Desa (Tingkat 1)',
                                2 => 'Sekretaris Desa (Tingkat 2)',
                                3 => 'Kasi / Kaur / Kabag (Tingkat 3)',
                                4 => 'Kepala Dusun (Tingkat 4)',
                                5 => 'Staf Seksi / Staf Urusan / Staf Pendukung (Tingkat 5)',
                            ])
                            ->default(4)
                            ->required(),
                        TextInput::make('order')
                            ->label('Urutan Tampilan')
                            ->placeholder('Contoh: 1')
                            ->helperText('Nomor urut pada bagan struktur.')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columnSpanFull(),
                FileUpload::make('photo')->label('Foto')
                    ->helperText('Unggah pasfoto resmi aparatur (Maksimal 2MB).')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeTargetWidth(500)
                    ->maxSize(2048)
                    ->nullable()
                    ->directory('officials')
                    ->columnSpanFull(),
            ]);
    }
}
