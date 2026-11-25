<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Models\ServiceRequest;
use App\Services\LetterDocumentService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
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
                    ) : 'Belum Terbit')
                    ->columnSpanFull(),

                Section::make('Data Pemohon')
                    ->description('Identitas dan kontak pemohon.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nik')->label('NIK Pemohon')
                                    ->placeholder('16 digit NIK')
                                    ->helperText('Nomor Induk Kependudukan 16 digit.')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('name')->label('Nama Lengkap')
                                    ->placeholder('Budi Santoso')
                                    ->helperText('Nama lengkap pemohon.')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('phone')->label('Nomor WA Pemohon')
                                    ->placeholder('081234567890')
                                    ->helperText('Nomor WhatsApp aktif pemohon.')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Detail Permohonan dan Status')
                    ->description('Layanan dan status penanganan.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('service_id')
                                    ->label('Jenis Layanan')
                                    ->placeholder('Pilih Jenis Layanan')
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

                                        if ($record->tte_status === ServiceRequest::TTE_SIGNED) {
                                            $trackUrl = route('service-requests.track', ['ticket_number' => $record->ticket_number]);
                                            $message = "Halo {$record->name}, surat {$serviceName} Anda dengan Nomor Tiket {$record->ticket_number} telah selesai ditandatangani secara elektronik. Unduh surat PDF melalui tautan berikut dengan verifikasi NIK: {$trackUrl}";
                                            $btnColor = '#25d366';
                                        } else {
                                            $statusLabel = match ($status) {
                                                'Diproses' => 'sedang DIPROSES',
                                                'Selesai' => 'telah SELESAI diproses. Silakan mengambil berkas fisik di Kantor Desa',
                                                default => 'masih MENUNGGU untuk diproses',
                                            };

                                            $message = "Halo {$record->name}, permohonan {$serviceName} Anda dengan Nomor Tiket {$record->ticket_number} {$statusLabel}.\n\nTerima kasih.";
                                            $btnColor = match ($status) {
                                                'Selesai' => '#25d366',
                                                'Diproses' => '#f59e0b',
                                                default => '#64748b',
                                            };
                                        }

                                        $url = "https://wa.me/{$waNumber}?text=".urlencode($message);

                                        return new HtmlString(
                                            "Status proses permohonan.<br><a href='{$url}' target='_blank' rel='noopener noreferrer' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$btnColor}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 4px; text-decoration: none;'>"
                                            ."<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim Notifikasi WA</a>"
                                        );
                                    }),
                            ]),
                    ]),

                Section::make('Data Surat dan TTE')
                    ->description('Data isian surat otomatis dan status tanda tangan elektronik BSrE.')
                    ->columnSpanFull()
                    ->visible(fn ($record): bool => $record !== null)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('letter_number')
                                    ->label('Nomor Surat')
                                    ->placeholder('470/001/TPB/XI/2026')
                                    ->helperText('Kosongkan agar dibuat otomatis saat mencetak draf PDF.')
                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                    ->maxLength(120),
                                DatePicker::make('letter_date')
                                    ->label('Tanggal Surat')
                                    ->default(now()->toDateString())
                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                            ]),

                        KeyValue::make('letter_payload')
                            ->label('Data Kependudukan Surat')
                            ->keyLabel('Variabel')
                            ->valueLabel('Nilai')
                            ->addButtonLabel('Tambah Variabel')
                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                            ->columnSpanFull(),

                        KeyValue::make('form_data')
                            ->label('Isian Tambahan Pemohon')
                            ->keyLabel('Kolom')
                            ->valueLabel('Nilai')
                            ->addButtonLabel('Tambah Isian')
                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                            ->columnSpanFull(),

                        Placeholder::make('tte_status_panel')
                            ->hiddenLabel()
                            ->content(fn ($record) => view('filament.service-requests.tte-status', [
                                'record' => $record,
                                'isGlobalTteEnabled' => app(LetterDocumentService::class)->isGlobalTteEnabled(),
                            ]))
                            ->columnSpanFull(),
                    ]),

                Section::make('Berkas Persyaratan')
                    ->description('Dokumen persyaratan yang diunggah pemohon.')
                    ->columnSpanFull()
                    ->visible(fn ($record): bool => $record !== null)
                    ->schema([
                        Placeholder::make('attachments_preview')
                            ->hiddenLabel()
                            ->content(fn ($record) => view('filament.service-requests.attachments-list', [
                                'record' => $record?->loadMissing('attachments'),
                            ])),
                    ]),
            ]);
    }
}
