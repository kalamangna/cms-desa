<?php

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pengaduan')
                    ->columnSpanFull()
                    ->schema([
                        Placeholder::make('ticket_badge')
                            ->label('Nomor Tiket')
                            ->visible(fn ($record): bool => $record !== null)
                            ->content(fn ($record) => $record?->ticket_number ? new HtmlString(
                                "<div style='display: inline-flex; align-items: center; gap: 8px; background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 8px 16px; border-radius: 10px; font-family: monospace; font-size: 16px; font-weight: 800; color: #047857; letter-spacing: 1px;'>"
                                ."<i class='fa-solid fa-ticket' style='font-size: 15px; color: #10b981;'></i> {$record->ticket_number}"
                                .'</div>'
                            ) : '-')
                            ->columnSpanFull(),

                        Grid::make(['default' => 1, 'md' => 2])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Pelapor')
                                    ->placeholder('Andi Muhammad')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('phone')
                                    ->label('WhatsApp')
                                    ->placeholder('081234567890')
                                    ->required()
                                    ->columnSpan(1),
                            ]),

                        TextInput::make('title')
                            ->label('Judul Pengaduan')
                            ->placeholder('Lampu jalan padam di Dusun Selatan')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label('Rincian Pengaduan')
                            ->placeholder('Lokasi, waktu kejadian, dan rincian kendala')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Tindak Lanjut')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->placeholder('Pilih status')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Diproses' => 'Diproses',
                                'Selesai' => 'Selesai',
                            ])
                            ->default('Menunggu')
                            ->required(),

                        Textarea::make('response')
                            ->label('Tanggapan')
                            ->placeholder('Petugas menjadwalkan perbaikan pekan ini')
                            ->rows(4),
                    ]),
            ]);
    }
}
