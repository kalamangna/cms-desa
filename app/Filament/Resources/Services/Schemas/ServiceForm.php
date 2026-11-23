<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->placeholder('Surat keterangan resmi dari pemerintah desa untuk keperluan usaha warga.')
                            ->helperText('Kegunaan singkat layanan ini.')
                            ->required()
                            ->columnSpanFull(),
                        RichEditor::make('requirements')
                            ->label('Panduan dan Prosedur')
                            ->placeholder('Tuliskan tahapan pengajuan dan catatan umum layanan.')
                            ->helperText('Panduan umum layanan yang tampil pada halaman publik.')
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
            ]);
    }
}
