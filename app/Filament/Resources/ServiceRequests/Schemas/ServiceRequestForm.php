<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('ticket_badge')
                    ->label('Nomor Tiket Permohonan')
                    ->content(fn ($record) => $record?->ticket_number ? new HtmlString(
                        "<div style='display: inline-flex; align-items: center; gap: 8px; background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px 18px; border-radius: 12px; font-family: monospace; font-size: 18px; font-weight: 800; color: #047857; letter-spacing: 1px;'>"
                        ."<i class='fa-solid fa-ticket' style='font-size: 16px; color: #10b981;'></i> {$record->ticket_number}"
                        .'</div>'
                    ) : 'Belum Terbit (Di-generate Otomatis)')
                    ->columnSpanFull(),

                Section::make('Data Pemohon')
                    ->description('Identitas dan kontak pemohon.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nik')->label('NIK Pemohon')
                                    ->placeholder('Contoh: 730601...')
                                    ->helperText('Nomor Induk Kependudukan (16 digit).')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('name')->label('Nama Lengkap')
                                    ->placeholder('Contoh: Andi Muhammad')
                                    ->helperText('Nama lengkap pemohon.')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('phone')->label('Nomor WA Pemohon')
                                    ->placeholder('Contoh: 081234567890')
                                    ->helperText('Nomor WhatsApp aktif pemohon.')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Detail Permohonan & Status')
                    ->description('Layanan dan status penanganan.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('service_id')
                                    ->label('Jenis Layanan')
                                    ->placeholder('Pilih Jenis Surat/Layanan')
                                    ->helperText('Jenis permohonan surat yang diajukan warga.')
                                    ->relationship('service', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('status')->label('Status Permohonan')
                                    ->placeholder('Pilih Status')
                                    ->options([
                                        'Menunggu' => 'Menunggu',
                                        'Diproses' => 'Diproses',
                                        'Selesai' => 'Selesai',
                                    ])
                                    ->default('Menunggu')
                                    ->required()
                                    ->helperText(function ($record) {
                                        if (! $record?->phone) {
                                            return 'Status proses permohonan.';
                                        }

                                        $waNumber = preg_replace('/[^0-9]/', '', $record->phone);
                                        if (str_starts_with($waNumber, '0')) {
                                            $waNumber = '62'.substr($waNumber, 1);
                                        }

                                        $status = $record->status ?? 'Menunggu';
                                        $serviceName = $record->service?->title ?? 'layanan yang Anda ajukan';

                                        $statusLabel = match ($status) {
                                            'Diproses' => 'sedang DIPROSES',
                                            'Selesai' => 'telah SELESAI diproses. Silakan mengambil berkas fisik di Kantor Desa',
                                            default => 'masih MENUNGGU untuk diproses',
                                        };

                                        $message = "Halo {$record->name}, permohonan {$serviceName} Anda (Nomor Tiket: {$record->ticket_number}) {$statusLabel}.\n\nTerima kasih.";
                                        $url = "https://wa.me/{$waNumber}?text=".urlencode($message);

                                        $btnColor = match ($status) {
                                            'Selesai' => '#25d366',
                                            'Diproses' => '#f59e0b',
                                            default => '#64748b',
                                        };

                                        return new HtmlString(
                                            "Status proses permohonan.<br><a href='{$url}' target='_blank' rel='noopener' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$btnColor}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 4px; text-decoration: none;'>"
                                            ."<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim Notifikasi WA ke Pemohon</a>"
                                        );
                                    }),
                            ]),
                    ]),
            ]);
    }
}
