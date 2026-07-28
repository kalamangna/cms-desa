<?php

namespace App\Filament\Resources\Datasets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class DatasetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Informasi Utama & Sumber Data')
                    ->description('Judul dan sumber data dataset.')
                    ->columnSpanFull()
                    ->columns(12)
                    ->schema([
                        Select::make('source_table')
                            ->label('Sumber Data Utama')
                            ->placeholder('Pilih Sumber Data')
                            ->helperText('Pilih asal basis data.')
                            ->options([
                                'citizens' => 'Data Penduduk',
                                'families' => 'Data Keluarga',
                                'manual' => 'Upload Berkas Manual',
                            ])
                            ->default('citizens')
                            ->live()
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('title')->label('Judul Dataset')
                            ->placeholder('Contoh: Data Sebaran Pendidikan Warga Desa')
                            ->helperText('Nama resmi kelompok data terbuka.')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('year')->label('Tahun Data')
                            ->placeholder('Contoh: 2026')
                            ->helperText('Tahun data ini diterbitkan.')
                            ->default(2026)
                            ->readOnly()
                            ->dehydrated()
                            ->length(4)
                            ->required()
                            ->columnSpan(6),

                        TextInput::make('source')->label('Instansi / Sumber Data')
                            ->placeholder('Contoh: Pemerintah Desa')
                            ->helperText('Nama instansi penyedia/pemilik data.')
                            ->default('Pemerintah Desa')
                            ->columnSpan(6),

                        Textarea::make('description')->label('Deskripsi Ringkas Dataset')
                            ->placeholder('Jelaskan cakupan dan peruntukan dataset ini...')
                            ->helperText('Tujuan dan cakupan publikasi data.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pilih Kolom Data yang Ingin Dipublikasikan')
                    ->description('Pilih kolom yang dipublikasikan. Data rahasia (NIK/KK) otomatis terlindungi.')
                    ->columnSpanFull()
                    ->columns(12)
                    ->visible(fn ($get) => in_array($get('source_table'), ['citizens', 'families'], true))
                    ->schema([
                        CheckboxList::make('selected_columns')
                            ->label('Pilihan Kolom Publik')
                            ->helperText('Centang atribut aman untuk publik.')
                            ->columnSpanFull()
                            ->columns(3)
                            ->options(function ($get) {
                                $source = $get('source_table');
                                if ($source === 'citizens') {
                                    return [
                                        'gender' => 'Jenis Kelamin (Laki-laki / Perempuan)',
                                        'age' => 'Umur / Usia Penduduk',
                                        'marital_status' => 'Status Perkawinan (Kawin / Belum Kawin / Cerai)',
                                        'family_relation' => 'Hubungan Dalam Keluarga',
                                        'education_level' => 'Tingkat Pendidikan Terakhir',
                                        'school_participation' => 'Partisipasi Sekolah Anak',
                                        'job' => 'Pekerjaan / Profesi Utama',
                                        'job_status' => 'Kedudukan / Status Pekerjaan',
                                        'dusun' => 'Nama Wilayah Dusun',
                                        'rt_rw' => 'Nomor RT / RW',
                                        'bpjs_status' => 'Status Kepesertaan Jaminan Kesehatan (BPJS)',
                                        'pip_status' => 'Status Penerima Bantuan Pendidikan (PIP)',
                                        'has_digital_wallet' => 'Kepemilikan Dompet Digital (E-Wallet)',
                                        'disability_type' => 'Ragam Penyandang Disabilitas',
                                    ];
                                }

                                if ($source === 'families') {
                                    return [
                                        'dusun' => 'Nama Wilayah Dusun',
                                        'rt_rw' => 'Nomor RT / RW',
                                        'assistance_type' => 'Jenis Bantuan Sosial (PKH, BLT, BPNT, dll.)',
                                        'ownership_status' => 'Status Kepemilikan Rumah (Milik Sendiri / Sewa)',
                                        'house_condition' => 'Karakteristik Physical Hunian / Rumah',
                                        'water_source' => 'Sumber Air Bersih Utama',
                                        'sanitation_type' => 'Fasilitas Sanitasi / Jamban Keluarga',
                                        'electricity_power' => 'Penggunaan Daya Listrik (PLN)',
                                        'livestock' => 'Kepemilikan Aset Ternak',
                                    ];
                                }

                                return [];
                            })
                            ->default([]),
                    ]),

                Section::make('Berkas Unduhan Manual (Opsional)')
                    ->description('Isi hanya untuk tipe Upload Manual.')
                    ->columnSpanFull()
                    ->columns(12)
                    ->visible(fn ($get) => $get('source_table') === 'manual')
                    ->schema([
                        FileUpload::make('file_csv')->label('File CSV (Custom)')
                            ->helperText('Unggah berkas data CSV.')
                            ->directory('datasets/csv')
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->columnSpan(4),
                        FileUpload::make('file_xlsx')->label('File XLSX (Custom)')
                            ->helperText('Unggah berkas spreadsheet Excel.')
                            ->directory('datasets/xlsx')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->columnSpan(4),
                        FileUpload::make('file_pdf')->label('File PDF (Custom)')
                            ->helperText('Unggah dokumen cetak PDF.')
                            ->directory('datasets/pdf')
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpan(4),
                    ]),
            ]);
    }
}
