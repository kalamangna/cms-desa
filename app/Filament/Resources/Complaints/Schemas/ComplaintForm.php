<?php

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
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
                \Filament\Forms\Components\Placeholder::make('ticket_badge')
                    ->label('Nomor Tiket Pengaduan')
                    ->content(fn ($record) => $record?->ticket_number ? new HtmlString(
                        "<div style='display: inline-flex; align-items: center; gap: 8px; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px 18px; border-radius: 12px; font-family: monospace; font-size: 18px; font-weight: 800; color: #15803d; letter-spacing: 1px;'>"
                        . "<i class='fa-solid fa-receipt' style='font-size: 16px; color: #16a34a;'></i> {$record->ticket_number}"
                        . "</div>"
                    ) : 'Belum Terbit (Di-generate Otomatis)')
                    ->columnSpanFull(),

                Section::make('Data Pelapor')
                    ->description('Informasi identitas dan kontak pengirim pengaduan')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')->label('Nama Pengirim')
                                    ->placeholder('Contoh: Andi Muhammad')
                                    ->helperText('Nama lengkap pengirim pengaduan.')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('phone')->label('Nomor WA Pengirim')
                                    ->placeholder('Contoh: 081234567890')
                                    ->helperText('Nomor WhatsApp aktif pengirim.')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Isi Pengaduan')
                    ->description('Judul dan detail laporan pengaduan')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')->label('Judul Pengaduan')
                            ->placeholder('Contoh: Kerusakan Lampu Penerangan Jalan Desa')
                            ->helperText('Ringkasan judul keluhan atau aspirasi.')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('content')->label('Isi Laporan / Pengaduan')
                            ->placeholder('Tuliskan rincian kronologi atau detail pengaduan...')
                            ->helperText('Penjelasan detail mengenai keluhan atau aduan.')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Tanggapan Admin')
                    ->description('Status penanganan dan tanggapan admin.')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')->label('Status Pengaduan')
                            ->placeholder('Pilih Status Penanganan')
                            ->helperText('Pilih status proses penanganan pengaduan.')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Diproses' => 'Diproses',
                                'Selesai' => 'Selesai',
                            ])
                            ->default('Menunggu')
                            ->required(),
                        Textarea::make('response')->label('Tanggapan / Tindak Lanjut Admin')
                            ->placeholder('Tuliskan balasan resmi atau laporan tindak lanjut dari pemerintah desa...')
                            ->rows(3)
                            ->helperText(function ($record) {
                                if (! $record?->phone) {
                                    return 'Balasan untuk pelapor.';
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

                                return new \Illuminate\Support\HtmlString(
                                    "Balasan untuk pelapor.<br><a href='{$url}' target='_blank' rel='noopener' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$btnColor}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 4px; text-decoration: none;'>"
                                    . "<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim Notifikasi WA ke Pelapor</a>"
                                );
                            }),
                    ]),
            ]);
    }
}
