<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_number')->label('Nomor Tiket')
                    ->disabled()
                    ->placeholder('Akan di-generate otomatis'),
                TextInput::make('nik')->label('NIK Pemohon')
                    ->required(),
                TextInput::make('name')->label('Nama Lengkap')
                    ->required(),
                TextInput::make('phone')->label('Nomor WA Pemohon')
                    ->required(),
                Select::make('service_id')
                    ->label('Jenis Layanan')
                    ->relationship('service', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')->label('Status Permohonan')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Diproses' => 'Diproses',
                        'Selesai' => 'Selesai',
                    ])
                    ->default('Menunggu')
                    ->required()
                    ->helperText(function ($record) {
                        if (! $record?->phone) {
                            return null;
                        }

                        $waNumber = preg_replace('/[^0-9]/', '', $record->phone);
                        if (str_starts_with($waNumber, '0')) {
                            $waNumber = '62' . substr($waNumber, 1);
                        }

                        $status      = $record->status ?? 'Menunggu';
                        $serviceName = $record->service?->title ?? 'layanan yang Anda ajukan';

                        $statusLabel = match ($status) {
                            'Diproses' => 'sedang DIPROSES',
                            'Selesai'  => 'telah SELESAI diproses. Silakan mengambil berkas fisik di Kantor Desa',
                            default    => 'masih MENUNGGU untuk diproses',
                        };

                        $message = "Halo {$record->name}, permohonan {$serviceName} Anda (Nomor Tiket: {$record->ticket_number}) {$statusLabel}.\n\nTerima kasih.";
                        $url = "https://wa.me/{$waNumber}?text=" . urlencode($message);

                        $btnColor = match ($status) {
                            'Selesai'  => '#25d366',
                            'Diproses' => '#f59e0b',
                            default    => '#64748b',
                        };

                        return new HtmlString(
                            "<a href='{$url}' target='_blank' rel='noopener' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$btnColor}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 8px; text-decoration: none;'>"
                            . "<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim Notifikasi WA ke Pemohon</a>"
                        );
                    }),
            ]);
    }
}

