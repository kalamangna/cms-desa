<?php

namespace App\Filament\Resources\GuestBooks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class GuestBookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(3)
                    ->schema([
                        TextInput::make('name')->label('Nama Lengkap')
                            ->placeholder('Contoh: Ir. Budi Santoso')
                            ->helperText('Nama lengkap tamu pengunjung.')
                            ->required(),
                        TextInput::make('institution_address')->label('Instansi / Alamat')
                            ->placeholder('Contoh: Dinas Pemberdayaan Masyarakat Desa')
                            ->helperText('Instansi atau domisili tamu.')
                            ->required(),
                        TextInput::make('phone')->label('Nomor Kontak (WhatsApp)')
                            ->placeholder('Contoh: 081234567890')
                            ->helperText('Nomor WhatsApp aktif tamu.')
                            ->required(),
                    ])
                    ->columnSpanFull(),
                Textarea::make('purpose')->label('Keperluan / Pesan')
                    ->placeholder('Contoh: Koordinasi program pendampingan desa digital...')
                    ->helperText('Tujuan kunjungan ke kantor desa.')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
