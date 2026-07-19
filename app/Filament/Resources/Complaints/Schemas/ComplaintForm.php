<?php

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_number')->label('Nomor Tiket')
                    ->disabled()
                    ->placeholder('Akan di-generate otomatis'),
                TextInput::make('name')->label('Nama Pengirim')
                    ->required(),
                TextInput::make('phone')->label('Nomor WA Pengirim')
                    ->required(),
                TextInput::make('title')->label('Judul Pengaduan')
                    ->required(),
                Textarea::make('content')->label('Isi Laporan / Pengaduan')
                    ->rows(4)
                    ->required(),
                Select::make('status')->label('Status Pengaduan')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Diproses' => 'Diproses',
                        'Selesai' => 'Selesai',
                    ])
                    ->default('Menunggu')
                    ->required(),
                Textarea::make('response')->label('Tanggapan / Tindak Lanjut Admin')
                    ->rows(3)
                    ->helperText(function ($record) {
                        if (! $record?->phone) {
                            return null;
                        }

                        $waNumber = preg_replace('/[^0-9]/', '', $record->phone);
                        if (str_starts_with($waNumber, '0')) {
                            $waNumber = '62' . substr($waNumber, 1);
                        }

                        $status  = $record->status ?? 'Menunggu';
                        $response = $record->response ? "\n\nTanggapan: {$record->response}" : '';

                        $statusLabel = match ($status) {
                            'Diproses' => 'sedang DIPROSES',
                            'Selesai'  => 'telah SELESAI ditindaklanjuti',
                            default    => 'masih MENUNGGU',
                        };

                        $message = "Halo {$record->name}, pengaduan Anda dengan judul \"{$record->title}\" (Nomor Tiket: {$record->ticket_number}) {$statusLabel}.{$response}\n\nTerima kasih.";
                        $url = "https://wa.me/{$waNumber}?text=" . urlencode($message);

                        $btnColor = match ($status) {
                            'Selesai'  => '#25d366',
                            'Diproses' => '#f59e0b',
                            default    => '#64748b',
                        };

                        return new HtmlString(
                            "<a href='{$url}' target='_blank' rel='noopener' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$btnColor}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 8px; text-decoration: none;'>"
                            . "<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim Notifikasi WA ke Pelapor</a>"
                        );
                    }),
            ]);
    }
}
