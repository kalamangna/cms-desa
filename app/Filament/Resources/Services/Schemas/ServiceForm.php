<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use App\Support\LetterTemplatePresets;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Layanan')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Layanan')
                            ->placeholder('Surat Keterangan Usaha')
                            ->helperText('Nama resmi jenis layanan surat desa.')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->schema([
                                Select::make('category')
                                    ->label('Kategori Layanan')
                                    ->options(Service::CATEGORIES)
                                    ->default('Layanan Lainnya')
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->integer()
                                    ->default(100)
                                    ->minValue(1)
                                    ->required()
                                    ->columnSpan(1),
                                Toggle::make('is_active')
                                    ->label('Aktif di Halaman Publik')
                                    ->default(true)
                                    ->inline(false)
                                    ->columnSpan(1),
                            ]),
                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->placeholder('Surat keterangan resmi dari pemerintah desa untuk keperluan usaha warga.')
                            ->helperText('Kegunaan singkat layanan ini.')
                            ->required()
                            ->columnSpanFull(),
                        RichEditor::make('requirements')
                            ->label('Persyaratan')
                            ->placeholder('Daftar persyaratan dan catatan pengajuan layanan.')
                            ->helperText('Informasi persyaratan yang tampil pada halaman publik.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Daftar Berkas Persyaratan')
                    ->description('Daftar berkas yang diunggah warga saat mengajukan layanan ini.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('requirementItems')
                            ->relationship()
                            ->label('Berkas Persyaratan')
                            ->orderColumn('sort_order')
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Persyaratan')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Berkas')
                                            ->placeholder('Foto KTP')
                                            ->required()
                                            ->maxLength(150)
                                            ->columnSpan(1),
                                        TextInput::make('description')
                                            ->label('Keterangan Singkat')
                                            ->placeholder('Foto asli yang terbaca jelas')
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                        Toggle::make('is_required')
                                            ->label('Wajib Diunggah')
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Template Surat dan TTE')
                    ->description('Atur template surat otomatis untuk ditandatangani secara elektronik melalui BSrE.')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_tte_enabled')
                            ->label('Aktifkan Pembuatan Surat dan TTE')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        Select::make('letter_preset')
                            ->label('Gunakan Preset Template Surat')
                            ->placeholder('Pilih Preset Surat Bawaan')
                            ->options(LetterTemplatePresets::options())
                            ->dehydrated(false)
                            ->live()
                            ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $preset = LetterTemplatePresets::get($state);
                                if (! $preset) {
                                    return;
                                }

                                $set('letter_code', $preset['letter_code']);
                                $set('letter_title', $preset['letter_title']);
                                $set('letter_template', $preset['letter_template']);
                                $set('form_fields', $preset['form_fields']);
                            })
                            ->helperText('Memilih preset akan mengisi kode, judul, isi surat, dan kolom tambahan otomatis.')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                            ->schema([
                                TextInput::make('letter_code')
                                    ->label('Kode Klasifikasi Surat')
                                    ->placeholder('470')
                                    ->maxLength(30)
                                    ->columnSpan(1),
                                TextInput::make('letter_title')
                                    ->label('Judul pada Surat')
                                    ->placeholder('SURAT KETERANGAN DOMISILI')
                                    ->maxLength(180)
                                    ->columnSpan(2),
                            ]),

                        RichEditor::make('letter_template')
                            ->label('Isi Template Surat')
                            ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                            ->columnSpanFull(),

                        Placeholder::make('letter_placeholders_guide')
                            ->label('Variabel Placeholder Tersedia')
                            ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                            ->content(function (): HtmlString {
                                $items = [];
                                foreach (LetterTemplatePresets::availablePlaceholders() as $token => $label) {
                                    $items[] = "<code style='background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px;'>{$token}</code> <span style='font-size:11px;color:#64748b;'>{$label}</span>";
                                }

                                return new HtmlString('<div style="display:flex;flex-wrap:wrap;gap:6px 12px;">'.implode('', $items).'</div>');
                            })
                            ->columnSpanFull(),

                        Repeater::make('form_fields')
                            ->label('Kolom Tambahan Pengajuan Warga')
                            ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Kolom')
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Label Kolom')
                                            ->placeholder('Nama Usaha')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Get $get, Set $set): void {
                                                if (! $get('key') && $state) {
                                                    $set('key', Str::slug($state, '_'));
                                                }
                                            })
                                            ->columnSpan(2),
                                        TextInput::make('key')
                                            ->label('Kunci Variabel')
                                            ->placeholder('nama_usaha')
                                            ->required()
                                            ->regex('/^[a-z0-9_]+$/')
                                            ->columnSpan(1),
                                        Select::make('type')
                                            ->label('Tipe Isian')
                                            ->options([
                                                'text' => 'Teks Singkat',
                                                'textarea' => 'Teks Panjang',
                                                'date' => 'Tanggal',
                                                'number' => 'Angka',
                                            ])
                                            ->default('text')
                                            ->required()
                                            ->columnSpan(1),
                                        Toggle::make('is_required')
                                            ->label('Wajib Diisi')
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(1),
                                        TextInput::make('placeholder')
                                            ->label('Contoh Isian')
                                            ->placeholder('Warung Sembako Berkah')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
