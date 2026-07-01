<?php

namespace App\Filament\Resources\Citizens\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CitizenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nik')->label('NIK')
                    ->required(),
                TextInput::make('kk_number')->label('No. KK'),
                \Filament\Forms\Components\Select::make('dusun_id')->label('Dusun')
                    ->relationship('dusun', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')->label('Nama')
                    ->required(),
                TextInput::make('place_of_birth')->label('Tempat Lahir'),
                DatePicker::make('date_of_birth')->label('Tanggal Lahir'),
                \Filament\Forms\Components\Select::make('gender')->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ]),
                TextInput::make('religion')->label('Agama'),
                TextInput::make('education')->label('Pendidikan'),
                TextInput::make('job')->label('Pekerjaan'),
                TextInput::make('blood_type')->label('Gol. Darah'),
                TextInput::make('marital_status')->label('Status Kawin'),
                Textarea::make('address')->label('Alamat')
                    ->columnSpanFull(),
                TextInput::make('rt')->label('RT'),
                TextInput::make('rw')->label('RW'),
                \Filament\Forms\Components\Select::make('status')->label('Status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Pindah' => 'Pindah',
                        'Meninggal' => 'Meninggal',
                    ])
                    ->required()
                    ->default('Aktif'),
            ]);
    }
}
