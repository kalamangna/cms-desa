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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Permohonan Layanan')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Permohonan')
                            ->icon('heroicon-o-clipboard-document-list')
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

                                Grid::make(['default' => 1, 'md' => 3])
                                    ->schema([
                                        TextInput::make('nik')
                                            ->label('NIK')
                                            ->placeholder('16 digit NIK')
                                            ->required()
                                            ->maxLength(16)
                                            ->rule('digits:16')
                                            ->extraInputAttributes(['inputmode' => 'numeric'])
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->columnSpan(1),
                                        TextInput::make('name')
                                            ->label('Nama Lengkap')
                                            ->placeholder('Budi Santoso')
                                            ->required()
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->columnSpan(1),
                                        TextInput::make('phone')
                                            ->label('WhatsApp')
                                            ->placeholder('081234567890')
                                            ->required()
                                            ->columnSpan(1),
                                    ]),

                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        Select::make('service_id')
                                            ->label('Layanan')
                                            ->placeholder('Pilih layanan')
                                            ->relationship('service', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->required()
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                        Select::make('status')
                                            ->label('Status')
                                            ->placeholder('Pilih status')
                                            ->options([
                                                'Menunggu' => 'Menunggu',
                                                'Diproses' => 'Diproses',
                                                'Selesai' => 'Selesai',
                                            ])
                                            ->default('Menunggu')
                                            ->required()
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->helperText(function ($record) {
                                                if (! $record?->phone) {
                                                    return null;
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
                                                    "<a href='{$url}' target='_blank' rel='noopener noreferrer' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$btnColor}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 4px; text-decoration: none;'>"
                                                    ."<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim Notifikasi WA</a>"
                                                );
                                            }),
                                    ]),
                            ]),

                        Tabs\Tab::make('Berkas')
                            ->icon('heroicon-o-paper-clip')
                            ->visible(fn ($record): bool => $record !== null)
                            ->schema([
                                Placeholder::make('attachments_preview')
                                    ->hiddenLabel()
                                    ->content(fn ($record) => view('filament.service-requests.attachments-list', [
                                        'record' => $record?->loadMissing('attachments'),
                                    ]))
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Surat TTE')
                            ->icon('heroicon-o-document-check')
                            ->visible(fn ($record): bool => $record !== null)
                            ->schema([
                                Placeholder::make('tte_status_panel')
                                    ->hiddenLabel()
                                    ->content(fn ($record) => view('filament.service-requests.tte-status', [
                                        'record' => $record,
                                        'isGlobalTteEnabled' => app(LetterDocumentService::class)->isGlobalTteEnabled(),
                                    ]))
                                    ->columnSpanFull(),

                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextInput::make('letter_number')
                                            ->label('Nomor Surat')
                                            ->placeholder('470/001/DESA/XI/2026')
                                            ->helperText('Kosongkan untuk penomoran otomatis.')
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->maxLength(120),
                                        DatePicker::make('letter_date')
                                            ->label('Tanggal Surat')
                                            ->default(now()->toDateString())
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                    ]),

                                KeyValue::make('form_data')
                                    ->label('Isian Tambahan')
                                    ->keyLabel('Kolom')
                                    ->valueLabel('Nilai')
                                    ->addButtonLabel('Tambah Isian')
                                    ->visible(fn ($record): bool => ! empty($record?->form_data) || ! empty($record?->service?->form_fields))
                                    ->addable(fn ($record): bool => ! ($record?->isTteSigned() ?? false))
                                    ->deletable(fn ($record): bool => ! ($record?->isTteSigned() ?? false))
                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                    ->columnSpanFull(),

                                Section::make('Data Kependudukan')
                                    ->collapsible()
                                    ->collapsed()
                                    ->compact()
                                    ->schema([
                                        KeyValue::make('letter_payload')
                                            ->hiddenLabel()
                                            ->keyLabel('Variabel')
                                            ->valueLabel('Nilai')
                                            ->addButtonLabel('Tambah Variabel')
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
