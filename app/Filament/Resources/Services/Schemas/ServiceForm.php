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
use Filament\Schemas\Components\Tabs;
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
                Tabs::make('Pengaturan Layanan')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informasi')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Nama Layanan')
                                    ->placeholder('Surat Keterangan Usaha')
                                    ->required()
                                    ->columnSpanFull(),
                                Grid::make(['default' => 1, 'md' => 3])
                                    ->schema([
                                        Select::make('category')
                                            ->label('Kategori')
                                            ->options(Service::CATEGORIES)
                                            ->default('Layanan Lainnya')
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('sort_order')
                                            ->label('Urutan')
                                            ->integer()
                                            ->default(100)
                                            ->minValue(1)
                                            ->required()
                                            ->columnSpan(1),
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(1),
                                    ]),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->placeholder('Keterangan resmi kepemilikan usaha warga')
                                    ->rows(2)
                                    ->required()
                                    ->columnSpanFull(),
                                RichEditor::make('requirements')
                                    ->label('Persyaratan')
                                    ->placeholder('KTP pemohon, Kartu Keluarga')
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Berkas')
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                Repeater::make('requirementItems')
                                    ->relationship()
                                    ->label('Berkas Unggahan')
                                    ->orderColumn('sort_order')
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->addActionLabel('Tambah Berkas')
                                    ->itemLabel(function (array $state): ?string {
                                        if (empty($state['name'])) {
                                            return null;
                                        }

                                        $status = ($state['is_required'] ?? true) ? 'Wajib' : 'Opsional';

                                        return $state['name'].' — '.$status;
                                    })
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 12])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Nama Berkas')
                                                    ->placeholder('Foto KTP')
                                                    ->required()
                                                    ->maxLength(150)
                                                    ->columnSpan(['default' => 1, 'md' => 5]),
                                                TextInput::make('description')
                                                    ->label('Keterangan')
                                                    ->placeholder('Foto asli terbaca jelas')
                                                    ->maxLength(255)
                                                    ->columnSpan(['default' => 1, 'md' => 5]),
                                                Toggle::make('is_required')
                                                    ->label('Wajib')
                                                    ->default(true)
                                                    ->inline(false)
                                                    ->columnSpan(['default' => 1, 'md' => 2]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Surat TTE')
                            ->icon('heroicon-o-document-check')
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        Toggle::make('is_tte_enabled')
                                            ->label('Aktifkan TTE')
                                            ->helperText('Terbitkan PDF otomatis dengan TTE BSrE.')
                                            ->default(false)
                                            ->live()
                                            ->inline(false)
                                            ->columnSpan(1),
                                        Select::make('letter_preset')
                                            ->label('Preset Surat')
                                            ->placeholder('Pilih preset')
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
                                            ->helperText('Mengisi kode, judul, isi surat, dan kolom tambahan.')
                                            ->columnSpan(1),
                                    ]),

                                Grid::make(['default' => 1, 'md' => 3])
                                    ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                                    ->schema([
                                        TextInput::make('letter_code')
                                            ->label('Kode Surat')
                                            ->placeholder('470')
                                            ->maxLength(30)
                                            ->columnSpan(1),
                                        TextInput::make('letter_title')
                                            ->label('Judul Surat')
                                            ->placeholder('SURAT KETERANGAN DOMISILI')
                                            ->maxLength(180)
                                            ->columnSpan(['default' => 1, 'md' => 2]),
                                    ]),

                                RichEditor::make('letter_template')
                                    ->label('Isi Surat')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                                    ->columnSpanFull(),

                                Section::make('Variabel Placeholder')
                                    ->collapsible()
                                    ->collapsed()
                                    ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                                    ->schema([
                                        Placeholder::make('letter_placeholders_guide')
                                            ->hiddenLabel()
                                            ->content(function (): HtmlString {
                                                $items = [];
                                                foreach (LetterTemplatePresets::availablePlaceholders() as $token => $label) {
                                                    $items[] = "<code style='background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px;'>{$token}</code> <span style='font-size:11px;color:#64748b;'>{$label}</span>";
                                                }

                                                return new HtmlString('<div style="display:flex;flex-wrap:wrap;gap:6px 12px;">'.implode('', $items).'</div>');
                                            }),
                                    ])
                                    ->columnSpanFull(),

                                Repeater::make('form_fields')
                                    ->label('Kolom Tambahan')
                                    ->visible(fn (Get $get): bool => (bool) $get('is_tte_enabled'))
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Tambah Kolom')
                                    ->itemLabel(function (array $state): ?string {
                                        if (empty($state['label'])) {
                                            return null;
                                        }

                                        $status = ($state['is_required'] ?? true) ? 'Wajib' : 'Opsional';

                                        return $state['label'].' — '.$status;
                                    })
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Label')
                                                    ->placeholder('Nama Usaha')
                                                    ->required()
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (?string $state, Get $get, Set $set): void {
                                                        if (! $get('key') && $state) {
                                                            $set('key', Str::slug($state, '_'));
                                                        }
                                                    }),
                                                TextInput::make('key')
                                                    ->label('Variabel')
                                                    ->placeholder('nama_usaha')
                                                    ->required()
                                                    ->regex('/^[a-z0-9_]+$/'),
                                                Select::make('type')
                                                    ->label('Tipe')
                                                    ->options([
                                                        'text' => 'Teks Singkat',
                                                        'textarea' => 'Teks Panjang',
                                                        'date' => 'Tanggal',
                                                        'number' => 'Angka',
                                                    ])
                                                    ->default('text')
                                                    ->required(),
                                                Toggle::make('is_required')
                                                    ->label('Wajib')
                                                    ->default(true)
                                                    ->inline(false),
                                                TextInput::make('placeholder')
                                                    ->label('Contoh')
                                                    ->placeholder('Warung Sembako Berkah')
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
