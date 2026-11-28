<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\LetterDocumentService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
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
                                        "<div style='display: inline-flex; align-items: center; gap: 8px; background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); padding: 8px 16px; border-radius: 10px; font-family: monospace; font-size: 16px; font-weight: 800; color: #10b981; letter-spacing: 1px;'>"
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
                                                'Ditolak' => 'Ditolak',
                                            ])
                                            ->default('Menunggu')
                                            ->live()
                                            ->required()
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->helperText(function ($record, Get $get) {
                                                $wa = $record?->buildWhatsAppNotification($get('status'), $get('admin_response'));
                                                if (! $wa) {
                                                    return null;
                                                }

                                                return new HtmlString(
                                                    "<a href='{$wa['url']}' target='_blank' rel='noopener noreferrer' style='display: inline-flex; align-items: center; gap: 6px; background-color: {$wa['color']}; color: white; font-weight: bold; padding: 6px 14px; border-radius: 8px; font-size: 11px; margin-top: 4px; text-decoration: none;'>"
                                                    ."<i class='fa-brands fa-whatsapp' style='font-size: 14px;'></i> Kirim WA</a>"
                                                );
                                            }),
                                    ]),

                                Textarea::make('admin_response')
                                    ->label('Catatan Petugas')
                                    ->placeholder('Foto KTP buram, silakan ajukan ulang dengan foto yang jelas')
                                    ->rows(2)
                                    ->maxLength(1000)
                                    ->live(onBlur: true)
                                    ->helperText('Ditampilkan kepada warga pada halaman pelacakan tiket.')
                                    ->columnSpanFull(),

                                Section::make('Data Kependudukan')
                                    ->collapsible()
                                    ->collapsed()
                                    ->compact()
                                    ->visible(fn ($record): bool => $record !== null)
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])
                                            ->schema([
                                                TextInput::make('letter_payload.place_of_birth')
                                                    ->label('Tempat Lahir')
                                                    ->placeholder('Sinjai')
                                                    ->maxLength(100)
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                DatePicker::make('letter_payload.date_of_birth')
                                                    ->label('Tanggal Lahir')
                                                    ->placeholder('Pilih tanggal lahir')
                                                    ->native(false)
                                                    ->displayFormat('d F Y')
                                                    ->format('Y-m-d')
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                Select::make('letter_payload.gender')
                                                    ->label('Jenis Kelamin')
                                                    ->placeholder('Pilih jenis kelamin')
                                                    ->options([
                                                        'Laki-laki' => 'Laki-laki',
                                                        'Perempuan' => 'Perempuan',
                                                    ])
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                Select::make('letter_payload.religion')
                                                    ->label('Agama')
                                                    ->placeholder('Pilih agama')
                                                    ->options([
                                                        'Islam' => 'Islam',
                                                        'Kristen' => 'Kristen',
                                                        'Katolik' => 'Katolik',
                                                        'Hindu' => 'Hindu',
                                                        'Buddha' => 'Buddha',
                                                        'Konghucu' => 'Konghucu',
                                                    ])
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                Select::make('letter_payload.marital_status')
                                                    ->label('Status Perkawinan')
                                                    ->placeholder('Pilih status perkawinan')
                                                    ->options([
                                                        'Belum Kawin' => 'Belum Kawin',
                                                        'Kawin' => 'Kawin',
                                                        'Cerai Hidup' => 'Cerai Hidup',
                                                        'Cerai Mati' => 'Cerai Mati',
                                                    ])
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                TextInput::make('letter_payload.job')
                                                    ->label('Pekerjaan')
                                                    ->placeholder('Wiraswasta')
                                                    ->maxLength(100)
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                            ]),
                                        TextInput::make('letter_payload.address')
                                            ->label('Alamat')
                                            ->placeholder('Jl. Poros Desa No. 12')
                                            ->maxLength(255)
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->columnSpanFull(),
                                        Grid::make(['default' => 1, 'md' => 3])
                                            ->schema([
                                                TextInput::make('letter_payload.rt')
                                                    ->label('RT')
                                                    ->placeholder('001')
                                                    ->maxLength(10)
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                TextInput::make('letter_payload.rw')
                                                    ->label('RW')
                                                    ->placeholder('002')
                                                    ->maxLength(10)
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                                TextInput::make('letter_payload.dusun')
                                                    ->label('Dusun')
                                                    ->placeholder('Mattirowalie')
                                                    ->maxLength(100)
                                                    ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Berkas')
                            ->icon('heroicon-o-paper-clip')
                            ->badge(function ($record): ?string {
                                if (! $record) {
                                    return null;
                                }

                                $count = $record->relationLoaded('attachments')
                                    ? $record->attachments->count()
                                    : $record->attachments()->count();

                                return $count > 0 ? (string) $count : null;
                            })
                            ->badgeColor('info')
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
                            ->badge(fn ($record): ?string => match ($record?->tte_status) {
                                ServiceRequest::TTE_DRAFT => 'Draf',
                                ServiceRequest::TTE_SIGNED => 'Sudah TTE',
                                ServiceRequest::TTE_REVOKED => 'Dicabut',
                                default => null,
                            })
                            ->badgeColor(fn ($record): string => match ($record?->tte_status) {
                                ServiceRequest::TTE_DRAFT => 'warning',
                                ServiceRequest::TTE_SIGNED => 'success',
                                ServiceRequest::TTE_REVOKED => 'danger',
                                default => 'gray',
                            })
                            ->visible(fn ($record): bool => $record !== null)
                            ->schema([
                                View::make('filament.service-requests.tte-status')
                                    ->viewData(fn ($record): array => [
                                        'record' => $record,
                                        'isGlobalTteEnabled' => app(LetterDocumentService::class)->isGlobalTteEnabled(),
                                    ])
                                    ->columnSpanFull(),

                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        TextInput::make('letter_number')
                                            ->label('Nomor Surat')
                                            ->placeholder('400.12.2/001/DESA/2026')
                                            ->helperText('Kosongkan untuk penomoran otomatis.')
                                            ->unique(table: 'service_requests', column: 'letter_number', ignoreRecord: true)
                                            ->validationMessages([
                                                'unique' => 'Nomor surat sudah digunakan pada permohonan lain.',
                                            ])
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                            ->maxLength(120),
                                        DatePicker::make('letter_date')
                                            ->label('Tanggal Surat')
                                            ->default(now()->toDateString())
                                            ->native(false)
                                            ->displayFormat('d F Y')
                                            ->format('Y-m-d')
                                            ->helperText('Kosongkan untuk tanggal hari ini otomatis.')
                                            ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false),
                                    ]),

                                Section::make('Isian Tambahan')
                                    ->compact()
                                    ->columns(['default' => 1, 'md' => 2])
                                    ->visible(function ($record, Get $get): bool {
                                        $serviceId = $get('service_id') ?? $record?->service_id;
                                        $service = $serviceId
                                            ? ($record?->service_id === (int) $serviceId ? $record->service : Service::find($serviceId))
                                            : null;

                                        return ! empty($service?->form_fields) || ! empty($record?->form_data);
                                    })
                                    ->schema(function ($record, Get $get): array {
                                        $serviceId = $get('service_id') ?? $record?->service_id;
                                        $service = $serviceId
                                            ? ($record?->service_id === (int) $serviceId ? $record->service : Service::find($serviceId))
                                            : null;

                                        $formFields = is_array($service?->form_fields) ? $service->form_fields : [];
                                        $components = [];

                                        foreach ($formFields as $field) {
                                            $key = $field['key'] ?? null;
                                            if (! is_string($key) || $key === '') {
                                                continue;
                                            }

                                            $label = (string) ($field['label'] ?? $key);
                                            $placeholder = (string) ($field['placeholder'] ?? '');
                                            $isRequired = (bool) ($field['is_required'] ?? false);
                                            $type = (string) ($field['type'] ?? 'text');
                                            $fieldPath = "form_data.{$key}";

                                            $component = match ($type) {
                                                'textarea' => Textarea::make($fieldPath)
                                                    ->rows(2)
                                                    ->maxLength(1000)
                                                    ->columnSpanFull(),
                                                'date' => DatePicker::make($fieldPath)
                                                    ->native(false)
                                                    ->displayFormat('d F Y')
                                                    ->format('Y-m-d'),
                                                'number' => TextInput::make($fieldPath)
                                                    ->numeric(),
                                                default => TextInput::make($fieldPath)
                                                    ->maxLength(1000),
                                            };

                                            $components[] = $component
                                                ->label($label)
                                                ->placeholder($placeholder)
                                                ->required($isRequired)
                                                ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false);
                                        }

                                        if ($components !== []) {
                                            return $components;
                                        }

                                        return [
                                            KeyValue::make('form_data')
                                                ->hiddenLabel()
                                                ->keyLabel('Kolom')
                                                ->valueLabel('Nilai')
                                                ->addButtonLabel('Tambah Isian')
                                                ->addable(fn ($record): bool => ! ($record?->isTteSigned() ?? false))
                                                ->deletable(fn ($record): bool => ! ($record?->isTteSigned() ?? false))
                                                ->disabled(fn ($record): bool => $record?->isTteSigned() ?? false)
                                                ->columnSpanFull(),
                                        ];
                                    })
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
